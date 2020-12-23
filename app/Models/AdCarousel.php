<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $title 
 * @property int $c_type 
 * @property string $image 
 * @property string $url 
 * @property string $additional 
 * @property int $order 
 * @property string $bg_color 
 * @property int $is_new_win 
 * @property int $is_show 
 * @property string $description 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class AdCarousel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ad_carousel';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'title', 'c_type', 'image', 'url', 'additional', 'order', 'bg_color', 'is_new_win', 'is_show', 'description', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'c_type' => 'integer', 'order' => 'integer', 'is_new_win' => 'integer', 'is_show' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getList
     * 获取系统轮播图列表
     * 可以传入条件
     * User：YM
     * Date：2020/2/9
     * Time：下午6:13
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.title', $this->table . '.c_type', $this->table . '.image', $this->table . '.url', $this->table . '.bg_color', $this->table . '.is_new_win', $this->table . '.is_show', $this->table . '.description', $this->table . '.additional', $this->table . '.created_at');
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