<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $user_id 
 * @property string $famous_nickname 
 * @property string $intro 
 * @property string $additional 
 * @property string $details 
 * @property string $image 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class Lecturer extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lecturer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'famous_nickname', 'intro', 'additional', 'details', 'image', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getCount
     * 重写父类的该方法，用于条件查询计算总数
     * User：YM
     * Date：2020/2/14
     * Time：下午11:42
     * @param array $where
     * @return int
     */
    public function getCount($where = [])
    {
        $query = $this;
        foreach ($where as $k => $v) {
            if ($k === 'famous_nickname') {
                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
                continue;
            }
            $query = $query->where($this->table . '.' . $k, $v);
        }
        $query = $query->count();
        return $query > 0 ? $query : 0;
    }
    /**
     * getList
     * 获取列表
     * User：YM
     * Date：2020/2/14
     * Time：下午11:42
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.user_id', $this->table . '.famous_nickname', $this->table . '.image', $this->table . '.intro', $this->table . '.created_at');
        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($v || $v != null) {
                if ($k === 'famous_nickname') {
                    $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
                    continue;
                }
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
     * Date：2020/2/14
     * Time：下午11:43
     * @param string $search 搜索条件
     * @param array $ids 讲师id集合
     * @param array $notIds 讲师id集合
     * @param int $limit
     * @return array
     */
    public function getSearchList($search = '', $ids = [], $notIds = [], $limit = 10)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.famous_nickname', $this->table . '.user_id', 'user.nickname', 'user.mobile');
        $query = $query->join('user', 'user.id', '=', $this->table . '.user_id');
        if ($search) {
            $query = $query->where(function ($queryS) use($search) {
                $queryS->where('user.username', 'like', "%{$search}%")->orWhere('user.mobile', 'like', "%{$search}%")->orWhere($this->table . '.famous_nickname', 'like', "%{$search}%");
            });
        }
        if ($ids) {
            $query = $query->whereIn($this->table . '.id', $ids);
        }
        if ($notIds) {
            $query = $query->whereNotIn($this->table . '.id', $notIds);
        }
        if ($limit) {
            $query = $query->limit($limit);
        }
        $query = $query->get();
        return $query ? $query->toArray() : [];
    }
}