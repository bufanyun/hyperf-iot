<?php

declare (strict_types=1);

namespace App\Models;

/**
 * @property int $id
 * @property string $name
 * @property string $group
 * @property string $title
 * @property string $tip
 * @property string $type
 * @property string $value
 * @property string $content
 * @property string $rule
 * @property string $extend
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Setting extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'group', 'title', 'tip', 'type', 'value', 'content', 'rule', 'extend', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


    /**
     * 获取分组配置
     * getGroupConfig
     * @param string $group
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/19 09:42
     */
    public function getGroupConfig(string $group): array
    {
        $query = $this->query()->select($this->table . '.name', $this->table . '.value');
        $query = $query->where(['group' => $group]);
        $query = $query->get();
        $query = $query ? $query->toArray() : [];
        $list  = [];
        foreach ($query as $k => $v) {
            $list = $list + [
                    $v['name'] => $v['value'],
                ];
        }
        unset($v);
        return $list;
    }

    /**
     * 通过name值取列表
     * getInfoByName
     * @param $name
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/19 09:39
     */
    public function getInfoByName(string $name)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.name', $this->table . '.value', $this->table . '.created_at');
        $query = $query->where($this->table . '.name', $name);
        $query = $query->first();
        return $query ? $query->toArray() : [];
    }

    /**
     * 通过name集合，取的相关列表数据
     * getList
     * @param $nameArr
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/19 09:39
     */
    public function getList(string $nameArr): array
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.name', $this->table . '.value', $this->table . '.created_at');
        $query = $query->whereIn($this->table . '.name', $nameArr);
        $query = $query->get();
        return $query ? $query->toArray() : [];
    }
}