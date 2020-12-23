<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * StatisticsController.php
 *
 * User：YM
 * Date：2020/2/18
 * Time：下午4:58
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;

/**
 * StatisticsController
 * 数据统计
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/18
 * Time：下午4:58
 *
 * @Controller(prefix="admin_api/statistics")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\StatisticsRepository $statisticsRepo
 */
class StatisticsController extends BaseController
{
    /**
     * flowData
     * 流量统计
     * User：YM
     * Date：2020/2/18
     * Time：下午9:27
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="flow_data")
     */
    public function flowData()
    {
        $reqParam = $this->request->all();
        $list = $this->statisticsRepo->getFlowData($reqParam);

        return $this->success($list);
    }

    /**
     * regionData
     * 地域统计
     * User：YM
     * Date：2020/2/19
     * Time：下午9:12
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="region_data")
     */
    public function regionData()
    {
        $reqParam = $this->request->all();
        $list = $this->statisticsRepo->getRegionData($reqParam);

        return $this->success($list);
    }
}