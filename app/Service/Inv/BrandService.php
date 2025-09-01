<?php

namespace App\Service\Inv;

use App\Exception\ServiceException;
use App\Model\InvBrand;
use App\Model\InvCategory;
use App\Model\InvItemSpu;
use Hyperf\Database\Model\Builder;

class BrandService
{

    public function getBrandList($data)
    {
        return InvBrand::when(isset($data['name']), fn(Builder $query) => $query->where('name', 'like', '%' . $data['name'] . '%'))
            ->orderByDesc('created_at')
            ->paginate(perPage: $data['pageSize'] ?? 20, page: $data['current'] ?? 1);
    }


    public function addBrand($data): InvBrand
    {
        if ($this->getBrandByMerchantIdAndId($data['merchant_id'], $data['name']) != null) {
            throw new ServiceException('分类已存在');
        }
        $brand = new InvBrand();
        $brand->merchant_id = $data['merchant_id'];
        $brand->name = $data['name'];
        $brand->save();
        return $brand;
    }

    public function getBrandByMerchantIdAndId(string $merchant_id, string $id): InvBrand
    {
        return InvBrand::where('id', $id)->where('merchant_id', $merchant_id)->first();
    }

    public function updateBrand($id, $data)
    {
        $res = InvBrand::where('id', $id)->update($data);
        if ($res === 0) {
            throw new ServiceException('分类更新失败');
        }
    }

    public function deleteBrand($id): void
    {
        $count = InvItemSpu::where('brand_id', $id)->count();
        if ($count > 0) {
            throw new ServiceException('品牌中有产品，不允许删除');
        }
        $res = InvBrand::where('id', $id)->delete();
        if ($res === 0) {
            throw new ServiceException('删除失败');
        }
    }

    public function getBrandByMerchantAndName($merchant_id, $name)
    {
        return InvBrand::where('name', $name)
            ->where('merchant_id', $merchant_id)
            ->first();
    }

}