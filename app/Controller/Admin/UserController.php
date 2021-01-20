<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Constants\StatusCode;
use App\Constants\UserCode;
use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Models\User;
use Hyperf\Di\Annotation\Inject;
use Core\Plugins\Sms;
use Core\Plugins\Ems;

/**
 * UserController
 * 用户管理
 *
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/5
 * Time：下午4:04
 *
 * @Controller(prefix="/admin_api/user")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\UserRepository $userRepo
 * @property Sms                                     $Sms
 * @property Ems                                     $Ems
 */
class UserController extends BaseController
{
    /**
     *
     * @Inject()
     * @var User
     */
    private $model;

    /**
     * @Inject()
     * @var Sms
     */
    protected $Sms;

    /**
     * @Inject()
     * @var Ems
     */
    protected $Ems;

    /**
     * list
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $reqParam = $this->request->all();
        $select   = $this->model->fillable ?? ['*'];
        $where    = []; //额外条件

        [$total, $list] = $this->model->parallelSearch($reqParam, $where, $select);
        
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['level'] = UserCode::getLevelMap()[$v['level']];
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

    /**
     * switch
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="switch")
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
     * store
     * 保存，新建、编辑都用该方法，区别是否有主键id
     * User：YM
     * Date：2020/2/5
     * Time：下午5:01
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id       = $this->userRepo->saveUser($reqParam);

        return $this->success($id);
    }

    /**
     * 获取个人信息及其权限
     * Info
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="info")
     */
    public function info()
    {
        $currUser          = $this->auth->check();
        $currUser['level'] = UserCode::getLevelMap()[$currUser['level']];;
        $permission_menu = [['test' => 6]];   //权限菜单
        return $this->success([
                'roles'           => ['admin'],
                'introduction'    => '千里号卡，正规卡推广系统，平台源码5000，提供渠道接口对接，有意者联系：15303830571',
                'name'            => $currUser['nickname'],
                'permission_menu' => $permission_menu,
            ] + $currUser);
    }

    /**
     * 修改头像
     * update_avatar
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="update_avatar")
     * @throws \Exception
     */
    public function update_avatar()
    {
        $reqParam = $this->request->all();
        $update   = [
            'id'     => $this->auth->check(false),
            //允许修改的字段
            'avatar' => $reqParam['avatar'],
        ];
        $id       = $this->userRepo->saveUser($update);

        return $this->success($id);
    }

    /**
     * 修改个人信息
     * update_info
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="update_info")
     * @throws \Exception
     */
    public function update_info()
    {
        $reqParam = $this->request->all();
        $update   = [
            'id'       => $this->auth->check(false),
            //允许修改的字段
            'password' => $reqParam['password'],
        ];
        $id       = $this->userRepo->saveUser($update);

        return $this->success($id);
    }

    /**
     * 修改提现信息
     * update_cash
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="update_cash")
     * @throws \Exception
     */
    public function update_cash()
    {
        $user     = $this->auth->check();
        $reqParam = $this->request->all();
        if (!isset($reqParam['captcha']) || $reqParam['captcha'] == '') {
            return $this->error(StatusCode::ERR_EXCEPTION, '请输入验证码');
        }
        $ret = $this->Sms->check($user['mobile'], $reqParam['captcha'], 'modified_withdrawal');
        if (!$ret) {
            return $this->error(StatusCode::ERR_EXCEPTION, '验证码不正确');
        }
        $update = [
            'id'   => $user['id'],
            //允许修改的字段
            'cash' => json_encode([
                //允许修改的字段
                'alipay_name'    => $reqParam['alipay_name'],
                'add_make_img'   => $reqParam['add_make_img'],
                'alipay_account' => $reqParam['alipay_account'],
            ]),
        ];
        $id     = $this->userRepo->saveUser($update);

        return $this->success($id);
    }

    /**
     * 修改/重置API配置
     * reset_api
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="reset_api")
     * @throws \Exception
     */
    public function reset_secret_key()
    {
        $reqParam = $this->request->all();
        if (isset($reqParam['ip_white']) && $reqParam['ip_white'] !== '') {
            $ip_whites = explode(',', $reqParam['ip_white']);
            foreach ($ip_whites as $ip) {
                if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $this->error(StatusCode::ERR_EXCEPTION, $ip . '不是有效的IP地址');
                }
            }
            unset($ip);
        }
        $update = [
            'id'         => $this->auth->check(false),
            //允许修改的字段
            'secret_key' => $reqParam['secret_key'],
            'ip_white'   => $reqParam['ip_white'],
        ];
        $id     = $this->userRepo->saveUser($update);

        return $this->success($id);
    }

    /**
     * 修改邮箱
     * update_email
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="update_email")
     * @throws \Exception
     */
    public function update_email()
    {
        $reqParam = $this->request->all();
        if (!isset($reqParam['captcha']) || $reqParam['captcha'] == '') {
            return $this->error(StatusCode::ERR_EXCEPTION, '请输入验证码');
        }
        $ret = $this->Ems->check($reqParam['email'], $reqParam['captcha'], 'update_email');
        if (!$ret) {
            return $this->error(StatusCode::ERR_EXCEPTION, '验证码不正确');
        }
        $update = [
            'id'    => $this->auth->check(false),
            //允许修改的字段
            'email' => $reqParam['email'],
        ];
        $id     = $this->userRepo->saveUser($update);

        return $this->success($id);
    }

    /**
     * getInfo
     * 根据id获取单条记录信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:25
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info     = $this->userRepo->getInfo($reqParam['id']);
        $data     = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除用户
     * User：YM
     * Date：2020/2/5
     * Time：下午4:26
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->userRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * getRoles
     * 获取绑定角色信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:26
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_roles")
     */
    public function getRoles()
    {
        $data = $this->userRepo->getRolesList();

        return $this->success($data);
    }
}