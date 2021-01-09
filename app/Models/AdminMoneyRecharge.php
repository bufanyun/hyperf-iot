<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $admin_id 
 * @property string $money 
 * @property string $orderid 
 * @property string $payid 
 * @property int $paytype 
 * @property int $paytime 
 * @property string $ip 
 * @property string $useragent 
 * @property int $status 
 * @property \Carbon\Carbon $created_at 
 */
class AdminMoneyRecharge extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_money_recharge';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'money', 'orderid', 'payid', 'paytype', 'paytime', 'ip', 'useragent', 'status', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'paytype' => 'integer', 'paytime' => 'integer', 'status' => 'integer', 'created_at' => 'datetime'];
}