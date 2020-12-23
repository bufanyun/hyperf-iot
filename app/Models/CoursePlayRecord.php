<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $user_id 
 * @property int $course_id 
 * @property int $video_id 
 * @property int $play_start 
 * @property int $play_end 
 * @property int $play_time 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class CoursePlayRecord extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_play_record';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'course_id', 'video_id', 'play_start', 'play_end', 'play_time', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'course_id' => 'integer', 'video_id' => 'integer', 'play_start' => 'integer', 'play_end' => 'integer', 'play_time' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}