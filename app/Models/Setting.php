<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $name 
 * @property string $value 
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
    protected $fillable = ['id', 'name', 'value', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getInfoByName
     * 通过name值取列表
     * User：YM
     * Date：2020/2/5
     * Time：下午8:55
     * @param $name
     * @return array
     */
    public function getInfoByName($name)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.name', $this->table . '.value', $this->table . '.created_at');
        $query = $query->where($this->table . '.name', $name);
        $query = $query->first();
        return $query ? $query->toArray() : [];
    }
    /**
     * getList
     * 通过name集合，取的相关列表数据
     * User：YM
     * Date：2020/2/5
     * Time：下午8:55
     * @param $nameArr
     * @return array
     */
    public function getList($nameArr)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.name', $this->table . '.value', $this->table . '.created_at');
        $query = $query->whereIn($this->table . '.name', $nameArr);
        $query = $query->get();
        return $query ? $query->toArray() : [];
    }
}