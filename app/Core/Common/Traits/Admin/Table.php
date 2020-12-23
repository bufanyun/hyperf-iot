<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Core\Common\Traits\Admin;

use App\Exception\BusinessException;
use App\Constants\StatusCode;
use Hyperf\DbConnection\Db;

/**
 * Trait Table
 * 表表操作类
 * @package Core\Common\Traits\Admin
 */
trait Table
{

    /**可操作的开关字段白名单
     * @var array
     */
    protected $switchList = ['status', ];

    /**
     * 添加数据
     * add
     * @param array $params
     * @param null $query
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2020/11/27 20:18
     */
    public function add(array $params = [], $query = null) : void
    {
        $model = make(get_called_class());
        if ($query == null) {
            $query = clone $model;
        }
        //过滤掉多余字段
        $insert = [];
        if (isset($model->fillable) && is_array($model->fillable)) {
            array_map(function ($k) use (&$insert, $params) {
                if (isset($params[$k]) && $params[$k] != '') {
                    $insert[$k] = $params[$k];
                }
            }, $model->fillable);
        }
        if (empty($insert)) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '没有提交任何参数内容');
        }

        Db::beginTransaction();
        try {
            $res = $query->insert($insert);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                $ex->getMessage());
        }

        if ( ! $res) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '添加失败，稍后重试');
        }
        return;
    }

    /**
     * 编辑/更新信息
     * edit
     *
     * @param  array  $where
     * @param  array  $params
     * @param  null   $query
     *
     * @return int|mixed
     * author MengShuai <133814250@qq.com>
     * date 2020/11/27 11:24
     */
    public function edit(array $where, array $params = [], $query = null) : void
    {
        $model = make(get_called_class());
        if ($query == null) {
            $query = clone $model;
        }
        //过滤掉多余字段
        $update = [];
        if (isset($model->fillable) && is_array($model->fillable)) {
            array_map(function ($k) use (&$update, $params) {
                if (isset($params[$k]) && $params[$k] != '') {
                    $update[$k] = $params[$k];
                }
            }, $model->fillable);
        }
        if (empty($update)) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '没有提交任何更新内容');
        }
        $exists = $query->where($where)->exists();
        if ( ! $exists) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '没有找到需要更新的数据');
        }
        Db::beginTransaction();
        try {
            $res = $query->where($where)->update($update);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                $ex->getMessage());
        }

        if ( ! $res) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '更新失败，稍后重试');
        }
        return;
    }

    /**
     * 状态类开关
     * switch
     *
     * @param  array  $where
     * @param  array  $params
     * @param  null   $query
     *
     * @return int|mixed
     * author MengShuai <133814250@qq.com>
     * date 2020/11/27 11:24
     */
    public function switch(array $where, array $params = [], $query = null) : int
    {
        $model = make(get_called_class());
        if ($query == null) {
            $query = clone $model;
        }
        if ( ! isset($params['key'])) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '没有提及需要更新的参数');
        }
        if ( !in_array($params['key'], $this->switchList)) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '参数:'.$params['key'].' 不在白名单');
        }
        $info = $query->select($params['key'])
            ->where($where)
            ->orderBy($model->getTable().'.'.$model->getKeyName(), 'DESC')
            ->first();
        if ($info === null || empty($info->toArray())) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '没有找到需要更新的数据');
        }
        if (isset($params['update']) && $params['update'] != '') {
            $update = $params['update'];
        } else {
            $update = $info->toArray()[$params['key']] == 1 ? 0 : 1;
        }

        Db::beginTransaction();
        try {
            $res = $query->where($where)->update([
                $params['key'] => $update,
            ]);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                $ex->getMessage());
        }

        if ( ! $res) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '更新失败，稍后重试');
        }
        return (int)$update;
    }

    /**
     * 表格查询
     * formQuery
     * @param array $params
     * @param null $query
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/11/26 22:59
     */
    public function formQuery(array $params = [], $query = null): array
    {
        $model = make(get_called_class());
        if ($query == null) {
            $query = clone $model;
        }
        //排序，默认使用主键
        $order = [$model->getKeyName() => 'DESC'];
        //分页
        $pageSize = 10;
        $currentPage = 1;
        if (isset($params['page_size'])) {
            $pageSize = $params['page_size'] > 0 ? $params['page_size'] : $pageSize;
            unset($params['page_size']);
        }
        if (isset($params['current_page'])) {
            $currentPage = $params['current_page'] > 0 ? $params['current_page'] : $currentPage;
            unset($params['current_page']);
        }
        $offset = ($currentPage - 1) * $pageSize;
        //查询条件
        $where = [];
        if (isset($model->fillable) && is_array($model->fillable)) {
            array_map(function ($k) use (&$where, $params) {
                if (isset($params[$k]) && $params[$k] != '') {
                    $where[$k] = $params[$k];
                }
            }, $model->fillable);

            //返回字段
            foreach ($model->fillable as $fk => $fv) {
                $model->fillable[$fk] = $model->getTable() . '.' . $fv;
            }
            $query->select($model->fillable);
        }

        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($v || $v != null) {
                $query =
                    $query->where($model->getTable() . '.' . $k, $v);
            }
        }
        // 追加排序
        if ($order && is_array($order)) {
            foreach ($order as $k => $v) {
                $query =
                    $query->orderBy($model->getTable() . '.' . $k, $v);
            }
        }

        //总数
        $que = clone $query;
        $count = $que->count();

        $query = $query->offset($offset)->limit($pageSize);
        $query = $query->get();
        $list = $query ? $query->toArray() : [];
        return [
            'count' => $count ?? 0,
            'list' => $list,
        ];
    }


    /**
     * 生成表格类查询所需要的条件,排序方式
     * buildTableParams
     * @param  array  $params
     * @param  null  $query
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/12/09 15:54
     */
    public function buildTableParams(array $params = [], $query = null) : array
    {
        $model = make(get_called_class());
        if ($query == null) {
            $query = clone $model;
        }

//        $relationSearch = is_null($relationSearch) ? $this->relationSearch : $relationSearch; //关联查询
        $search = isset($params['search']) ? $params['search'] : '';
        $filter = isset($params['filter']) ? $params['filter'] : '';

        $op = isset($params['op']) ? trim($params['op']) : '';
        $sort = isset($params['sort']) ? $params['sort'] : $model->getKeyName();
        $order = isset($params['order']) ? $params['order'] : 'DESC';
        $offset = isset($params['offset']) ? $params['offset'] : 0;
        $limit = isset($params['limit']) ? $params['limit'] : 10;
        $filter = (array)json_decode($filter, true);
        $op = (array)json_decode($op, true);
        $filter = $filter ? $filter : [];
        $tableName = $model->getTable();

//        if ($relationSearch) {
//            if (!empty($this->model)) {
//                $name = \think\Loader::parseName(basename(str_replace('\\', '/', get_class($this->model))));
//                $name = $this->model->getTable();
//                $tableName = $name . '.';
//            }
//            $sortArr = explode(',', $sort);
//            foreach ($sortArr as $index => & $item) {
//                $item = stripos($item, ".") === false ? $tableName . trim($item) : $  item;
//            }
//            unset($item);
//            $sort = implode(',', $sortArr);
//        }
//        $adminIds = $this->getDataLimitAdminIds();
//        if (is_array($adminIds)) {
//            $where[] = [$tableName . $this->dataLimitField, 'in', $adminIds];
//        }
        if ($search && ! empty($this->searchFields)) {  //允许快捷搜索的字段
            $searchFields = is_array($this->searchFields) ? $this->searchFields : explode(',', $this->searchFields);
            if ( ! empty($searchFields)) {
                $query->where(function ($query) use ($searchFields, $tableName, $search) {
                    foreach ($searchFields as $k => $v) {
                        $v = stripos($v, ".") === false ? $tableName.'.'.$v : $v;
                        $query->orWhere($v, 'like', "%{$search}%");
                    }
                    unset($v);
                });
            }
        }

        if (isset($model->fillable) && is_array($model->fillable)) {
            $legalKeys = array_intersect(array_keys($filter), $model->fillable);
            foreach ($filter as $k => $v) {
                if ( ! in_array($k, $legalKeys) || $filter[$k] == '') {
                    unset($filter[$k]);
                }
            }
            unset($v);
        } else {
            $filter = [];  //不存在fillable设置时，禁止高级查询
        }

        foreach ($filter as $k => $v) {
            $sym = isset($op[$k]) ? $op[$k] : '=';
            if (stripos($k, ".") === false) {
                $k = $tableName . '.' . $k;
            }
            $v = !is_array($v) ? trim($v) : $v;
            $sym = strtoupper(isset($op[$k]) ? $op[$k] : $sym);
            switch ($sym) {
                case '=':
                case '<>':
                    $query->where($k, $sym, (string)$v);
                    break;
                case 'LIKE':
                case 'NOT LIKE':
                case 'LIKE %...%':
                case 'NOT LIKE %...%':
                    $query->where($k, trim(str_replace('%...%', '', $sym)), "%{$v}%");
                    break;
                case '>':
                case '>=':
                case '<':
                case '<=':
                    $query->where($k, $sym, intval($v));
                    break;
//                case 'FINDIN':  //关联查询
//                case 'FINDINSET':
//                case 'FIND_IN_SET':
//                    $where[] = "FIND_IN_SET('{$v}', " . ($relationSearch ? $k : '`' . str_replace('.', '`.`', $k) . '`') . ")";
//                    break;
                case 'IN':
                case 'IN(...)':
                case 'NOT IN':
                case 'NOT IN(...)':
                    $query->where($k, str_replace('(...)', '', $sym), is_array($v) ? $v : explode(',', $v));
                    break;
                case 'BETWEEN':
                case 'NOT BETWEEN':
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr)) {
                        var_export(['$v' => $v , '$arr' => $arr]);
                        continue 2;
                    }
                    if($arr[0] !== '' && $arr[1] !== ''){
                        $m = $sym == 'BETWEEN' ? 'whereBetween' : 'whereNotBetween';
                        $query->$m($k, $arr);
                        break;
                    }

                    //当出现一边为空时改变操作符
                    if ($arr[0] === '') {
                        $sym = $sym == 'BETWEEN' ? '<=' : '>=';
                        $arr = $arr[1];
                    } elseif ($arr[1] === '') {
                        $sym = $sym == 'BETWEEN' ? '>=' : '=<';
                        $arr = $arr[0];
                    }

                    $query->where($k, $sym, $arr);
                    break;
                case 'RANGE':
                case 'NOT RANGE':
                    $v = str_replace(' - ', ',', $v);
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr)) {
                        continue 2;
                    }
                    if($sym=='NOT RANGE') {  //判断方法
                        $m = 'whereNotBetween';
                    }else{
                        $m = 'whereBetween';
                    }
                    $query->$m($k,$arr);
                    break;
                case 'LIKE':
                case 'LIKE %...%':
                    $query->where($k, 'LIKE', "%{$v}%");
                    break;
                case 'NULL':
                case 'IS NULL':
                case 'NOT NULL':
                case 'IS NOT NULL':
                    $query->where($k, strtolower(str_replace('IS ', '', $sym)));
                    break;
                default:
                    break;
            }
        }
        unset($v);

        return [$query, $sort, $order, $offset, $limit];
    }

}
