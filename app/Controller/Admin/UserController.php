<?php

declare(strict_types=1);

namespace App\Controller\Admin;

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


/**
 * UserController
 * 用户管理
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
     * list
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $reqParam = $this->request->all();
        $query    = $this->model->query();

        [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $query);
        $where = []; //额外条件

        $total = $querys
            ->where($where)
            ->orderBy($sort, $order)
            ->count();
        //        Db::enableQueryLog();
        $list = $querys
            ->where($where)
            ->orderBy($sort, $order)
            ->offset($offset)->limit($limit)
            ->get();
        //        var_export(Db::getQueryLog());

        $list = $list ? $list->toArray() : [];
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                //                $list[$k]['status'] = $v['status'] === 0 ? false : true;
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

    /**
     * switch
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

//    /**
//     * index
//     * 用户列表，用户管理
//     * User：YM
//     * Date：2020/2/5
//     * Time：下午4:05
//     * @return \Psr\Http\Message\ResponseInterface
//     *
//     * @GetMapping(path="list")
//     */
//    public function index()
//    {
//        $reqParam = $this->request->all();
//        $list = $this->userRepo->getUserList(filterParams($reqParam));
//        $data = [
//            'pages' => $list['pages'],
//            'list' => $list['data'],
//        ];
//        return $this->success($data);
//    }


    /**
     * store
     * 保存，新建、编辑都用该方法，区别是否有主键id
     * User：YM
     * Date：2020/2/5
     * Time：下午5:01
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
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="info")
     */
    public function info()
    {
        $currUser        = $this->auth->check();
        $permission_menu = [['test' => 6]];   //权限菜单
        return $this->success([
                'roles'           => ['admin'],
                'introduction'    => '千里号卡，正规卡推广系统，平台源码5000，提供渠道接口对接，有意者联系：15303830571',
                'name'            => $currUser['nickname'],
                'permission_menu' => $permission_menu,
            ] + $currUser);
    }

    /**
     * 修改个人信息
     * update_info
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="update_info")
     * @throws \Exception
     */
    public function update_info()
    {
        $reqParam = $this->request->all();
        $id       = $this->userRepo->saveUser($reqParam+['id' => $this->auth->check(false)]);

        return $this->success($id);
    }


    /**
     * getInfo
     * 根据id获取单条记录信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:25
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