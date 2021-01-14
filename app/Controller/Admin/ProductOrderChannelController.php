<?php
declare (strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Crypto\Rand;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use http\Exception;
use App\Exception\BusinessException;
use Core\Plugins\BaiDu\Lbs;
use Hyperf\DbConnection\Db;
use App\Models\IpRegion;
use Hyperf\Utils\Parallel;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Constants\StatusCode;
use App\Models\ProductOrderChannel;

/**
 * ProductOrderChannelController
 * 销售渠道
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/product_order_channel")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class ProductOrderChannelController extends BaseController
{

    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     *
     * @Inject()
     * @var ProductOrderChannel
     */
    private $model;

}