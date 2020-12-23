<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property string $id 
 * @property int $is_lecturer 
 * @property int $team_num 
 * @property string $token 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class LiveNeteaseUser extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'live_netease_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'is_lecturer', 'team_num', 'token', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['is_lecturer' => 'integer', 'team_num' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}