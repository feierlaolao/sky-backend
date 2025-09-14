<?php

declare(strict_types=1);

namespace App\Model;


use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Snowflake\Concern\Snowflake;

/**
 * @property string $id 
 * @property string $merchant_id 
 * @property string $spu_id 
 * @property string $name 
 * @property string $barcode 
 * @property string $base_sku_id 
 * @property int $conversion_to_base 
 * @property string $attrs 
 * @property string $cost_price 
 * @property int $stock_quantity 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property-read null|InvItemSpu $spu 
 * @property-read null|\Hyperf\Database\Model\Collection|InvItemSkuPrice[] $price 
 * @property-read null|self $parent 
 * @property-read null|\Hyperf\Database\Model\Collection|self[] $children 
 */
class InvItemSku extends Model
{
    use Snowflake;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_item_sku';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'merchant_id', 'spu_id', 'name', 'barcode', 'base_sku_id', 'conversion_to_base', 'attrs', 'cost_price', 'stock_quantity', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['conversion_to_base' => 'integer', 'stock_quantity' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function spu(): BelongsTo
    {
        return $this->belongsTo(InvItemSpu::class, 'spu_id', 'id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(InvItemSkuPrice::class, 'sku_id', 'id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'base_sku_id','id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'base_sku_id','id');
    }

}
