<?php

declare (strict_types=1);
namespace App\Models;

use App\Constants\ProductOrderCode;

/**
 * @property int $id 
 * @property int $sid 
 * @property string $admin_id 
 * @property string $dock_order_id 
 * @property string $order_id 
 * @property string $pay_id 
 * @property int $pay_type 
 * @property int $pay_status 
 * @property string $pay_ip 
 * @property string $price 
 * @property int $status 
 * @property string $auto_msg
 * @property string $province 
 * @property string $city 
 * @property string $district 
 * @property string $address
 * @property string $name 
 * @property string $phone 
 * @property string $sim_identity 
 * @property string $sim_hold 
 * @property string $sim_just 
 * @property string $sim_back 
 * @property string $express 
 * @property string $express_no 
 * @property string $app_province 
 * @property string $app_city 
 * @property string $app_number 
 * @property int $activat_status 
 * @property int $sale_channel 
 * @property string $source 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class ProductOrder extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_order';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'sid', 'admin_id', 'dock_order_id', 'order_id', 'pay_id', 'pay_type', 'pay_status', 'pay_ip', 'price', 'status', 'auto_msg', 'province', 'city', 'district', 'address','name', 'phone', 'sim_identity', 'sim_hold', 'sim_just', 'sim_back', 'express', 'express_no', 'app_province', 'app_city', 'app_number', 'activat_status', 'sale_channel', 'source', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'sid' => 'integer', 'pay_type' => 'integer', 'pay_status' => 'integer', 'status' => 'integer', 'activat_status' => 'integer', 'sale_channel' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];




    public function product_sale()
    {
        return $this->hasOne(ProductSale::class, 'id', 'sid');
    }
}