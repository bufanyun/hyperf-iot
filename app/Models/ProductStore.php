<?php

declare (strict_types=1);
namespace App\Models;

/**
 */
class ProductStore extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_store';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'cid', 'access', 'name', 'titile', 'price', 'icon', 'recommend', 'stocks', 'sales', 'penalty', 'first_desc', 'sort', 'status', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'admin_id' => 'integer', 'cid' => 'integer', 'price' => 'float', 'recommend' => 'integer', 'stocks' => 'integer', 'sales' => 'integer', 'sort' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


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


    //修改时 更改储存格式或者值 【自动触发，无需调用】
    public function setStatusAttribute($value)
    {
        //$value 代表字段的值
//        $this->attributes['title'] = empty($value) ? '0' : $value;
    }
}