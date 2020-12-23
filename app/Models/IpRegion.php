<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $pid 
 * @property string $name 
 * @property int $level 
 * @property string $code 
 * @property string $lng 
 * @property string $lat 
 */
class IpRegion extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ip_region';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'pid', 'name', 'level', 'code', 'lng', 'lat'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'pid' => 'integer', 'level' => 'integer'];
    /**
     * getList
     * 获取列表
     * User：YM
     * Date：2020/2/23
     * Time：下午1:35
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.pid', $this->table . '.name', $this->table . '.level', $this->table . '.code', $this->table . '.lng', $this->table . '.lat');
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