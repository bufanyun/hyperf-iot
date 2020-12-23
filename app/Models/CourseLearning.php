<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $user_id 
 * @property int $course_id 
 * @property int $video_id 
 * @property int $progress 
 * @property int $status 
 * @property \Carbon\Carbon $updated_at 
 * @property \Carbon\Carbon $created_at 
 */
class CourseLearning extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_learning';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'course_id', 'video_id', 'progress', 'status', 'updated_at', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'course_id' => 'integer', 'video_id' => 'integer', 'progress' => 'integer', 'status' => 'integer', 'updated_at' => 'datetime', 'created_at' => 'datetime'];
}