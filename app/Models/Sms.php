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
class Sms extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sms';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'event', 'mobile', 'created_at', 'code', 'times', 'ip'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime'];

    const UPDATED_AT = null;

}