<?php

namespace App\Service\Inv;

use App\Exception\ServiceException;
use App\Model\InvChannel;
use App\Model\InvItemSku;
use App\Model\InvItemSkuPrice;
use App\Model\InvPurchaseOrder;
use App\Request\Merchant\InvPurchaseOrderRequest;
use Hyperf\DbConnection\Db;

class PurchaseOrderService
{

    public function addPurchaseOrder($data): void
    {
        Db::transaction(function () use ($data) {
            //查询渠道是否存在
            if (!InvChannel::where('type', 0)->where('id', $data['channel_id'])->where('merchant_id', $data['merchant_id'])->exists()) {
                throw new ServiceException('进货渠道不存在');
            }
            $sku_ids = array_column($data['items'], 'sku_id');
            $exists = InvItemSku::whereIn('id', $sku_ids)->where('merchant_id', $data['merchant_id'])->pluck('id')->all();
            $missing = array_diff($sku_ids, $exists);
            if ($missing) {
                throw new ServiceException('以下SKU不存在: ' . implode(',', $missing));
            }
            //获取渠道价格，计算总价
            $existsPriceSkuIds = InvItemSkuPrice::whereIn('sku_id', $sku_ids)->where('channel_id', $data['channel_id'])->pluck('sku_id')->all();
            $missingPriceSkuIds = array_diff($sku_ids, $existsPriceSkuIds);
            if ($missingPriceSkuIds) {
                throw new ServiceException('以下SKU渠道价格不存在: ' . implode(',', $missingPriceSkuIds));
            }

//            $purchaseOrder = new InvPurchaseOrder();
//            $purchaseOrder->merchant_id = $data['merchant_id'];
//            $purchaseOrder->channel_id = $data['channel_id'];
//            $purchaseOrder->total_amount = 0;
//            $purchaseOrder->save();


        });
    }


}