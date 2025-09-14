<?php

namespace App\Service\Inv;

use App\Exception\ServiceException;
use App\Model\FileAttachment;
use App\Model\FileUsage;
use App\Model\InvBrand;
use App\Model\InvCategory;
use App\Model\InvChannel;
use App\Model\InvItemSku;
use App\Model\InvItemSkuPrice;
use App\Model\InvItemSpu;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;

class ItemService
{

    public function items($data): LengthAwarePaginatorInterface
    {
        return InvItemSpu::query()->where('merchant_id', $data['merchant_id'])
            ->when(isset($data['sort_by']), fn(Builder $query) => $query->orderByDesc($data['sort_by']))
            ->when(isset($data['merchant_id']), fn(Builder $query) => $query->where('merchant_id', $data['merchant_id']))
            ->when(!empty($data['category_id']), fn(Builder $query) => $query->where('category_id', $data['category_id']))
            ->when(isset($data['name']), fn(Builder $query) => $query->where('name', 'like', '%' . $data['name'] . '%'))
            ->with(['skus' => function ($query) {
                $query->whereNull('base_sku_id')->with(['prices', 'children']);
            }, 'category', 'brand', 'images.attachment'])
            ->paginate(perPage: $data['page_size'] ?? 20, page: $data['current'] ?? 1);
    }


    public function skuList($data): LengthAwarePaginatorInterface
    {
        return InvItemSpu::query()->where('merchant_id', $data['merchant_id'])
            ->when(!empty($data['name']), fn(Builder $query) => $query->where('name', 'like', '%' . $data['name'] . '%'))
            ->with(['skus' => function ($query) use ($data) {
                if (!empty($data['barcode'])){
                    $query->where('barcode', $data['barcode']);
                }
                $query->whereHas('prices')->with([
                    'prices' => function ($p) use ($data) {
                        if (!empty($data['channel_id'])) {
                            $p->where('channel_id', $data['channel_id']);
                        }
                    }
                ]);
            }])
            ->whereHas('skus')
            ->paginate(perPage: $data['page_size'] ?? 20, page: $data['current'] ?? 1);
    }

    public function getItemByMerchantIdAndId($merchant_id, $id)
    {
        return InvItemSpu::where('id', $id)
            ->where('merchant_id', $merchant_id)
            ->with(['skus.prices', 'skus.children', 'category', 'brand', 'images.attachment'])
            ->first();
    }

    public function addItem($data)
    {
        return Db::transaction(function () use ($data) {
            //查询产品名称是否被占用
            if (InvItemSpu::where('merchant_id', $data['merchant_id'])->where('name', $data['name'])->exists()) {
                throw new ServiceException('产品名称已存在');
            }
            //查询category_id是否合法
            if (!InvCategory::where('merchant_id', $data['merchant_id'])->where('id', $data['category_id'])->exists()) {
                throw new ServiceException('分类不存在');
            }
            //查询brand_id是否合法
            if (isset($data['brand_id']) && !InvBrand::where('merchant_id', $data['merchant_id'])->where('id', $data['brand_id'])->exists()) {
                throw new ServiceException('品牌不存在');
            }
            //检查图片ID是否合法
            $imageIds = array_values(array_unique($data['images'] ?? []));
            if ($imageIds) {
                // 按你的实际所有权字段来：merchant_id / owner_type+owner_id / uploader_user_id 等
                $validIds = FileAttachment::query()
                    ->whereIn('id', $imageIds)
                    ->where('upload_user_id', $data['merchant_id'])   // <<< 按你的表结构修改
                    ->pluck('id')
                    ->all();

                $diff = array_diff($imageIds, $validIds);
                if ($diff) {
                    throw new ServiceException('存在无效图片：' . implode(',', $diff));
                }
            }

            //增加spu
            $spu = new InvItemSpu();
            $spu->merchant_id = $data['merchant_id'];
            $spu->category_id = $data['category_id'];
            $spu->brand_id = $data['brand_id'] ?? null;
            $spu->name = $data['name'];
            $spu->description = $data['description'] ?? null;
            $spu->save();

            //创建图片引用
            $usages = [];
            foreach ($imageIds as $imageId) {
                $temp = new FileUsage();
                $temp->attachment_id = $imageId;
                $temp->owner_type = 'spu';
                $temp->owner_id = $spu->id;
                $usages[] = $temp;
            }
            $spu->images()->saveMany($usages);

            //递归保存
            $saveSku = function (array $row, $spu_id, $base_sku_id) use ($data, &$saveSku) {
                $sku = new InvItemSku();
                $sku->merchant_id = $data['merchant_id'];
                $sku->spu_id = $spu_id;
                $sku->base_sku_id = $base_sku_id;
                $sku->name = $row['name'];
                $sku->barcode = $row['barcode'] ?? null;
                $sku->conversion_to_base = $row['conversion_to_base'] ?? 1;
                $sku->save();
                foreach (($row['children'] ?? []) as $childRow) {
                    $saveSku($childRow, $spu_id, $sku->id);
                }
            };
            foreach ($data['skus'] ?? [] as $skuRow) {
                $saveSku($skuRow, $spu->id, null);
            }
            return $spu;
        });
    }


    public function updateItem($id, $data)
    {
        Db::transaction(function () use ($id, $data) {
            $spu = InvItemSpu::where('id', $id)
                ->where('merchant_id', $data['merchant_id'])
                ->first();
            if ($spu == null) {
                throw new ServiceException('产品不存在');
            }

            if ($spu->name != $data['name']) {
                //查询产品名称冲突
                if (InvItemSpu::where('merchant_id', $data['merchant_id'])->where('name', $data['name'])->exists()) {
                    throw new ServiceException('产品名称已存在');
                }

            }

            //查询category_id是否合法
            if (!InvCategory::where('merchant_id', $data['merchant_id'])->where('id', $data['category_id'])->exists()) {
                throw new ServiceException('分类不存在');
            }

            //查询brand_id是否合法
            if (isset($data['brand_id']) && !InvBrand::where('merchant_id', $data['merchant_id'])->where('id', $data['brand_id'])->exists()) {
                throw new ServiceException('品牌不存在');
            }

            $spu->name = $data['name'];
            $spu->category_id = $data['category_id'];
            $spu->brand_id = $data['brand_id'] ?? null;
            $spu->description = $data['description'] ?? null;
            $spu->save();


            $imageIds = array_values(array_unique($data['images'] ?? []));
            //查询当前的所有图片
            $currentUsages = FileUsage::where('owner_type', 'spu')->where('owner_id', $spu->id)
                ->pluck('attachment_id')->all();
            $toAdd = array_values(array_diff($imageIds, $currentUsages));
            $toRemove = array_values(array_diff($currentUsages, $imageIds));

            if ($toRemove) {
                FileUsage::query()
                    ->where('owner_type', 'spu')
                    ->where('owner_id', $spu->id)
                    ->whereIn('attachment_id', $toRemove)
                    ->delete();
            }

            if ($toAdd) {
                $rows = [];
                foreach ($toAdd as $aid) {
                    $rows[] = [
                        'attachment_id' => $aid,
                        'owner_type' => 'spu',
                        'owner_id' => $spu->id,
                    ];
                }
                FileUsage::query()->insert($rows);
            }

            //查询当前所有的skuId
//            $existsIds = InvItemSku::where('spu_id', $spu->id)->pluck('id')->all();

            $nowIds = [];
            //递归保存
            $saveSku = function (array $row, $spu_id, $base_sku_id) use ($data, &$saveSku, &$nowIds) {
                if (!empty($row['id'])) {
                    //更新
                    $sku = InvItemSku::where('id', $row['id'])->where('merchant_id', $data['merchant_id'])->where('spu_id', $spu_id)->first();
                    if ($sku == null) {
                        throw new ServiceException('SKU不存在');
                    }
                    $sku->name = $row['name'];
                    $sku->barcode = $row['barcode'] ?? null;
                    $sku->base_sku_id = $base_sku_id;
                    $sku->conversion_to_base = $row['conversion_to_base'] ?? 1;
                    $sku->save();
                } else {
                    //新增
                    $sku = new InvItemSku();
                    $sku->merchant_id = $data['merchant_id'];
                    $sku->spu_id = $spu_id;
                    $sku->base_sku_id = $base_sku_id;
                    $sku->name = $row['name'];
                    $sku->barcode = $row['barcode'] ?? null;
                    $sku->conversion_to_base = $row['conversion_to_base'] ?? 1;
                    $sku->save();
                }
                $nowIds[] = $sku->id;
                foreach (($row['children'] ?? []) as $childRow) {
                    $saveSku($childRow, $spu_id, $sku->id);
                }
            };
            foreach ($data['skus'] ?? [] as $skuRow) {
                $saveSku($skuRow, $spu->id, null);
            }
            //删除未出现的sku
            if (!empty($nowIds)) {
                InvItemSku::where('merchant_id', $data['merchant_id'])
                    ->where('spu_id', $spu->id)
                    ->whereNotIn('id', $nowIds)
                    ->delete();
            } else {
                //删除所有的sku
                InvItemSku::where('merchant_id', $data['merchant_id'])
                    ->where('spu_id', $spu->id)
                    ->delete();
            }
        });


    }


    public function skuPriceList($data): LengthAwarePaginatorInterface
    {
        return InvItemSkuPrice::when(isset($data['type']), function ($query) use ($data) {
            $query->where('type', $data['type']);
        })->when(isset($data['sku_id']), function ($query) use ($data) {
            $query->where('sku_id', $data['sku_id']);
        })->when(isset($data['channel_id']), function ($query) use ($data) {
            $query->where('channel_id', $data['channel_id']);
        })->when(isset($data['merchant_id']), function ($query) use ($data) {
            $query->where('merchant_id', $data['merchant_id']);
        })->paginate(perPage: $data['page_size'] ?? 20, page: $data['current'] ?? 1);
    }

    public function addSkuPrice($data): void
    {
        if (!InvItemSku::where('merchant_id', $data['merchant_id'])->where('id', $data['sku_id'])->exists()) {
            throw new ServiceException('SKU不存在');
        }
        $channel = InvChannel::where('merchant_id', $data['merchant_id'])->where('id', $data['channel_id'])->first();
        if ($channel == null) {
            throw new ServiceException('渠道不存在');
        }
        //一个渠道只能有一个价格
        if (InvItemSkuPrice::where('merchant_id', $data['merchant_id'])
            ->where('sku_id', $data['sku_id'])
            ->where('channel_id', $data['channel_id'])
            ->exists()) {
            throw new ServiceException('渠道价格已存在');
        }
        $invItemSkuPrice = new InvItemSkuPrice();
        $invItemSkuPrice->merchant_id = $data['merchant_id'];
        $invItemSkuPrice->sku_id = $data['sku_id'];
        $invItemSkuPrice->channel_id = $data['channel_id'];
        $invItemSkuPrice->type = $channel->type;
        $invItemSkuPrice->price = $data['price'];
        $invItemSkuPrice->save();
    }

    public function updateSkuPrice($id, $data): void
    {
        $invItemSkuPrice = InvItemSkuPrice::where('merchant_id', $data['merchant_id'])->where('id', $id)->first();
        if ($invItemSkuPrice == null) {
            throw new ServiceException('SKU不存在');
        }
        if ($data['channel_id'] != $invItemSkuPrice->channel_id) {
            $channel = InvChannel::where('merchant_id', $data['merchant_id'])->where('id', $data['channel_id'])->first();
            if ($channel == null) {
                throw new ServiceException('渠道不正确');
            }
            $invItemSkuPrice->channel_id = $channel->id;
            $invItemSkuPrice->type = $channel->type;
        }
        $invItemSkuPrice->price = $data['price'];
        $invItemSkuPrice->save();
    }

    public function getSkuPriceByMerchantIdAndId($merchantId, $id)
    {
        return InvItemSkuPrice::where('merchant_id', $merchantId)->where('id', $id)->first();
    }

    public function deleteSkuPrice($id): void
    {
        InvItemSkuPrice::where('id', $id)->delete();
    }

    public function getSkuByBarCode(string $barcode)
    {
        return InvItemSku::where('barcode', $barcode)
            ->with('spu', 'prices')
            ->first();
    }

}