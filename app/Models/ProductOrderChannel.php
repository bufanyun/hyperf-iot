<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $admin_id 
 * @property string $name 
 * @property string $remarks 
 * @property int $status 
 * @property int $sort 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class ProductOrderChannel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_order_channel';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'name', 'remarks', 'status', 'sort', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'status' => 'integer', 'sort' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected $searchFields = ['id', 'name', 'remarks'];
    
    public function getStatusAttribute() : bool
    {
        return $this->attributes['status'] = $this->attributes['status'] ? true : false;
    }

}