<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property string $id 
 * @property string $attachment_id 
 * @property string $owner_type 
 * @property string $owner_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class FileUsage extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'file_usage';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'attachment_id', 'owner_type', 'owner_id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];
}
