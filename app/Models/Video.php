<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $course_id 
 * @property int $chapter_id 
 * @property string $title 
 * @property string $intro 
 * @property string $filename 
 * @property int $material_id 
 * @property string $cover 
 * @property int $duration 
 * @property int $size 
 * @property int $status 
 * @property string $aliyun_video_id 
 * @property int $order 
 * @property string $seo_title 
 * @property string $seo_keywords 
 * @property string $seo_description 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Video extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'video';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'course_id', 'chapter_id', 'title', 'intro', 'filename', 'material_id', 'cover', 'duration', 'size', 'status', 'aliyun_video_id', 'order', 'seo_title', 'seo_keywords', 'seo_description', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'course_id' => 'integer', 'chapter_id' => 'integer', 'material_id' => 'integer', 'duration' => 'integer', 'size' => 'integer', 'status' => 'integer', 'order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}