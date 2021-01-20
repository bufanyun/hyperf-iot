<?php
declare (strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use App\Models\AdminMoneyLog;

/**
 * AdminMoneyLogController
 * 余额记录明细
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/admin_money_log")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 * @property \App\Models\AdminMoneyLog $model
 */
class AdminMoneyLogController extends BaseController
{
    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     * @Inject()
     * @var AdminMoneyLog
     */
    private $model;

}