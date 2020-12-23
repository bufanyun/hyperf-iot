<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $system_role_id 
 * @property int $system_permission_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class SystemRolesPermission extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_roles_permissions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['system_role_id', 'system_permission_id', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['system_role_id' => 'integer', 'system_permission_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getPermissionsByRolesIds
     * 获取角色对应的权限集合
     * 对应联查，将权限的分组父级查出来
     * User：YM
     * Date：2020/2/5
     * Time：上午11:06
     * @param int $ids
     * @return array
     */
    public function getPermissionsByRolesIds($ids = 0)
    {
        $query = $this->query()->select($this->table . '.system_role_id', $this->table . '.system_permission_id', 'system_permissions.parent_id');
        $query = $query->leftjoin('system_permissions', 'system_permissions.id', '=', $this->table . '.system_permission_id');
        if (is_array($ids)) {
            $query = $query->whereIn('system_role_id', $ids);
        } else {
            $query = $query->where('system_role_id', $ids);
        }
        $query = $query->get();
        return $query ? $query->toArray() : [];
    }
    /**
     * deleteRolesPermissions
     * 根据roleid删除对应的信息。
     * User：YM
     * Date：2020/2/5
     * Time：上午11:14
     * @param $id
     * @return mixed
     */
    public function deleteRolesPermissions($id)
    {
        $query = $this->query()->where('system_role_id', $id);
        $query = $query->delete();
        return $query;
    }
    /**
     * saveRolesPermissions
     * 保存角色权限，可以处理多维数组
     * User：YM
     * Date：2020/2/5
     * Time：上午11:13
     * @param $data
     * @return mixed
     */
    public function saveRolesPermissions($data)
    {
        $query = $this->insert($data);
        return $query;
    }
}