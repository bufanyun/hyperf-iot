<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $name 
 * @property string $title 
 * @property string $icon 
 * @property string $describes 
 * @property string $price 
 * @property int $auto_order_count 
 * @property int $auto_commission_count 
 * @property int $status 
 * @property int $sort 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class UserLevel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_level';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'title', 'icon', 'describes', 'price', 'auto_order_count', 'auto_commission_count', 'status', 'sort', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'auto_order_count' => 'integer', 'auto_commission_count' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}