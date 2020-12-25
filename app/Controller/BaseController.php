<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 *​
 * BaseController.php
 *
 * 基础控制器
 *
 * User：YM
 * Date：2019/11/14
 * Time：上午9:53
 */


namespace App\Controller;

use App\Constants\StatusCode;
use Core\Common\Facade\Log;
use Hyperf\Utils\Context;
use Hyperf\Utils\Coroutine;

/**
 * BaseController
 * 基础类的控制器
 * @package App\Controller
 * User：YM
 * Date：2019/11/14
 * Time：上午9:53
 */
class BaseController extends AbstractController
{

    /**
     * __get
     * 隐式注入仓库类
     * User：YM
     * Date：2019/11/21
     * Time：上午9:27
     * @param $key
     * @return \Psr\Container\ContainerInterface|void
     */
    public function __get($key)
    {
        if ($key == 'app') {
            return $this->container;
        } else {
            $suffix = strstr($key,'Repo');
            if ($suffix && ($suffix == 'Repo' || $suffix == 'Repository')) {
                $repoName = $suffix == 'Repo' ? $key.'sitory':$key;
                return $this->getRepositoriesInstance($repoName);
            } else {
                throw new \RuntimeException("仓库{$key}不存在，书写错误！", StatusCode::ERR_SERVER);
            }
        }
    }

    /**
     * getRepositoriesInstance
     * 获取仓库类实例
     * User：YM
     * Date：2019/11/21
     * Time：上午10:30
     * @param $key
     * @return mixed
     */
    public function getRepositoriesInstance($key)
    {
        $key = ucfirst($key);
        $module = $this->getModuleName();
        if (!empty($module)) {
            $module = "{$module}";
        } else {
            $module = "";
        }
        if ($module) {
            $filename = BASE_PATH."/app/Core/Repositories/{$module}/{$key}.php";
            $className = "Core\\Repositories\\{$module}\\{$key}";
        } else {
            $filename = BASE_PATH."/app/Core/Repositories/{$key}.php";
            $className = "Core\\Repositories\\{$key}";
        }
//        var_export([
//            '$key' => $key,
//            '$module' => $module,
//            '$filename' => $filename,
//            'file_exists($filename)' => file_exists($filename),
//        ]);
        if (file_exists($filename)) {
            return $this->container->get($className);
        } else {
            throw new \RuntimeException("仓库{$key}不存在，文件不存在!！", StatusCode::ERR_SERVER);
        }
    }

    /**
     * getModuleName
     * 获取所属模块
     * User：YM
     * Date：2019/11/21
     * Time：上午9:32
     * @return string
     */
    private function getModuleName()
    {
        $className = get_called_class();
        $name = substr($className, 15);
        $space = explode('\\', $name);
        if(count($space) > 1){
            return $space[0];
        }else{
            return '';
        }
    }

    /**
     * success
     * 成功返回请求结果
     * User：YM
     * Date：2019/11/20
     * Time：下午3:56
     * @param array $data
     * @param null $msg
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function success($data = [], string $msg = null)
    {

        return $this->response->success($data, $msg);

        $msg = $msg ?? StatusCode::getMessage(StatusCode::SUCCESS);
        $data = ['code' => StatusCode::SUCCESS, 'msg' => $msg, 'data' => $data];
        $response = $this->response->json($data);
        $executionTime = microtime(true) - Context::get('request_start_time');
        $rbs = strlen($response->getBody()->getContents());
        // 获取日志实例，记录日志
        $logger = Log::get(requestEntry(Coroutine::getBackTrace()));
        $logger->info($msg, getLogArguments($executionTime, $rbs));
        return $response;
    }

    /**
     * error
     * 业务相关错误结果返回
     * User：YM
     * Date：2019/11/20
     * Time：下午3:56
     * @param int $code
     * @param null $msg
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function error($code = StatusCode::ERR_EXCEPTION, $msg = null)
    {

        return $this->response->error($code, $msg);
        $msg = $msg ?? StatusCode::getMessage(StatusCode::ERR_EXCEPTION);
        $data = ['code' => $code, 'msg' => $msg, 'data' => []];
        $response = $this->response->json($data);
        $executionTime = microtime(true) - Context::get('request_start_time');
        $rbs = strlen($response->getBody()->getContents());
        // 获取日志实例，记录日志
        $logger = Log::get(requestEntry(Coroutine::getBackTrace()));
        $logger->error($msg, getLogArguments($executionTime, $rbs));
        return $response;
    }



}