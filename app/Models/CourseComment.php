<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $user_id 
 * @property int $course_id 
 * @property int $video_id 
 * @property string $content 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class CourseComment extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_comments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'course_id', 'video_id', 'content', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'course_id' => 'integer', 'video_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}