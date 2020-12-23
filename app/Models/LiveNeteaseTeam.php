<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $live_id 
 * @property string $tid 
 * @property string $accid 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class LiveNeteaseTeam extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'live_netease_team';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'live_id', 'tid', 'accid', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'live_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}