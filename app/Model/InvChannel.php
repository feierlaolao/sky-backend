<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property string $id 
 * @property string $merchant_id 
 * @property int $type 
 * @property string $name 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class InvChannel extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'inv_channel';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'merchant_id', 'type', 'name', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['type' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
