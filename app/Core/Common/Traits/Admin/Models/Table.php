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

namespace Core\Common\Traits\Admin\Models;

use App\Exception\BusinessException;
use App\Constants\StatusCode;
use Hyperf\DbConnection\Db;
use App\Exception\DatabaseExceptionHandler;

/**
 * Trait Table
 * 表格操作类
 *
 * @package Core\Common\Traits\Admin\Models
 * author MengShuai <133814250@qq.com>
 * date 2021/01/15 14:41
 */
trait Table
{
    /**
     * 添加数据行
     * add
     *
     * @param array $params
     * @param null  $query
     *
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 14:41
     */
    public function add(array $params = [], $query = null): bool
    {
        $model = make(get_called_class());
        if ($query == null) {
            $query = clone $model;
        }
        //过滤掉多余字段
        $insert = $this->loadModel($params, null, false);
        if (count($insert) < 1) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '缺少插入内容');
        }

        Db::beginTransaction();
        try {
            $res = $query->insert($insert);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new DatabaseExceptionHandler(StatusCode::ERR_EXCEPTION_DATABASE, $ex->getMessage(), $ex);
        }

        if (!$res) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '添加失败，稍后重试');
        }
        return true;
    }

    /**
     * 编辑/更新信息行
     * edit
     *
     * @param       $where
     * @param array $params
     * @param null  $query
     *
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 14:41
     */
    public function edit($where, array $params = [], $query = null): bool
    {
        $model = make(get_called_class());
        if ($query == null) {
            $query = clone $model;
        }
        //过滤掉多余字段
        $update = $this->loadModel($params);
        if (count($update) < 1) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '缺少更新内容');
        }
        Db::beginTransaction();
        try {
            $res = $query->where([$this->getKeyName() => $where->{$this->getKeyName()}])
                ->update($update);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new DatabaseExceptionHandler(StatusCode::ERR_EXCEPTION_DATABASE, $ex->getMessage(), $ex);
        }

        if (!$res) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '更新失败，稍后重试');
        }
        return true;
    }

    /**
     * 通过主键删除数据，支持批量，兼容伪删除
     * del
     *
     * @param      $ids
     * @param null $query
     *
     * @return int
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 14:37
     */
    public function del($ids, $query = null): int
    {
        $model = make(get_called_class());
        if ($query == null) {
            $query = clone $model;
        }
        $list = $query->whereIn($this->getKeyName(), $ids)->get()->toArray();
        if (!$list) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '未找到需要删除的数据');
        }

        $count       = 0;
        $isPseudoDel = $this->isPseudoDel();
        Db::beginTransaction();
        try {
            foreach ($list as $k => $v) {
                $db    = Db::table($this->getTable())->where([$this->getKeyName() => $v[$this->getKeyName()]]);
                $count += $isPseudoDel
                    ? $db->update([static::DELETED_AT => date("Y-m-d H:i:s")])
                    : $db->delete();
            }
            unset($v);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new DatabaseExceptionHandler(StatusCode::ERR_EXCEPTION_DATABASE, $ex->getMessage(), $ex);
        }
        return $count;
    }

    /**
     * 状态类开关
     * switch
     *
     * @param array $where
     * @param array $params
     * @param null  $query
     *
     * @return int
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 14:42
     */
    public function switch(array $where, array $params = [], $query = null): int
    {
        $model = make(get_called_class());
        if ($query == null) {
            $query = clone $model;
        }
        if (!isset($params['key'])) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '没有提及需要更新的参数');
        }
        if (!in_array($params['key'], $this->switchList)) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '参数:' . $params['key'] . ' 不在白名单');
        }
        $info = $query->select($params['key'])
            ->where($where)
            ->orderBy($model->getTable() . '.' . $model->getKeyName(), 'DESC')
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
            throw new DatabaseExceptionHandler(StatusCode::ERR_EXCEPTION_DATABASE, $ex->getMessage(), $ex);
        }

        if (!$res) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '更新失败，稍后重试');
        }
        return (int)$update;
    }

    /**
     * 获取检查后的更新/添加的数据内容
     * loadModel
     *
     * @param array $params
     * @param null  $query
     * @param bool  $isUpdate
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:42
     */
    public function loadModel(array $params = [], $query = null, bool $isUpdate = true): array
    {
        $model = make(get_called_class());
        if ($query === null) {
            $query = clone $model;
        }
        $update     = [];
        $editRoster = $model->editRoster ?? $model->fillable;
        if ($isUpdate === false) {
            $editRoster = $model->fillable;
        }
        if (isset($params)) {
            array_map(function ($k) use (&$update, $params, $model) {
                if (isset($params[$k])) {
                    $update[$model->getTable() . '.' . $k] = $params[$k];
                }
            }, $editRoster);
        }
        $update = array_merge(
            $update,
            $this->setFillAttribute($model, $isUpdate),
        //...
        );
        return $update;
    }

    /**
     * 填充模型自动字段
     * setFillAttribute
     *
     * @param      $model
     * @param bool $isUpdate
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 10:46
     */
    protected function setFillAttribute($model, bool $isUpdate): array
    {
        $update = [];
        //新增时间
        if ($isUpdate === false && in_array($model::CREATED_AT, $model->fillable)) {
            $update[$model::CREATED_AT] = date("Y-m-d H:i:s");
        }
        //更新时间
        if ($isUpdate === true && in_array($model::UPDATED_AT, $model->fillable)) {
            $update[$model::UPDATED_AT] = date("Y-m-d H:i:s");
        }
        return $update;
    }

    /**
     * 判断是否存在伪删除
     * isPseudoDel
     *
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 11:49
     */
    public function isPseudoDel(): bool
    {
        return in_array(static::DELETED_AT, $this->fillable);
    }

    /**
     * 生成表格类查询所需要的条件,排序方式,过滤伪删除
     * buildTableParams
     *
     * @param array $params
     * @param null  $query
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/12/09 15:54
     */
    public function buildTableParams(array $params = [], $query = null): array
    {
        $model = make(get_called_class());
        if ($query === null) {
            $query = clone $model;
        }

        $search    = isset($params['search']) ? $params['search'] : '';
        $filter    = isset($params['filter']) ? $params['filter'] : '';
        $op        = isset($params['op']) ? trim($params['op']) : '';
        $sort      = isset($params['sort']) ? $params['sort'] : $model->getKeyName();
        $order     = isset($params['order']) ? $params['order'] : 'DESC';
        $offset    = isset($params['offset']) ? $params['offset'] : 0;
        $limit     = isset($params['limit']) ? $params['limit'] : 10;
        $filter    = (array)json_decode($filter, true);
        $op        = (array)json_decode($op, true);
        $filter    = $filter ? $filter : [];
        $tableName = $model->getTable();

        if ($search && !empty($this->searchFields)) {  //允许快捷搜索的字段
            $searchFields = is_array($this->searchFields) ? $this->searchFields : explode(',', $this->searchFields);
            if (!empty($searchFields)) {
                $query->where(function ($query) use ($searchFields, $tableName, $search) {
                    foreach ($searchFields as $k => $v) {
                        $v = stripos($v, ".") === false ? $tableName . '.' . $v : $v;
                        $query->orWhere($v, 'like', "%{$search}%");
                    }
                    unset($v);
                });
            }
        }

        if (isset($model->fillable) && is_array($model->fillable)) {
            $legalKeys = array_intersect(array_keys($filter), $model->fillable);
            foreach ($filter as $k => $v) {
                if (!in_array($k, $legalKeys) || $filter[$k] == '') {
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
            $v   = !is_array($v) ? trim($v) : $v;
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
                        var_export(['$v' => $v, '$arr' => $arr]);
                        continue 2;
                    }
                    if ($arr[0] !== '' && $arr[1] !== '') {
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
                    $v   = str_replace(' - ', ',', $v);
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr)) {
                        continue 2;
                    }
                    if ($sym == 'NOT RANGE') {  //判断方法
                        $m = 'whereNotBetween';
                    } else {
                        $m = 'whereBetween';
                    }
                    $query->$m($k, $arr);
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

        //是否使用伪删除
        if ($this->isPseudoDel()) {
            $query->whereNull(static::DELETED_AT);
        }

        return [$query, $sort, $order, $offset, $limit];
    }

}
