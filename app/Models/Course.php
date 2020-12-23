<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $user_id 
 * @property string $title 
 * @property int $category_id 
 * @property string $category_ids 
 * @property string $label_ids 
 * @property string $cover_pc 
 * @property string $intro 
 * @property int $video_intro 
 * @property string $pc_detail_intro 
 * @property int $attach_id 
 * @property float $price 
 * @property int $type 
 * @property int $serial 
 * @property string $serial_time 
 * @property int $is_free 
 * @property int $vip_free 
 * @property int $status 
 * @property int $video_nums 
 * @property int $duration 
 * @property int $comment_nums 
 * @property int $follow_nums 
 * @property int $learning_nums 
 * @property int $play_nums 
 * @property int $order 
 * @property string $seo_title 
 * @property string $seo_keywords 
 * @property string $seo_description 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Course extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'title', 'category_id', 'category_ids', 'label_ids', 'cover_pc', 'intro', 'video_intro', 'pc_detail_intro', 'attach_id', 'price', 'type', 'serial', 'serial_time', 'is_free', 'vip_free', 'status', 'video_nums', 'duration', 'comment_nums', 'follow_nums', 'learning_nums', 'play_nums', 'order', 'seo_title', 'seo_keywords', 'seo_description', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'category_id' => 'integer', 'video_intro' => 'integer', 'attach_id' => 'integer', 'price' => 'float', 'type' => 'integer', 'serial' => 'integer', 'is_free' => 'integer', 'vip_free' => 'integer', 'status' => 'integer', 'video_nums' => 'integer', 'duration' => 'integer', 'comment_nums' => 'integer', 'follow_nums' => 'integer', 'learning_nums' => 'integer', 'play_nums' => 'integer', 'order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}