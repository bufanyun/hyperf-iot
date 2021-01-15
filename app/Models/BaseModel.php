<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Models;

use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;
use Core\Common\Traits\Admin\Models\Table;

abstract class BaseModel extends Model implements CacheableInterface
{
    /**
     * The name of the "deleted_at" column.
     *
     * @var string
     */
    const DELETED_AT = 'deleted_at';

    /**可操作的开关字段白名单
     *
     * @var array
     */
    protected $switchList = ['status',];

    use Cacheable;
    use Table;

    /**
     * getInfo
     * 通过主键id/ids获取信息
     * User：YM
     * Date：2020/1/8
     * Time：下午5:55
     * @param $id
     * @param bool $useCache 是否使用模型缓存
     * @return BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id,$useCache = true)
    {
        $instance = make(get_called_class());

        if ($useCache === true) {
            $modelCache = is_array($id)?$instance->findManyFromCache($id):$instance->findFromCache($id);
            return isset($modelCache) && $modelCache ? $modelCache->toArray() : [];
        }

        $query = $instance->query()->find($id);
        return $query ? $query->toArray() : [];
    }

    /**
     * saveInfo
     * 创建/修改记录
     * User：YM
     * Date：2020/1/8
     * Time：下午7:49
     * @param $data 保存数据
     * @param bool $type 是否强制写入，适用于主键是规则生成情况
     * @return null
     */
    public function saveInfo($data,$type=false)
    {
        $id = null;
        $instance = make(get_called_class());
        if (isset($data['id']) && $data['id'] && !$type) {
            $id = $data['id'];
            unset($data['id']);
            $query = $instance->query()->find($id);
            foreach ($data as $k => $v) {
                $query->$k = $v;
            }
            $query->save();
        } else {
            foreach ($data as $k => $v) {
                if ($k === 'id') {
                    $id = $v;
                }
                $instance->$k = $v;
            }
            $instance->save();
            if (!$id) {
                $id = $instance->id;
            }
        }

        return $id;
    }

    /**
     * getInfoByWhere
     * 根据条件获取结果
     * User：YM
     * Date：2020/1/9
     * Time：下午10:24
     * @param $where
     * @param bool $type 是否查询多条
     * @return array
     */
    public function getInfoByWhere($where,$type=false)
    {
        $instance = make(get_called_class());
        foreach ($where as $k => $v) {
            $instance = is_array($v)?$instance->where($k,$v[0],$v[1]):$instance->where($k,$v);
        }
        $instance = $type?$instance->get():$instance->first();
        return $instance ? $instance->toArray() : [];
    }

    /**
     * deleteInfo
     * 删除/恢复
     * User：YM
     * Date：2020/2/3
     * Time：下午8:22
     * @param $ids 删除的主键ids
     * @param string 删除delete/恢复restore
     * @return int
     */
    public function deleteInfo($ids, $type = 'delete') {
        $instance = make(get_called_class());
        if ($type == 'delete') {
            return $instance->destroy($ids);
        } else {
            $count = 0;
            $ids = is_array($ids)?$ids:[$ids];
            foreach ($ids as $id) {
                if ($instance::onlyTrashed()->find($id)->restore()) {
                    ++$count;
                }
            }

            return $count;
        }
    }

    /**
     * getPagesInfo
     * 获取分页信息，适用于数据量小
     * 数据量过大，可以采用服务层调用，加入缓存
     * User：YM
     * Date：2020/2/4
     * Time：下午10:16
     * @param $where
     * @return array
     */
    public function getPagesInfo($where = [], $count = true)
    {
        $pageSize = 10;
        $currentPage = 1;
        if (isset($where['page_size'])) {
            $pageSize = $where['page_size']>0?$where['page_size']:10;
            unset($where['page_size']);
        }
        if (isset($where['current_page'])) {
            $currentPage = $where['current_page']>0?$where['current_page']:1;
            unset($where['current_page']);
        }

        $offset = ($currentPage-1)*$pageSize;

        if($count) {
            $total = $this->getCount($where);
        }

        return [
            'current_page' => (int)$currentPage,
            'offset' => (int)$offset,
            'page_size' => (int)$pageSize,
            'total' => $count ? (int)$total : 0,
        ];
    }

    /**
     * getCount
     * 根据条件获取总数
     * User：YM
     * Date：2020/2/4
     * Time：下午10:16
     * @param array $where
     * @return int
     */
    public function getCount($where = [])
    {
        $instance = make(get_called_class());

        foreach ($where as $k => $v) {
            if ($k === 'title') {
                $instance = $instance->where($k,'LIKE','%'.$v.'%');
                continue;
            }
            $instance = $instance->where($k,$v);
        }

        $count = $instance->count();

        return $count > 0 ? $count : 0;
    }
}
