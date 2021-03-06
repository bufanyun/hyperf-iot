<?php

declare (strict_types=1);

namespace App\Models;

/**
 * @property int $id
 * @property string $admin_id
 * @property int $type
 * @property int $month
 * @property string $money
 * @property string $amount_money
 * @property string $bind_products
 * @property \Carbon\Carbon $created_at
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
    protected $fillable = ['id', 'admin_id', 'type', 'month', 'money', 'detailed_titile', 'amount_money', 'created_at' , 'bind_products'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'type' => 'integer', 'month' => 'integer'];
    /**
     * 允许修改的字段名单
     * @var array
     */
    protected $editRoster = ['type', 'month', 'amount_money', 'money', 'detailed_titile'];

    const UPDATED_AT = null;

    public function getCreatedAtAttribute() : string
    {
        if ('0000-00-00 00:00:00' === (string) $this->attributes['created_at']) {
            return '-';
        }
        return (string) $this->attributes['created_at'];
    }

}