<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property int $system_permission_id 
 * @property int $parent_id 
 * @property string $display_name 
 * @property string $icon 
 * @property string $url 
 * @property int $order 
 * @property string $additional 
 * @property string $description 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class SystemMenu extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_menu';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'system_permission_id', 'parent_id', 'display_name', 'icon', 'url', 'order', 'additional', 'description', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'system_permission_id' => 'integer', 'parent_id' => 'integer', 'order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getList
     * 获取系统菜单列表
     * User：YM
     * Date：2020/1/13
     * Time：上午12:08
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @return array
     */
    public function getList($where = [], $order = [])
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.system_permission_id', $this->table . '.parent_id', $this->table . '.display_name', $this->table . '.icon', $this->table . '.url', $this->table . '.order', 'system_permissions.name as permission_name');
        $query = $query->leftjoin('system_permissions', 'system_permissions.id', '=', $this->table . '.system_permission_id');
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
        $query = $query->get();
        return $query ? $query->toArray() : [];
    }
    /**
     * getMenuCount
     * 根据条件获取菜单的个数
     * User：YM
     * Date：2020/2/3
     * Time：下午4:54
     * @param array $where 查询条件
     * @return int
     */
    public function getMenuCount($where = [])
    {
        $query = $this->query();
        foreach ($where as $k => $v) {
            $query = $query->where($k, $v);
        }
        $query = $query->count();
        return $query > 0 ? $query : 0;
    }
}