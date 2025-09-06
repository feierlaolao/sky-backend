<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property string $id 
 * @property string $order_id 
 * @property string $sku_id 
 * @property string $total_price 
 * @property string $unit_price 
 * @property int $quantity 
 * @property int $base_quantity 
 * @property int $base_unit_price 
 * @property string $expiry_date 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class InvPurchaseOrderItem extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_purchase_order_item';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'order_id', 'sku_id', 'total_price', 'unit_price', 'quantity', 'base_quantity', 'base_unit_price', 'expiry_date', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['quantity' => 'integer', 'base_quantity' => 'integer', 'base_unit_price' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
