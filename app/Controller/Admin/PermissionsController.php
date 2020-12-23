<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * PermissionsController.php
 *
 * User：YM
 * Date：2020/1/11
 * Time：下午2:43
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use App\Constants\StatusCode;
use Throwable;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\DbConnection\Db;
use App\Models\SystemPermission;
use Core\Common\Extend\Tools\Tree;
use Core\Common\Container\Auth;
use Core\Common\Container\AdminPermission;

/**
 * PermissionsController
 * 权限控制器
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/1/11
 * Time：下午2:43
 *
 * @Controller(prefix="admin_api/permission")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\PermissionsRepository $permissionsRepo
 */
class PermissionsController extends BaseController
{
    /**
     * tree
     * 权限树
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="tree")
     */
    public function tree()
    {
        $list = $this->permissionsRepo->getPermissionsTreeList();

        return $this->success($list);
    }

    /**
     * list
     * 权限列表
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="list")
     */
    public function list(SystemPermission $SystemPermission)
    {
        $reqParam = $this->request->all();
        $query = $SystemPermission->query();
        if (isset($reqParam['pid']) && $reqParam['pid'] != '') {
            $query = $query->where(['id' => $reqParam['pid']])->orWhere(
                'parent_id',
                $reqParam['pid']
            );
        }
        list($where, $sort, $order, $offset, $limit) = $SystemPermission->buildParams($reqParam, $query);

        var_export([$where, $sort, $order, $offset, $limit]);
        var_export('off..');
        
        $data = $SystemPermission->formQuery($reqParam, $query);
        if (!empty($data['list'])) {
            foreach ($data['list'] as $k => $v) {
                if ($data['list'][$k]['parent_id'] == 0) {
                    $data['list'][$k]['parent_name'] = '顶级菜单';
                } else {
                    $data['list'][$k]['parent_name'] = $SystemPermission->query()->where('id',
                            $data['list'][$k]['parent_id'])->value('display_name');
                }
            }
        }
        return $this->success($data);
    }


    /**
     * switch
     * 更新状态开关
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="switch")
     */
    public function switch(SystemPermission $SystemPermission)
    {
        $reqParam = $this->request->all();
        if ( ! isset($reqParam['key'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新开关的参数');
        }
        $primaryKey = $SystemPermission->getKeyName();
        if ( ! isset($reqParam[$primaryKey])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新开关的条件');
        }
        $query = $SystemPermission->query();
        $where = [$primaryKey => $reqParam[$primaryKey]];
        $param = [
            'key'    => $reqParam['key'],
            'update' => isset($reqParam['update']) ? $reqParam['update'] : '',
        ];

        $update = $SystemPermission->switch($where, $param, $query);
        return $this->success(['switch' => $update]);
    }

    /**
 * edit
 * 修改信息
 *
 * @return \Psr\Http\Message\ResponseInterface
 *
 * @GetMapping(path="edit")
 */
    public function edit(SystemPermission $SystemPermission)
    {
        $reqParam = $this->request->all();
        $primaryKey = $SystemPermission->getKeyName();
        if ( ! isset($reqParam[$primaryKey])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新主键条件：' . $primaryKey);
        }
        $query = $SystemPermission->query();
        $where = [$primaryKey => $reqParam[$primaryKey]];

        $res = $SystemPermission->edit($where, $reqParam, $query);
        return $this->success();
    }

    /**
     * parent_classify
     * 获取父类树
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="parent_classify")
     */
    public function parent_classify(SystemPermission $SystemPermission)
    {
        $ruleList = $SystemPermission->query()->orderBy('order')->get()->toArray();
        foreach ($ruleList as $k => &$v) {
            $v['title'] = $v['display_name'];
            $v['remark'] = $v['description'];
        }
        unset($v);
        Tree::instance()->init($ruleList);
        $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
        $ruledata = [0 => '根目录'];
        foreach ($this->rulelist as $k => &$v) {
//            if (!$v['ismenu']) {   //非菜单类权限
//                continue;
//            }
            $ruledata[$v['id']] = $v['title'];
        }
        unset($v);
        return $this->success($ruledata);
    }

    /**
     * add
     * 添加菜单
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="add")
     */
    public function add(SystemPermission $SystemPermission)
    {
        $reqParam = $this->request->all();

        $query = $SystemPermission->query();

        $SystemPermission->add($reqParam, $query);

        return $this->success();
    }

    /**
     * navs
     * 获取当前用户拥有的权限的navs
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="navs")
     */
    public function navs(Auth $auth, AdminPermission $AdminPermission)
    {
        $userInfo = $auth->check();
        if (!isset($userInfo['id']) || !$userInfo['id']) {
            throw new BusinessException(StatusCode::ERR_NOT_LOGIN);
        }
        $userPermissions = $AdminPermission->getUserAllPermissionsNavs($userInfo['id']);

        return $this->success($userPermissions);
    }

    /**
     * getUserPermissions
     * 获取当前用户拥有的权限
     * User：YM
     * Date：2020/1/11
     * Time：下午2:47
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="user_permissions")
     */
    public function getUserPermissions()
    {
        $list = $this->permissionsRepo->getUserPermissionsList();

        $data = [
            'list' => $list
        ];

        return $this->success($data);
    }

    /**
     * index
     * 权限列表，权限管理
     * User：YM
     * Date：2020/2/4
     * Time：下午8:23
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="lists")
     */
    public function index()
    {

        $list = $this->permissionsRepo->getPermissionsList();

        $data = [
            'list' => $list
        ];

        return $this->success($data);
    }

    /**
     * store
     * User：YM
     * Date：2020/2/4
     * Time：下午9:05
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        try {
            $reqParam = $this->request->all();
            $id = $this->permissionsRepo->savePermissions($reqParam);

            return $this->success($id);
        } catch (Throwable $throwable) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,$throwable->getMessage());
        }
    }

    /**
     * getInfo
     * 根据id获取单条记录信息
     * User：YM
     * Date：2020/2/4
     * Time：下午9:04
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->permissionsRepo->getInfo($reqParam['id']);

        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * orderPermissions
     * 权限的拖拽排序
     * User：YM
     * Date：2020/2/4
     * Time：下午9:03
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @PostMapping(path="order")
     */
    public function orderPermissions()
    {
        $reqParam = $this->request->all();
        $this->permissionsRepo->orderPermissions($reqParam['ids']);

        return $this->success('ok');
    }

    /**
     * destroy
     * 删除权限
     * User：YM
     * Date：2020/2/4
     * Time：下午9:02
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->permissionsRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }



}