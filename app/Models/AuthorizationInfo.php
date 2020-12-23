<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $user_id 
 * @property int $goods_type 
 * @property int $goods_id 
 * @property int $authorization_type 
 * @property int $authorization_status 
 * @property string $handle_user 
 * @property string $handle_time 
 * @property string $remarks 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class AuthorizationInfo extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'authorization_info';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'goods_type', 'goods_id', 'authorization_type', 'authorization_status', 'handle_user', 'handle_time', 'remarks', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'goods_type' => 'integer', 'goods_id' => 'integer', 'authorization_type' => 'integer', 'authorization_status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}