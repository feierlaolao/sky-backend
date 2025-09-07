<?php

declare(strict_types=1);

namespace App\Model;


use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Snowflake\Concern\Snowflake;

/**
 * @property string $id
 * @property string $merchant_id
 * @property string $category_id
 * @property string $brand_id
 * @property string $name
 * @property string $description
 * @property string $attrs
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read null|\Hyperf\Database\Model\Collection|InvItemSku[] $sku
 */
class InvItemSpu extends Model
{
    use Snowflake;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_item_spu';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'merchant_id', 'category_id', 'brand_id', 'name', 'description', 'attrs', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function sku(): HasMany
    {
        return $this->hasMany(InvItemSku::class, 'spu_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InvCategory::class, 'category_id', 'id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(InvBrand::class, 'brand_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(FileUsage::class, 'owner_id', 'id')->where('owner_type', 'spu');
    }

}
