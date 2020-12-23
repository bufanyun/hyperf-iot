<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $video_id 
 * @property string $aliyun_video_id 
 * @property string $info 
 * @property string $type 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class VideoLog extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'video_logs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'video_id', 'aliyun_video_id', 'info', 'type', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'video_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}