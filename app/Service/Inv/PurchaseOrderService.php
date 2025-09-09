<?php

namespace App\Service\Inv;

use App\Exception\ServiceException;
use App\Model\InvChannel;
use App\Model\InvItemSku;
use App\Model\InvPurchaseOrder;
use App\Model\InvPurchaseOrderItem;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\DbConnection\Db;
use function Hyperf\Collection\collect;

class PurchaseOrderService
{


    public function purchaseOrderList($data): LengthAwarePaginatorInterface
    {
        return InvPurchaseOrder::where('merchant_id', $data['merchant_id'])
            ->with(['items.sku.spu.images','channel'])
            ->paginate(perPage: $data['pageSize'] ?? 20, page: $data['current'] ?? 1);
    }

    public function getPurchaseOrderByMerchantIdAndId($merchant,$id)
    {
        return InvPurchaseOrder::where('merchant_id',$merchant)->where('id',$id)
            ->with(['items.sku.spu.images','channel'])
            ->first();
    }

    /**
     * 1.检查sku合法性（整理合并重复sku）
     * 2.获取单价总价，当前成本和利润
     * 3.插入订单，减少库存
     * @param $data
     * @return void
     */
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
                ->with(['price', 'parent', 'spu'])
                ->get();
            //判断sku_id是否存在
            $missing = array_diff($sku_ids, $skus->pluck('id')->all());
            if ($missing) {
                throw new ServiceException('以下SKU不存在: ' . implode(',', $missing));
            }

            $purchaseOrder = new InvPurchaseOrder();
            $purchaseOrder->merchant_id = $data['merchant_id'];
            $purchaseOrder->channel_id = $data['channel_id'];

            $purchaseOrderItems = [];
            $totalAmount = 0;
            $quantity = 0;
            $eSkus = $skus->keyBy('id')->toArray();
            foreach ($mergedItems as $index => $item) {
                $tempItem = new InvPurchaseOrderItem();
                $tempItem->sku_id = $item['sku_id'];
                $tempItem->quantity = $item['quantity'];

                $nowSku = $eSkus[$item['sku_id']];
                if (empty($channelPrice = array_filter($nowSku['price'], function ($temp) use ($data) {
                    return $temp['channel_id'] == $data['channel_id'];
                }))) {
                    throw new ServiceException('渠道价格不存在');
                }
                $tempItem->unit_price = $channelPrice[0]['price'];
                $tempItem->total_price = $channelPrice[0]['price'] * $item['quantity'];
                $totalAmount += $channelPrice[0]['price'] * $item['quantity'];
                $quantity += $item['quantity'];
                $base_sku_id = $item['sku_id'];
                if ($nowSku['base_sku_id'] == null) {//是上级
                    $tempItem->base_quantity = $item['quantity'];
                    $tempItem->base_unit_price = $channelPrice[0]['price'];
                    $stock_quantity = $nowSku['stock_quantity'] + $item['quantity'];
                    $cost_price = ($nowSku['stock_quantity'] * $nowSku['cost_price'] + $item['quantity'] * $channelPrice[0]['price']) / $stock_quantity;

                } else {
                    $tempItem->base_quantity = $item['quantity'] * $nowSku['conversion_to_base'];
                    $tempItem->base_unit_price = $channelPrice[0]['price'] / $nowSku['conversion_to_base'];
                    $parentSku = $nowSku['parent'];

                    $stock_quantity = $parentSku['stock_quantity'] + $item['quantity'] * $nowSku['conversion_to_base'];
                    $cost_price = ($parentSku['stock_quantity'] * $parentSku['cost_price'] + $item['quantity'] * $channelPrice[0]['price']) / $stock_quantity;
                    $base_sku_id = $parentSku['id'];
                }
                $purchaseOrderItems[] = $tempItem;
                //改变库存和成本
                InvItemSku::where('id', $base_sku_id)->update([
                    'stock_quantity' => $stock_quantity,
                    'cost_price' => $cost_price,
                ]);
            }

            $purchaseOrder->total_amount = $totalAmount;
            $purchaseOrder->quantity = $quantity;
            $purchaseOrder->save();
            $purchaseOrder->items()->saveMany($purchaseOrderItems);

        });
    }


}