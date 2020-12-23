<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $course_id 
 * @property int $lecturer_id 
 * @property string $title 
 * @property string $intro 
 * @property string $cover 
 * @property string $start_time 
 * @property string $end_time 
 * @property int $order 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Live extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'live';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'course_id', 'lecturer_id', 'title', 'intro', 'cover', 'start_time', 'end_time', 'order', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'course_id' => 'integer', 'lecturer_id' => 'integer', 'order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getList
     * 获取直播列表
     * User：YM
     * Date：2020/2/14
     * Time：下午11:57
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.lecturer_id', $this->table . '.course_id', $this->table . '.title', $this->table . '.intro', $this->table . '.cover', $this->table . '.order', $this->table . '.start_time', $this->table . '.end_time', $this->table . '.created_at');
        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($v || $v != null) {
                if ($k === 'title') {
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
        return $query && count($query) ? $query->toArray() : [];
    }
}