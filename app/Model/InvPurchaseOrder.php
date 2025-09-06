<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property string $id 
 * @property string $merchant_id 
 * @property string $channel_id 
 * @property int $type 
 * @property string $total_amount 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class InvPurchaseOrder extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_purchase_order';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'merchant_id', 'channel_id', 'type', 'total_amount', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['type' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
