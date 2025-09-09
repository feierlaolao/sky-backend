<?php

declare(strict_types=1);

namespace App\Model;


use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Snowflake\Concern\Snowflake;

/**
 * @property string $id
 * @property string $merchant_id
 * @property string $channel_id
 * @property string $total_amount
 * @property int $quantity
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class InvPurchaseOrder extends Model
{
    use Snowflake;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_purchase_order';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'merchant_id', 'channel_id', 'total_amount', 'quantity', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['quantity' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function items(): HasMany
    {
        return $this->hasMany(InvPurchaseOrderItem::class, 'order_id', 'id');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(InvChannel::class, 'channel_id', 'id');
    }
}
