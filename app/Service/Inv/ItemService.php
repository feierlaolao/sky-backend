<?php

namespace App\Service\Inv;

use App\Exception\ServiceException;
use App\Model\FileAttachment;
use App\Model\FileUsage;
use App\Model\InvBrand;
use App\Model\InvCategory;
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
        return InvItemSpu::query()->when(isset($data['sortBy']), fn(Builder $query) => $query->orderByDesc($data['sortBy']))
            ->when(isset($data['merchant_id']), fn(Builder $query) => $query->where('merchant_id', $data['merchant_id']))
            ->when(isset($data['category_id']), fn(Builder $query) => $query->where('category_id', $data['category_id']))
            ->when(isset($data['name']), fn(Builder $query) => $query->where('name', 'like', '%' . $data['name'] . '%'))
            ->with(['skus.price', 'skus.children', 'category', 'brand', 'images.attachment'])
            ->paginate(perPage: $data['pageSize'] ?? 20, page: $data['current'] ?? 1);
    }


    public function getItemByMerchantIdAndId($merchant_id, $id)
    {
        return InvItemSpu::where('id', $id)
            ->where('merchant_id', $merchant_id)
            ->with(['skus.price', 'skus.children', 'category', 'brand', 'images.attachment'])
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


    public function updateItem($data)
    {
        InvItemSpu::where('id', $data['id'])->where('merchant_id', $data['merchant_id'])->exists();
    }


    public function getSkuByBarCode(string $barcode)
    {
        return InvItemSku::where('barcode', $barcode)
            ->with('spu', 'price')
            ->first();
    }

}