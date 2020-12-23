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
use Core\Common\Traits\Admin\Table;

abstract class BaseModel extends Model implements CacheableInterface
{
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


    /**
     * 生成查询所需要的条件
     * buildparams
     * @param array $ReqParams
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/11/26 16:43
     */
    public function buildparams($params = []): ?array
    {
        if (empty($params)) {
            return [];
        }
        $instance = make(get_called_class());
        $where = [];
        array_map(function ($k) use (&$where, $params) {
            if (isset($params[$k]) && $params[$k] != '') {
                $where[$k] = $params[$k];
            }
        }, $instance->fillable);
        return $where;
    }

    /**
     * 生成查询所需要的条件,排序方式
     * @param mixed   $searchfields   快速查询的字段
     * @param boolean $relationSearch 是否关联查询
     * @return array
     */
    protected function build($searchfields = null, $relationSearch = null)
    {
        $searchfields = is_null($searchfields) ? $this->searchFields : $searchfields;
        $relationSearch = is_null($relationSearch) ? $this->relationSearch : $relationSearch;
        $search = $this->request->get("search", '');
        $filter = $this->request->get("filter", '');
        $op = $this->request->get("op", '', 'trim');
        $sort = $this->request->get("sort", !empty($this->model) && $this->model->getPk() ? $this->model->getPk() : 'id');
        $order = $this->request->get("order", "DESC");
        $offset = $this->request->get("offset", 0);
        $limit = $this->request->get("limit", 0);
        $filter = (array)json_decode($filter, true);
        $op = (array)json_decode($op, true);
        $filter = $filter ? $filter : [];
        $where = [];
        $tableName = '';
        if ($relationSearch) {
            if (!empty($this->model)) {
                $name = \think\Loader::parseName(basename(str_replace('\\', '/', get_class($this->model))));
                $name = $this->model->getTable();
                $tableName = $name . '.';
            }
            $sortArr = explode(',', $sort);
            foreach ($sortArr as $index => & $item) {
                $item = stripos($item, ".") === false ? $tableName . trim($item) : $item;
            }
            unset($item);
            $sort = implode(',', $sortArr);
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $where[] = [$tableName . $this->dataLimitField, 'in', $adminIds];
        }
        if ($search) {
            $searcharr = is_array($searchfields) ? $searchfields : explode(',', $searchfields);
            foreach ($searcharr as $k => &$v) {
                $v = stripos($v, ".") === false ? $tableName . $v : $v;
            }
            unset($v);
            $where[] = [implode("|", $searcharr), "LIKE", "%{$search}%"];
        }
        foreach ($filter as $k => $v) {
            $sym = isset($op[$k]) ? $op[$k] : '=';
            if (stripos($k, ".") === false) {
                $k = $tableName . $k;
            }
            $v = !is_array($v) ? trim($v) : $v;
            $sym = strtoupper(isset($op[$k]) ? $op[$k] : $sym);
            switch ($sym) {
                case '=':
                case '<>':
                    $where[] = [$k, $sym, (string)$v];
                    break;
                case 'LIKE':
                case 'NOT LIKE':
                case 'LIKE %...%':
                case 'NOT LIKE %...%':
                    $where[] = [$k, trim(str_replace('%...%', '', $sym)), "%{$v}%"];
                    break;
                case '>':
                case '>=':
                case '<':
                case '<=':
                    $where[] = [$k, $sym, intval($v)];
                    break;
                case 'FINDIN':
                case 'FINDINSET':
                case 'FIND_IN_SET':
                    $where[] = "FIND_IN_SET('{$v}', " . ($relationSearch ? $k : '`' . str_replace('.', '`.`', $k) . '`') . ")";
                    break;
                case 'IN':
                case 'IN(...)':
                case 'NOT IN':
                case 'NOT IN(...)':
                    $where[] = [$k, str_replace('(...)', '', $sym), is_array($v) ? $v : explode(',', $v)];
                    break;
                case 'BETWEEN':
                case 'NOT BETWEEN':
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr)) {
                        continue 2;
                    }
                    //当出现一边为空时改变操作符
                    if ($arr[0] === '') {
                        $sym = $sym == 'BETWEEN' ? '<=' : '>';
                        $arr = $arr[1];
                    } elseif ($arr[1] === '') {
                        $sym = $sym == 'BETWEEN' ? '>=' : '<';
                        $arr = $arr[0];
                    }
                    $where[] = [$k, $sym, $arr];
                    break;
                case 'RANGE':
                case 'NOT RANGE':
                    $v = str_replace(' - ', ',', $v);
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr)) {
                        continue 2;
                    }
                    //当出现一边为空时改变操作符
                    if ($arr[0] === '') {
                        $sym = $sym == 'RANGE' ? '<=' : '>';
                        $arr = $arr[1];
                    } elseif ($arr[1] === '') {
                        $sym = $sym == 'RANGE' ? '>=' : '<';
                        $arr = $arr[0];
                    }
                    $where[] = [$k, str_replace('RANGE', 'BETWEEN', $sym) . ' time', $arr];
                    break;
                case 'LIKE':
                case 'LIKE %...%':
                    $where[] = [$k, 'LIKE', "%{$v}%"];
                    break;
                case 'NULL':
                case 'IS NULL':
                case 'NOT NULL':
                case 'IS NOT NULL':
                    $where[] = [$k, strtolower(str_replace('IS ', '', $sym))];
                    break;
                default:
                    break;
            }
        }

        $where = function ($query) use ($where) {

            foreach ($where as $k => $v) {

                if (is_array($v)) {
                    call_user_func_array([$query, 'where'], $v);
                } else {
                    $query->where($v);
                }
            }
        };
        return [$where, $sort, $order, $offset, $limit];
    }
}
