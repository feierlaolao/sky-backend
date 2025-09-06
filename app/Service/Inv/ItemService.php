<?php

namespace App\Service\Inv;

use App\Exception\ServiceException;
use App\Model\FileAttachment;
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
        return InvItemSpu::when(isset($data['sortBy']), fn(Builder $query) => $query->orderByDesc($data['sortBy']))
            ->when(isset($data['merchant_id']), fn(Builder $query) => $query->where('merchant_id', $data['merchant_id']))
            ->when(isset($data['category_id']), fn(Builder $query) => $query->where('category_id', $data['category_id']))
            ->when(isset($data['name']), fn(Builder $query) => $query->where('name', 'like', '%' . $data['name'] . '%'))
            ->with(['sku.price', 'category', 'brand'])
            ->paginate(perPage: $data['pageSize'] ?? 20, page: $data['current'] ?? 1);
    }


    public function getItemByMerchantIdAndId($merchant_id, $id)
    {
        return InvItemSpu::where('id', $id)
            ->where('merchant_id', $merchant_id)
            ->with('sku')
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

            $skus = [];
            foreach ($data['sku'] ?? [] as $value) {
                //查询barcode有没有被占用
                if (InvItemSku::where('barcode', $value['barcode'])->exists()){
                    throw new ServiceException('条形码已存在');
                }
                $sku = new InvItemSku();
                $sku->name = $value['name'];
                $sku->barcode = $value['barcode'];
                $sku->merchant_id = $data['merchant_id'];
                $sku->conversion_to_base = $value['conversion_to_base'];
                $skus[] = $sku;
            }
            $spu = new InvItemSpu();
            $spu->merchant_id = $data['merchant_id'];
            $spu->category_id = $data['category_id'];
            $spu->brand_id = $data['brand_id'] ?? null;
            $spu->name = $data['name'];
            $spu->save();
            $skus = $spu->sku()->saveMany($skus);
            //sku渠道价格目录
            foreach ($skus as $index => $temp){
                $skuPrices = [];
                foreach ($data['sku'][$index]['price'] ?? [] as $temp2){
                    $skuPrice = new InvItemSkuPrice();
                    $skuPrice->channel_id = $temp2['channel_id'];
                    $skuPrice->price = $temp2['price'];
                    $skuPrices[] = $skuPrice;
                }
                $temp->price()->saveMany($skuPrices);
            }
            //创建图片引用

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
            ->with('spu','price')
            ->first();
    }

}