<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $admin_id 
 * @property string $name 
 * @property string $icon 
 * @property string $title 
 * @property int $sort 
 * @property int $status 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class ProductClassify extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_classify';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'name', 'icon', 'title', 'sort', 'status', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'admin_id' => 'integer', 'sort' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}