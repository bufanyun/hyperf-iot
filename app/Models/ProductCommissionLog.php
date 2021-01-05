<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $admin_id 
 * @property string $order_id 
 * @property int $type 
 * @property int $month 
 * @property string $money 
 * @property string $detailed_titile 
 * @property \Carbon\Carbon $created_at 
 */
class ProductCommissionLog extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_commission_log';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'order_id', 'type', 'month', 'money', 'detailed_titile', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'admin_id' => 'integer', 'type' => 'integer', 'month' => 'integer', 'created_at' => 'datetime'];
}