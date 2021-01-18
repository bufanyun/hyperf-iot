<?php

declare (strict_types=1);

namespace App\Controller\Admin;

use App\Constants\StatusCode;
use App\Constants\UserCode;
use App\Controller\BaseController;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Constants\UserLevelCode;
use App\Constants\AdminMoneyLogCode;
use App\Models\UserLevel;
use App\Models\User;
use App\Models\AdminMoneyLog;
use Core\Common\Extend\Helpers\ArrayHelpers;
use App\Exception\DatabaseExceptionHandler;

/**
 * UserLevelController
 * 用户等级
 *
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/user_level")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 * @property UserLevel     $model
 * @property User          $UserModel
 * @property AdminMoneyLog $AdminMoneyLogModel
 */
class UserLevelController extends BaseController
{
    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     *
     * @Inject()
     * @var UserLevel
     */
    private $model;
    /**
     *
     * @Inject()
     * @var AdminMoneyLog
     */
    private $AdminMoneyLogModel;
    /**
     *
     * @Inject()
     * @var User
     */
    private $UserModel;

    /**
     * 升级购买分销等级
     * buy
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="buy")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:24
     */
    public function buy()
    {
        $reqParam = $this->request->all();
        if (!isset($reqParam['id'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '请选择等级！');
        }
        $query     = $this->model->query();
        $levelInfo = $query->where(['id' => $reqParam['id']])->first();
        if ($levelInfo['status'] !== 1) {
            return $this->error(StatusCode::ERR_EXCEPTION, '等级权限已暂停购买！');
        }
        $currUser = $this->auth->check();
        if ($currUser['level'] >= $levelInfo['id']) {
            return $this->error(StatusCode::ERR_EXCEPTION,
                '您已经是' . UserCode::getLevelMap()[$currUser['level']] . '啦，无需购买！');
        }
        if ($currUser['balance'] < $levelInfo['price']) {
            return $this->error(StatusCode::ERR_EXCEPTION_USER, '余额不足，请先充值！');
        }

        Db::beginTransaction();
        try {
            $this->UserModel->where(['id' => $currUser['id']])->update($this->UserModel->loadModel([
                'balance' => $currUser['balance'] - $levelInfo['price'],
                'level'   => $levelInfo['id'],
            ]));
            $this->AdminMoneyLogModel->set([
                'admin_id' => $currUser['id'],
                'money'    => $levelInfo['price'],
                'before'   => $currUser['balance'],
                'after'    => $currUser['balance'] - $levelInfo['price'],
                'memo'     => AdminMoneyLogCode::PURCHASE_LEVEL . ': #' . $levelInfo['id'],
            ]);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new DatabaseExceptionHandler(StatusCode::ERR_EXCEPTION_DATABASE, $ex->getMessage(), $ex);
        }
        return $this->success([], '购买成功');
    }

    /**
     * 下拉框
     * selected
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="selected")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:24
     */
    public function selected()
    {
        $query = $this->model->query();
        $where = ['status' => 1];
        $list  = $query
            ->select('name', 'id as code')
            ->where($where)
            ->orderBy('sort', 'DESC')
            ->get()
            ->toArray();
        return $this->success($list);
    }

    /**
     * list
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $list     = $this->model->query()->orderBy('id', 'ASC')->get()->toArray();
        $currUser = $this->auth->check();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['checked'] = ($currUser['level'] === $v['id']) ?? false;
            }
            unset($v);
        }
        $result = ["rows" => $list, 'user' => $currUser + ['level_name' => UserCode::getLevelMap()[$currUser['level']]]];
        return $this->success($result);
    }

}