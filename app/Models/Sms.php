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
 * @property int $createtime 
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
    protected $fillable = ['id', 'event', 'mobile', 'code', 'times', 'ip', 'createtime'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'times' => 'integer', 'createtime' => 'integer'];
}