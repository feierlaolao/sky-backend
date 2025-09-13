<?php

namespace App\Service\Inv;

use App\Exception\ServiceException;
use App\Model\InvChannel;
use App\Model\InvItemSkuPrice;
use Hyperf\Database\Model\Builder;
use Hyperf\Paginator\LengthAwarePaginator;

class ChannelService
{

    public function getChannelList($data): LengthAwarePaginator
    {
        return InvChannel::when(isset($data['name']), fn(Builder $query) => $query->where('name', 'like', '%' . $data['name'] . '%'))
            ->when(isset($data['type']), fn(Builder $query) => $query->where('type', $data['type']))
            ->orderByDesc('created_at')
            ->paginate(perPage: $data['page_size'] ?? 20, page: $data['current'] ?? 1);
    }


    public function addChannel($data): InvChannel
    {
        if ($this->getChannelByMerchantAndName($data['merchant_id'], $data['name']) != null) {
            throw new ServiceException('分类已存在');
        }
        $channel = new InvChannel();
        $channel->merchant_id = $data['merchant_id'];
        $channel->name = $data['name'];
        $channel->type = $data['type'];
        $channel->save();
        return $channel;
    }

    public function getChannelByMerchantIdAndId(string $merchant_id, string $id): InvChannel
    {
        return InvChannel::where('id', $id)->where('merchant_id', $merchant_id)->first();
    }

    public function updateChannel($id, $data): void
    {
        $res = InvChannel::where('id', $id)->update($data);
        if ($res === 0) {
            throw new ServiceException('分类更新失败');
        }
    }

    public function deleteChannel($id): void
    {
//        $count = InvItemSpu::where('brand_id', $id)->count();
//        if ($count > 0) {
//            throw new ServiceException('品牌中有产品，不允许删除');
//        }
//        $res = InvBrand::where('id', $id)->delete();
//        if ($res === 0) {
//            throw new ServiceException('删除失败');
//        }
        //todo 需要先判断是否存在进出库订单
    }

    public function getChannelByMerchantAndName($merchant_id, $name)
    {
        return InvChannel::where('name', $name)
            ->where('merchant_id', $merchant_id)
            ->first();
    }

}