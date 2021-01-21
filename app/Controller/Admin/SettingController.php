<?php

declare(strict_types=1);

namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Models\Setting;
use Hyperf\Di\Annotation\Inject;

/**
 * SettingController
 * 基础配置控制器
 *
 * @package App\Controller\Admin
 *
 * @Controller(prefix="admin_api/setting")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\SettingRepository $settingRepo
 */
class SettingController extends BaseController
{

    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     *
     * @Inject()
     * @var Setting
     */
    private $model;

    /**
     * list
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $groups = $this->model->query()->select('group')->groupBy('group')->get()->toArray();
        if (!empty($groups)) {
            foreach ($groups as $k => $v) {
                $groups[$k]['title'] = __("admin_setting.{$v['group']}");
                $groups[$k]['lists'] = $this->model->query()->select('*')->where(['group' => $v['group']])->get()->toArray();
                foreach ($groups[$k]['lists'] as $kk => $vv) {
                    if ($vv['type'] === 'array') {
                        $value  = json_decode($vv['value'], true);
                        $values = [];
                        $i      = 0;
                        foreach ($value as $k3 => $v3) {
                            $values[$i]['key']   = $k3;
                            $values[$i]['value'] = $v3;
                            $i++;
                        }
                        $groups[$k]['lists'][$kk]['value'] = $values;
                        unset($v3);
                    }
                    if($vv['type'] === 'switch')
                    {
                        $groups[$k]['lists'][$kk]['value'] = $vv['value']===1 ?? false;
                    }
                }
                unset($vv);
            }
            unset($v);
        }

        $result = ['groups' => $groups];
        return $this->success($result);
    }

    /**
     * siteSet
     * 获取站点信息
     * User：YM
     * Date：2020/2/5
     * Time：下午5:55
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="site_set")
     */
    public function siteSet()
    {
        $info = $this->settingRepo->getSiteInfo();
        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * siteSave
     * 保存站点设置信息
     * User：YM
     * Date：2020/2/5
     * Time：下午5:55
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="site_save")
     */
    public function siteSave()
    {
        $reqParam = $this->request->all();
        $this->settingRepo->saveSettingInfo($reqParam);

        return $this->success('ok');
    }
}