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

namespace Core\Common\Traits\Admin\Controller;

use App\Constants\StatusCode;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Exception\DatabaseExceptionHandler;

/**
 * 通用控制器方法
 * Trait Expert
 *
 * @package Core\Common\Traits\Admin\Controller
 * author MengShuai <133814250@qq.com>
 * date 2021/01/14 21:24
 */
trait Expert
{
    /**
     * 页面列表
     * list
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:24
     */
    public function list()
    {
        $reqParam = $this->request->all();
        $where    = []; //额外条件
        $query    = $this->model->query()->where($where);   //前置模型

        [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $query);

        $total = $querys
            ->orderBy($sort, $order)
            ->count();
        //        Db::enableQueryLog();
        $list = $querys
            ->orderBy($sort, $order)
            ->offset($offset)->limit($limit)
            ->get()
            ->toArray();

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                //    $list[$k]['status'] = $v['status'] === 0 ? false : true;
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

    /**
     * 状态开关
     * switch
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="switch")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:24
     */
    public function switch()
    {
        $reqParam = $this->request->all();
        if (!isset($reqParam['key'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新开关的参数');
        }
        $primaryKey = $this->model->getKeyName();
        if (!isset($reqParam[$primaryKey])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新开关的条件');
        }
        $query = $this->model->query();
        $where = [$primaryKey => $reqParam[$primaryKey]];
        $param = [
            'key'    => $reqParam['key'],
            'update' => isset($reqParam['update']) ? $reqParam['update'] : '',
        ];

        $update = $this->model->switch($where, $param, $query);
        return $this->success(['switch' => $update]);
    }


    /**
     * 编辑/更新行
     * edit
     *
     * @return void
     *
     * @RequestMapping(path="edit")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:24
     */
    public function edit()
    {
        if (!$this->request->has($this->model->getKeyName())) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少编辑的条件');
        }
        $row = $this->model->query()
            ->where([$this->model->getKeyName() => $this->request->input($this->model->getKeyName()),])
            ->first();
        if (!$row) {
            return $this->error(StatusCode::ERR_EXCEPTION, '数据不存在');
        }

        if ($this->request->isMethod('post') && $this->model->edit($row, $this->request->all())) {
            return $this->success($row, '更新成功');
        }

        return $this->success($row, '获取成功');
    }

    /**
     * 添加/插入行
     * add
     *
     * @return mixed
     *
     * @RequestMapping(path="add")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:45
     */
    public function add()
    {
        if ($this->request->isMethod('post') && $this->model->add($this->request->all())) {
            return $this->success([], '添加成功');
        }
        return $this->error(StatusCode::ERR_EXCEPTION, '访问非法');
    }


    /**
     * 删除
     * del
     *
     * @RequestMapping(path="del")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 11:02
     */
    public function del()
    {
        if ($this->request->isMethod('post')) {
            if (!$this->request->has($this->model->getKeyName())) {
                return $this->error(StatusCode::ERR_EXCEPTION, '缺少编辑的条件');
            }
            $ids   = explode(",", (string)$this->request->input($this->model->getKeyName()));
            $query = $this->model->query();
            $where = [
                //管理员访问权限
            ];
            $query->where($where);
            $count = $this->model->del($ids, $query);
            if ($count) {
                return $this->success([], '成功删除' . $count . '条数据');
            } else {
                return $this->error(StatusCode::ERR_EXCEPTION, '删除失败');
            }
        }

        return $this->error(StatusCode::ERR_EXCEPTION, '访问非法');
    }


    /**
     * 回收站
     */
    public function recyclebin()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this->model
                ->onlyTrashed()
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->onlyTrashed()
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = ["total" => $total, "rows" => $list];

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 真实删除
     */
    public function destroy($ids = "")
    {
        $pk       = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        if ($ids) {
            $this->model->where($pk, 'in', $ids);
        }
        $count = 0;
        Db::startTrans();
        try {
            $list = $this->model->onlyTrashed()->select();
            foreach ($list as $k => $v) {
                $count += $v->delete(true);
            }
            Db::commit();
        } catch (PDOException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        } else {
            $this->error(__('No rows were deleted'));
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 还原
     */
    public function restore($ids = "")
    {
        $pk       = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        if ($ids) {
            $this->model->where($pk, 'in', $ids);
        }
        $count = 0;
        Db::startTrans();
        try {
            $list = $this->model->onlyTrashed()->select();
            foreach ($list as $index => $item) {
                $count += $item->restore();
            }
            Db::commit();
        } catch (PDOException $e) {
            Db::rollback();
            $this->error($e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were updated'));
    }

    /**
     * 批量更新
     */
    public function multi($ids = "")
    {
        $ids = $ids ? $ids : $this->request->param("ids");
        if ($ids) {
            if ($this->request->has('params')) {
                parse_str($this->request->post("params"), $values);
                $values = array_intersect_key($values,
                    array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
                if ($values || $this->auth->isSuperAdmin()) {
                    $adminIds = $this->getDataLimitAdminIds();
                    if (is_array($adminIds)) {
                        $this->model->where($this->dataLimitField, 'in', $adminIds);
                    }
                    $count = 0;
                    Db::startTrans();
                    try {
                        $list = $this->model->where($this->model->getPk(), 'in', $ids)->select();
                        foreach ($list as $index => $item) {
                            $count += $item->allowField(true)->isUpdate(true)->save($values);
                        }
                        Db::commit();
                    } catch (PDOException $e) {
                        Db::rollback();
                        $this->error($e->getMessage());
                    } catch (Exception $e) {
                        Db::rollback();
                        $this->error($e->getMessage());
                    }
                    if ($count) {
                        $this->success();
                    } else {
                        $this->error(__('No rows were updated'));
                    }
                } else {
                    $this->error(__('You have no permission'));
                }
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 导入
     */
    protected function import()
    {
        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        //实例化reader
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $this->error(__('Unknown data format'));
        }
        if ($ext === 'csv') {
            $file     = fopen($filePath, 'r');
            $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
            $fp       = fopen($filePath, "w");
            $n        = 0;
            while ($line = fgets($file)) {
                $line     = rtrim($line, "\n\r\0");
                $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
                if ($encoding != 'utf-8') {
                    $line = mb_convert_encoding($line, 'utf-8', $encoding);
                }
                if ($n == 0 || preg_match('/^".*"$/', $line)) {
                    fwrite($fp, $line . "\n");
                } else {
                    fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
                }
                $n++;
            }
            fclose($file) || fclose($fp);

            $reader = new Csv();
        } elseif ($ext === 'xls') {
            $reader = new Xls();
        } else {
            $reader = new Xlsx();
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table    = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list     = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?",
            [$table, $database]);
        foreach ($list as $k => $v) {
            if ($importHeadType == 'comment') {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            } else {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        //加载文件
        $insert = [];
        try {
            if (!$PHPExcel = $reader->load($filePath)) {
                $this->error(__('Unknown data format'));
            }
            $currentSheet    = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
            $allColumn       = $currentSheet->getHighestDataColumn(); //取得最大的列号
            $allRow          = $currentSheet->getHighestRow(); //取得一共有多少行
            $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);
            $fields          = [];
            for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val      = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $fields[] = $val;
                }
            }

            for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                $values = [];
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val      = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $values[] = is_null($val) ? '' : $val;
                }
                $row  = [];
                $temp = array_combine($fields, $values);
                foreach ($temp as $k => $v) {
                    if (isset($fieldArr[$k]) && $k !== '') {
                        $row[$fieldArr[$k]] = $v;
                    }
                }
                if ($row) {
                    $insert[] = $row;
                }
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        if (!$insert) {
            $this->error(__('No rows were updated'));
        }

        try {
            //是否包含admin_id字段
            $has_admin_id = false;
            foreach ($fieldArr as $name => $key) {
                if ($key == 'admin_id') {
                    $has_admin_id = true;
                    break;
                }
            }
            if ($has_admin_id) {
                $auth = Auth::instance();
                foreach ($insert as &$val) {
                    if (!isset($val['admin_id']) || empty($val['admin_id'])) {
                        $val['admin_id'] = $auth->isLogin() ? $auth->id : 0;
                    }
                }
            }
            $this->model->saveAll($insert);
        } catch (PDOException $exception) {
            $msg = $exception->getMessage();
            if (preg_match("/.+Integrity constraint violation: 1062 Duplicate entry '(.+)' for key '(.+)'/is", $msg,
                $matches)) {
                $msg = "导入失败，包含【{$matches[1]}】的记录已存在";
            };
            $this->error($msg);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success();
    }


}