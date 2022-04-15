<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public $table="customers";
    protected $guarded = [];
    protected $hidden = ["password"];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 username 字段为空
            if (!$model->username) {
                // 调用 findAvailableNo 生成username
                $model->username = static::findAvailableUsername();
                // 如果生成失败，则终止创建
                if (!$model->username) {
                    return false;
                }
            }
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class,"customer_id");
    }
    public function addresses()
    {
        return $this->hasMany(Address::class,"customer_id");
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class,"customer_id");
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class,"customer_id");
    }

    public static function findAvailableUsername()
    {
        // 生成Username
        $prefix = "m_".date('ymd');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $username = $prefix.str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('username', $username)->exists()) {
                return $username;
            }
        }
        return false;
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }
}