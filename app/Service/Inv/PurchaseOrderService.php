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
            ->paginate(perPage: $data['page_size'] ?? 20, page: $data['current'] ?? 1);
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


    public function deletePurchaseOrder($id)
    {
        //删除order和item，恢复库存
        Db::transaction(function () use ($id) {
           $order = InvPurchaseOrder::with('items.sku')->find($id);
           foreach ($order->items as $item) {
               $updateSkuId = $item->sku_id;
               if ($item->sku->base_sku_id != null){
                   $updateSkuId = $item->base_sku_id;
               }
               //恢复库存
               InvItemSku::where('id',$updateSkuId)->increment('stock_quantity',$item->base_quantity);
               InvPurchaseOrderItem::where('order_id',$id)->delete();
               InvPurchaseOrder::where('id', $id)->delete();
           }
        });
    }

    public function updatePurchaseOrder(int $id, $data)
    {
        //合并重复，
        //找到 增加/修改/删除 的sku
        //增加的进入正常流程，修改库存，修改进货价格
        //
        Db::transaction(function () use ($id, $data) {
            $order = InvPurchaseOrder::with('items')->where('id', $id)->where('merchant_id',$data['merchant_id'])->first();
            if ($order == null) {
                throw new ServiceException('订单不存在');
            }
            $nowItems = $data['items'];
            $mergedItems = collect($nowItems)->groupBy('sku_id')->map(fn($group) => [
                'id' => $group->firstWhere('id', '!=', null)['id'] ?? null,
                'sku_id' => $group->first()['sku_id'],
                'quantity' => $group->sum('quantity'),
            ])->values()->all();

            foreach ($mergedItems as $index => $item) {

            }

        });
    }



}