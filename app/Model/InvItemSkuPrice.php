<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Snowflake\Concern\Snowflake;

/**
 * @property string $id 
 * @property string $sku_id 
 * @property string $merchant_id 
 * @property int $type 
 * @property string $channel_id 
 * @property string $price 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property-read null|InvItemSku $sku 
 */
class InvItemSkuPrice extends Model
{
    use  Snowflake;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_item_sku_price';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'sku_id', 'merchant_id', 'type', 'channel_id', 'price', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['type' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function sku(): BelongsTo
    {
        return $this->belongsTo(InvItemSku::class, 'sku_id','id');
    }

}
