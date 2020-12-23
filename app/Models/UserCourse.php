<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $user_id 
 * @property int $course_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class UserCourse extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_course';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'course_id', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'course_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}