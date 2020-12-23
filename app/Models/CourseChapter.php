<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $course_id 
 * @property string $title 
 * @property string $intro 
 * @property int $attach_id 
 * @property int $order 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class CourseChapter extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_chapter';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'course_id', 'title', 'intro', 'attach_id', 'order', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'course_id' => 'integer', 'attach_id' => 'integer', 'order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}