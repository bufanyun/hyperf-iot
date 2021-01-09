<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $admin_id 
 * @property string $money 
 * @property string $fee 
 * @property string $last_money 
 * @property string $ip 
 * @property string $useragent 
 * @property int $status 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $remarks 
 */
class ProductCommissionCash extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_commission_cash';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'money', 'fee', 'last_money', 'ip', 'useragent', 'status', 'created_at', 'updated_at', 'remarks'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}