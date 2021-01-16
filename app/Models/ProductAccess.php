<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $admin_id 
 * @property string $label 
 * @property string $api_model 
 * @property string $kind 
 * @property string $remarks 
 * @property int $captcha_switch 
 * @property int $area_switch 
 * @property int $num_select_switch 
 * @property string $age_limit 
 * @property string $pay_limit 
 * @property string $check_url 
 * @property int $sort 
 * @property int $status 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class ProductAccess extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_access';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'label', 'api_model', 'kind', 'remarks', 'captcha_switch', 'area_switch', 'num_select_switch', 'age_limit', 'pay_limit', 'check_url', 'sort', 'status', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'admin_id' => 'integer', 'captcha_switch' => 'integer', 'area_switch' => 'integer', 'num_select_switch' => 'integer', 'sort' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}