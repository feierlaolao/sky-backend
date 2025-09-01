<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Snowflake\Concern\Snowflake;

/**
 * @property string $id 
 * @property string $merchant_id 
 * @property string $name 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class InvCategory extends Model
{
    use Snowflake;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_category';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'merchant_id', 'name', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
}
