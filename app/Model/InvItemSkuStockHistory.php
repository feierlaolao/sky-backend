<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property string $id 
 * @property string $merchant_id 
 * @property string $sku_id 
 * @property int $type 
 * @property int $before_stock 
 * @property int $after_stock 
 * @property int $change_stock 
 * @property string $description 
 * @property string $ref_type 
 * @property string $ref_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class InvItemSkuStockHistory extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_item_sku_stock_history';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'merchant_id', 'sku_id', 'type', 'before_stock', 'after_stock', 'change_stock', 'description', 'ref_type', 'ref_id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['type' => 'integer', 'before_stock' => 'integer', 'after_stock' => 'integer', 'change_stock' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
