<?php
/**
 * Created by PhpStorm.
 *​
 * TestController.php
 *
 * User：YM
 * Date：2019/11/13
 * Time：上午11:32
 */


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

/**
 * TestController
 * 测试用！！！
 * @package App\Controller\Admin
 * User：YM
 * Date：2019/11/13
 * Time：上午11:32
 *
 * @AutoController(prefix="admin_api/test")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class TestController extends BaseController
{

    /**
     *
     * @Inject()
     * @var IpRegion
     */
    private $ipRegionModel;
    /**
     * biaoge2
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="curd2")
     */
    public function curd2(\App\Models\User $User)
    {
        $reqParam = $this->request->all();
        $query = $User->query();
        [$querys, $sort, $order, $offset, $limit] = $User->buildTableParams($reqParam, $query);
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
        if(!empty($list)){
            foreach ($list as $k => $v) {
                $list[$k]['status'] = $v['status'] === 0 ? false : true;
            }
            unset($v);
        }
        $result = array("total" => $total, "rows" => $list);
        return $this->success($result);
    }

    /**
     * switch
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="switch")
     */
    public function switch(\App\Models\User $Model)
    {
        $reqParam = $this->request->all();
        if ( ! isset($reqParam['key'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新开关的参数');
        }
        $primaryKey = $Model->getKeyName();
        if ( ! isset($reqParam[$primaryKey])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新开关的条件');
        }
        $query = $Model->query();
        $where = [$primaryKey => $reqParam[$primaryKey]];
        $param = [
            'key'    => $reqParam['key'],
            'update' => isset($reqParam['update']) ? $reqParam['update'] : '',
        ];

        $update = $Model->switch($where, $param, $query);
        return $this->success(['switch' => $update]);
    }

    /**
     * index
     * 分类列表
     * User：YM
     * Date：2020/2/11
     * Time：下午4:57
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {

        $user = $this->request->input('user', 'Hyperf2');
        $method = $this->request->getMethod();
        $ipRegion = Db::table('ip_region')->where('lat','')->get()->toArray();
//        $ipRegion = $this->ipRegionModel->getList();
        $lbs = make(Lbs::class);
        $parallel = new Parallel(150);
        foreach ($ipRegion as $v) {
            $parallel->add(function () use ($lbs,$v) {
            $tmp = $lbs->getGeoCoding($v->name);
            $tmp = json_decode($tmp,true);
            return [
                'id' => $v->id,
                'name' => $v->name,
                'lng' =>  $tmp['result']['location']['lng']??'',
                'lat' =>  $tmp['result']['location']['lat']??''
            ];
        });
        }
        
        $results = $parallel->wait();
        $sql = "INSERT INTO ymkj_ip_region(`id`,`name`,`lng`,`lat`) values";
        $sqlValue = [];
        foreach ($results as $v) {
            $sqlValue[] = "({$v['id']},'{$v['name']}','{$v['lng']}','{$v['lat']}')";
        }
        $sql .= implode(',',$sqlValue);
        $sql .= " ON DUPLICATE KEY UPDATE lng=VALUES(lng),lat=VALUES(lat)";
//        sort()
        Db::select($sql);
        $data = [
            'method-test' => $method,
            'message' => "Hello {$user}.",
            'test' => ['q','b'],
            'count' => count($results),
//            'ip_region' => $results,
            'sql' => $sql,
        ];

        return $this->success($data);
    }


    public function index1()
    {
        $lbs = make(Lbs::class);
        $parallel = new Parallel(500);
        for ($i = 0; $i < 50000; $i++) {
            $parallel->add(function () use ($lbs) {
                $tmp = $lbs->getGeoCoding('北京');
                $tmp = json_decode($tmp,true);
                return [
                    'name' => '北京',
                    'lng' =>  $tmp['result']['location']['lng']??'',
                    'lat' =>  $tmp['result']['location']['lat']??''
                ];
            });
        }
        $results = $parallel->wait();

        $data = [
            'count' => count($results),
            'count' => $results,
        ];

        return $this->success($data);
    }


}