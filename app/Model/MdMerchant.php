<?php

declare(strict_types=1);

namespace App\Model;


use Hyperf\Snowflake\Concern\Snowflake;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property string $id
 * @property string $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MdMerchant extends Model implements Authenticatable
{
    use Snowflake;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'md_merchant';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function getId()
    {
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return MdMerchant::find($key);
    }
}
