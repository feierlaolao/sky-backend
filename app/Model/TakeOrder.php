<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property string $id 
 * @property string $user_id 
 * @property string $store_id 
 * @property string $status 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class TakeOrder extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'take_order';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'store_id', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
}
