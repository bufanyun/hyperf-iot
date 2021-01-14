<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $event 
 * @property string $mobile 
 * @property string $code 
 * @property int $times 
 * @property string $ip 
 * @property \Carbon\Carbon $created_at 
 */
class Ems extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ems';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'event', 'mobile', 'code', 'times', 'ip', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'times' => 'integer', 'created_at' => 'datetime'];
}