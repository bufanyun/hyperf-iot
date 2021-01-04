<?php

declare (strict_types=1);
namespace App\Models;

use Core\Common\Container\Redis;
use App\Constants\RedisCode;
use Hyperf\Di\Annotation\Inject;

/**
 * @property int $id
 * @property int $admin_id
 * @property string $name
 * @property string $icon
 * @property string $title
 * @property int $sort
 * @property int $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 */
class ProductClassify extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_classify';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'admin_id', 'name', 'icon', 'title', 'sort', 'status', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'admin_id' => 'integer', 'sort' => 'integer', 'status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * @Inject()
     * @var Redis
     */
    private $Redis;

    /**
     * 获取产品列表
     * getList
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/04 14:52
     */
    public function getList() : array
    {
        $key = RedisCode::CLASSIFY_LIST;
        if ($res = $this->Redis->get($key)) {
            return json_decode($res, true);
        }
        $res = $this->query()->where(['status' => 1])->orderBy($this->table .'.sort', 'orderBy')->get();
        $res = $res ? $res->toArray() : [];
        if ( ! empty($res)) {
            $this->Redis->set($key, json_encode($res), 3600);
        }
        return $res;
    }

}