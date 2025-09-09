<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property string $id 
 * @property string $order_id 
 * @property string $product_id 
 * @property int $quantity 
 * @property string $unit_price 
 * @property string $total_price 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class TakeOrderItem extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'take_order_item';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['quantity' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
