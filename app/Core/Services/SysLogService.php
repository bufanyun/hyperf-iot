<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * SysLogService.php
 *
 * User：YM
 * Date：2020/2/16
 * Time：上午11:48
 */


namespace Core\Services;

/**
 * SysLogService
 * 系统日志服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/16
 * Time：上午11:48
 *
 * @property \App\Models\Log $logModel
 */
class SysLogService extends BaseService
{
    /**
     * getList
     * 条件获取列表
     * User：YM
     * Date：2020/2/10
     * Time：下午10:34
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->logModel->getList($where,$order,$offset,$limit);
        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/10
     * Time：下午10:35
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->logModel->getPagesInfo($where);

        return $pageInfo;
    }
}