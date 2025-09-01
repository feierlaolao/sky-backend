<?php

declare(strict_types=1);

namespace App\Model;


use Hyperf\Snowflake\Concern\Snowflake;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property string $id
 * @property string $username
 * @property string $password
 * @property int $phone_area_code
 * @property string $phone
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MdUser extends Model implements Authenticatable
{
    use Snowflake;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'md_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'username', 'password', 'phone_area_code', 'phone', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['phone_area_code' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function getId()
    {
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return MdUser::find($key);
    }
}
