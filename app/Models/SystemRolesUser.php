<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $system_role_id 
 * @property string $user_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class SystemRolesUser extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_roles_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['system_role_id', 'user_id', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['system_role_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getList
     * 获取列表
     * User：YM
     * Date：2020/2/4
     * Time：下午11:33
     * @param array $where
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getList($where = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.system_role_id', $this->table . '.user_id', $this->table . '.created_at');
        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($v || $v != null) {
                $query = $query->where($this->table . '.' . $k, $v);
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
     * deleteRolesUser
     * 根据规则删除对应信息
     * User：YM
     * Date：2020/2/5
     * Time：下午2:43
     * @param array $where
     * @return \Hyperf\Database\Model\Builder|int|mixed
     */
    public function deleteRolesUser($where = [])
    {
        $query = $this->query();
        foreach ($where as $k => $v) {
            $query = $query->where($this->table . '.' . $k, $v);
        }
        $query = $query->delete();
        return $query;
    }
    /**
     * saveUserRoles
     * 保存角色用户，可以处理多维数组
     * User：YM
     * Date：2020/2/5
     * Time：下午5:17
     * @param $data
     * @return mixed
     */
    public function saveUserRoles($data)
    {
        $query = $this->insert($data);
        return $query;
    }
}