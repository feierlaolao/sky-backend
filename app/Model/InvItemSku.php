<?php

declare(strict_types=1);

namespace App\Model;


use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Snowflake\Concern\Snowflake;

/**
 * @property string $id
 * @property string $spu_id
 * @property string $name
 * @property string $barcode
 * @property string $base_sku_id
 * @property int $conversion_to_base
 * @property string $attrs
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
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
    protected array $fillable = ['id', 'spu_id', 'name', 'barcode', 'base_sku_id', 'conversion_to_base', 'attrs', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['conversion_to_base' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function spu(): BelongsTo
    {
        return $this->belongsTo(InvItemSpu::class, 'spu_id', 'id');
    }

    public function price(): HasMany
    {
        return $this->hasMany(InvItemSkuPrice::class, 'sku_id', 'id');
    }
}
