<?php

namespace App\Service;

use App\Exception\ServiceException;
use App\Model\FileAttachment;
use App\Model\InvBrand;
use App\Model\InvCategory;
use App\Model\InvItemSku;
use App\Model\InvItemSpu;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use function Symfony\Component\Translation\t;

class ItemService
{

    public function items($data): LengthAwarePaginatorInterface
    {
        return InvItemSpu::when(isset($data['sortBy']), fn(Builder $query) => $query->orderByDesc($data['sortBy']))
            ->when(isset($data['merchant_id']), fn(Builder $query) => $query->where('merchant_id', $data['merchant_id']))
            ->when(isset($data['name']), fn(Builder $query) => $query->where('name', 'like', '%' . $data['name'] . '%'))
            ->with(['sku', 'category', 'brand'])
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
                $sku = new InvItemSku();
                $sku->name = $value['name'];
                $sku->barcode = $value['barcode'];
                $sku->conversion_to_base = $value['conversion_to_base'];
                $skus[] = $sku;
            }
            $spu = new InvItemSpu();
            $spu->merchant_id = $data['merchant_id'];
            $spu->category_id = $data['category_id'];
            $spu->brand_id = $data['brand_id'] ?? null;
            $spu->name = $data['name'];
            $spu->save();
            $spu->sku()->saveMany($skus);
            //创建图片引用

            return $spu;
        });
    }


}