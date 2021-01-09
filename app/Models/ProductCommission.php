<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $admin_id 
 * @property int $type 
 * @property int $month 
 * @property string $money 
 * @property string $detailed_titile 
 */
class ProductCommission extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_commission';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'type', 'month', 'money', 'detailed_titile'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'type' => 'integer', 'month' => 'integer'];
}