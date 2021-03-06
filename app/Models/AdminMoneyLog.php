<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $admin_id 
 * @property string $money 
 * @property string $before 
 * @property string $after 
 * @property string $memo 
 * @property \Carbon\Carbon $created_at 
 */
class AdminMoneyLog extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_money_log';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'money', 'before', 'after', 'memo', 'created_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'admin_id' => 'integer', 'created_at' => 'datetime'];

    protected $searchFields = ['memo'];


    /**
     * 添加明细记录
     * set
     * @param array $data
     *
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2021/01/17 21:39
     */
    public function set(array $data)
    {
        return $this->query()->insert($this->loadModel($data, null, false));
    }
}