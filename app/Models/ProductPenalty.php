<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $admin_id 
 * @property int $type 
 * @property string $province 
 * @property string $city 
 * @property string $district 
 * @property string $address
 * @property int $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class ProductPenalty extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_penalty';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'type', 'province', 'city', 'district', 'address', 'status',  'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'admin_id' => 'integer', 'type' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function getStatusAttribute() : bool
    {
        return $this->attributes['status'] = $this->attributes['status'] ? true : false;
    }

    public function getCreatedAtAttribute() : string
    {
        if('0000-00-00 00:00:00' === (string)$this->attributes['created_at']){
            return '-';
        }
        return (string)$this->attributes['created_at'];
    }

    public function getUpdatedAtAttribute() : string
    {
        if('0000-00-00 00:00:00' === (string)$this->attributes['updated_at']){
            return '-';
        }
        return (string)$this->attributes['updated_at'];
    }

}