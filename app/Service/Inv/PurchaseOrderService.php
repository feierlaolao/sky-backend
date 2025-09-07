<?php

namespace App\Service\Inv;

use App\Exception\ServiceException;
use App\Model\InvChannel;
use App\Model\InvItemSku;
use App\Model\InvItemSkuPrice;
use App\Model\InvPurchaseOrder;
use App\Model\InvPurchaseOrderItem;
use App\Request\Merchant\InvPurchaseOrderRequest;
use Hyperf\DbConnection\Db;
use function Hyperf\Collection\collect;

class PurchaseOrderService
{

    public function addPurchaseOrder($data): void
    {
        Db::transaction(function () use ($data) {

            $items = $data['items'];

            //查询渠道是否存在
            if (!InvChannel::where('type', 0)->where('id', $data['channel_id'])->where('merchant_id', $data['merchant_id'])->exists()) {
                throw new ServiceException('进货渠道不存在');
            }

            //重复的合并sku
            $mergedItems = collect($items)->groupBy('sku_id')->map(fn($group) => [
                'sku_id' => $group->first()['sku_id'],
                'quantity' => $group->sum('quantity'),
            ])->values()->all();

            //查询sku
            $sku_ids = array_column($mergedItems, 'sku_id');
            $skus = InvItemSku::whereIn('id', $sku_ids)
                ->where('merchant_id', $data['merchant_id'])
                ->with(['price' => function ($query) use ($data) {
                    $query->where('channel_id', $data['channel_id']);
                }, 'parent', 'spu'])
                ->get();

            //判断sku_id是否存在
            $missing = array_diff($sku_ids, $skus->pluck('id')->all());
            if ($missing) {
                throw new ServiceException('以下SKU不存在: ' . implode(',', $missing));
            }

            foreach ($skus as $sku) {
                var_dump($sku->price);
                if (empty($sku->price)) {
                    throw new ServiceException('SKU渠道价格不存在:' . $sku->spu->name . '-' . $sku->name);
                }
            }

//            foreach ($mergedItems as $index => $item) {
//                $temp = new InvPurchaseOrderItem();
//                $temp->sku_id = $item['sku_id'];
//                $temp->unit_price = $existsPrice[$index]['price'];
//                $temp->total_price = $existsPrice[$index]['price'] * $item['quantity'];
//
//
//            }

//            $purchaseOrder = new InvPurchaseOrder();
//            $purchaseOrder->merchant_id = $data['merchant_id'];
//            $purchaseOrder->channel_id = $data['channel_id'];
//            $purchaseOrder->total_amount = 0;
//            $purchaseOrder->save();


        });
    }


}