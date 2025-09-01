<?php

declare(strict_types=1);

namespace App\Model;



use Hyperf\Snowflake\Concern\Snowflake;

/**
 * @property string $id 
 * @property string $upload_user_id 
 * @property string $bucket 
 * @property string $object_key 
 * @property string $mime 
 * @property int $size_bytes 
 * @property string $sha256 
 * @property int $width 
 * @property int $height 
 * @property int $status 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class FileAttachment extends Model
{

    use Snowflake;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'file_attachment';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'upload_user_id', 'bucket', 'object_key', 'mime', 'size_bytes', 'sha256', 'width', 'height', 'status', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['size_bytes' => 'integer', 'width' => 'integer', 'height' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
