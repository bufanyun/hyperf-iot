<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property string $id 
 * @property string $mobile 
 * @property string $username 
 * @property string $email 
 * @property string $nickname 
 * @property int $avatar 
 * @property string $job_number 
 * @property string $session_id 
 * @property string $password 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class User extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'status', 'mobile', 'username', 'email', 'nickname', 'avatar', 'job_number', 'session_id', 'password', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['avatar' => 'string', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * 主键id类型为字符串
     *
     * @var string
     */
    protected $keyType = 'string';
    /**
     * 允许快捷搜索的字段
     *
     * @var array
     */
    protected $searchFields = ['id', 'mobile', 'username'];
    /**
     * getList
     * 获取系统用户列表
     *
     * 可以传入条件
     * User：YM
     * Date：2020/2/5
     * Time：下午4:11
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.mobile', $this->table . '.username', $this->table . '.email', $this->table . '.nickname', $this->table . '.job_number', $this->table . '.created_at');
        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($v || $v != null) {
                $query = $query->where($this->table . '.' . $k, $v);
            }
        }
        // 追加排序
        if ($order && is_array($order)) {
            foreach ($order as $k => $v) {
                $query = $query->orderBy($this->table . '.' . $k, $v);
            }
        }
        // 是否分页
        if ($limit) {
            $query = $query->offset($offset)->limit($limit);
        }
        $query = $query->get();
        return $query ? $query->toArray() : [];
    }
    /**
     * getSearchList
     * 根据搜索条件返回list
     * User：YM
     * Date：2020/2/5
     * Time：下午2:28
     * @param string $search
     * @param array $userIds
     * @param array $notIds
     * @param int $limit
     * @return array
     */
    public function getSearchList($search = '', $userIds = [], $notIds = [], $limit = 10)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.username', $this->table . '.nickname', $this->table . '.mobile', $this->table . '.email');
        if ($search) {
            $query = $query->where(function ($queryS) use($search) {
                $queryS->where('username', 'like', "%{$search}%")->orWhere('mobile', 'like', "%{$search}%");
            });
        }
        if ($userIds) {
            $query = $query->whereIn('id', $userIds);
        }
        if ($notIds) {
            $query = $query->whereNotIn('id', $notIds);
        }
        if ($limit) {
            $query = $query->limit($limit);
        }
        $query = $query->get();
        return $query && count($query) ? $query->toArray() : [];
    }
}