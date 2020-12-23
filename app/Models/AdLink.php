<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $title 
 * @property int $c_type 
 * @property string $image 
 * @property string $url 
 * @property int $order 
 * @property int $is_show 
 * @property string $target 
 * @property string $additional 
 * @property string $description 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class AdLink extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ad_link';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'title', 'c_type', 'image', 'url', 'order', 'is_show', 'target', 'additional', 'description', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'c_type' => 'integer', 'order' => 'integer', 'is_show' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getList
     * 获取系统友情链接列表
     * User：YM
     * Date：2020/2/10
     * Time：下午10:36
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.title', $this->table . '.image', $this->table . '.url', $this->table . '.is_show', $this->table . '.c_type', $this->table . '.target', $this->table . '.description', $this->table . '.created_at');
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
}