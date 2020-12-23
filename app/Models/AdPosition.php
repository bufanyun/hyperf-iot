<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $title 
 * @property int $c_type 
 * @property string $unique_identify 
 * @property string $image 
 * @property int $video_id 
 * @property string $url 
 * @property int $order 
 * @property int $is_show 
 * @property string $additional 
 * @property string $description 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class AdPosition extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ad_position';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'title', 'c_type', 'unique_identify', 'image', 'video_id', 'url', 'order', 'is_show', 'additional', 'description', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'c_type' => 'integer', 'video_id' => 'integer', 'order' => 'integer', 'is_show' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getList
     * 获取列表
     * 可以传入条件
     * User：YM
     * Date：2020/2/10
     * Time：下午5:26
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.title', $this->table . '.unique_identify', $this->table . '.image', $this->table . '.video_id', $this->table . '.c_type', $this->table . '.url', $this->table . '.is_show', $this->table . '.description', $this->table . '.created_at');
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
     * getListByIdentify
     * 模糊匹配唯一标识获取list
     * 协定只模糊匹配内容后半段
     * User：YM
     * Date：2020/2/10
     * Time：下午5:28
     * @param string $identify
     * @return array
     */
    public function getListByIdentify($identify = '')
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.title', $this->table . '.unique_identify', $this->table . '.image', $this->table . '.video_id', $this->table . '.c_type', $this->table . '.url', $this->table . '.is_show', $this->table . '.additional_field', $this->table . '.description', $this->table . '.created_at');
        if ($identify) {
            $query = $query->where($this->table . '.unique_identify', 'like', "{$identify}%");
        }
        $query = $query->get();
        return $query ? $query->toArray() : [];
    }
}