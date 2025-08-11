<?php

declare(strict_types=1);

namespace App\Model;


use Hyperf\Database\Model\Relations\HasMany;

/**
 * @property string $id
 * @property string $merchant_id
 * @property string $name
 * @property string $attrs
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class InvItemSpu extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_item_spu';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'merchant_id', 'name', 'attrs', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function sku(): HasMany
    {
        return $this->hasMany(InvItemSku::class, 'spu_id', 'id');
    }
}
