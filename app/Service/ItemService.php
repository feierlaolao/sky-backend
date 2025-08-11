<?php

namespace App\Service;

use App\Exception\ServiceException;
use App\Model\InvItemSpu;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;

class ItemService
{

    public function items($data): LengthAwarePaginatorInterface
    {
        return InvItemSpu::when(isset($data['sortBy']), fn(Builder $query) => $query->orderByDesc($data['sortBy']))
            ->when(isset($data['name']), fn(Builder $query) => $query->where('name', 'like', '%' . $data['name'] . '%'))
            ->with('sku')
            ->paginate(perPage: $data['pageSize'] ?? 20, page: $data['current'] ?? 1);
    }


    public function item($id)
    {
        try {
            return InvItemSpu::where('id', $id)->firstOrFail();
        } catch (\Exception $exception) {
            throw new ServiceException('产品不存在');
        }
    }


}