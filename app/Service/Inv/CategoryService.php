<?php

namespace App\Service\Inv;

use App\Exception\ServiceException;
use App\Model\InvCategory;
use App\Model\InvItemSpu;
use Hyperf\Database\Model\Builder;
use Hyperf\Paginator\LengthAwarePaginator;

class CategoryService
{

    public function getCategoryList($data): LengthAwarePaginator
    {
        return InvCategory::when(isset($data['name']), fn(Builder $query) => $query->where('name', 'like', '%' . $data['name'] . '%'))
            ->when(isset($data['merchant_id']), fn(Builder $query) => $query->where('merchant_id', $data['merchant_id']))
            ->orderByDesc('created_at')
            ->paginate(perPage: $data['pageSize'] ?? 20, page: $data['current'] ?? 1);
    }

    public function addCategory($data): InvCategory
    {
        if ($this->getCategoryByMerchantAndName($data['merchant_id'], $data['name']) != null) {
            throw new ServiceException('分类已存在');
        }
        $category = new InvCategory();
        $category->merchant_id = $data['merchant_id'];
        $category->name = $data['name'];
        $category->save();
        return $category;
    }

    public function getCategoryByMerchantIdAndId(string $merchant_id, string $id): InvCategory
    {
        return InvCategory::where('id', $id)->where('merchant_id', $merchant_id)->first();
    }

    public function updateCategory($id, $data)
    {
        $res = InvCategory::where('id', $id)->update($data);
        if ($res === 0) {
            throw new ServiceException('分类更新失败');
        }
    }

    public function deleteCategory($id): void
    {
        $count = InvItemSpu::where('category_id', $id)->count();
        if ($count > 0) {
            throw new ServiceException('分类中有产品，不允许删除');
        }
        $res = InvCategory::where('id', $id)->delete();
        if ($res === 0) {
            throw new ServiceException('删除失败');
        }
    }

    public function getCategoryByMerchantAndName($merchant_id, $name)
    {
        return InvCategory::where('name', $name)
            ->where('merchant_id', $merchant_id)
            ->first();
    }

}