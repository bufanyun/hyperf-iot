<?php
declare(strict_types=1);

namespace App\Controller;

use App\Constants\StatusCode;
use Core\Common\Facade\Log;
use Hyperf\Utils\Context;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Amqp\Producer;
use App\Amqp\Producer\LogsProducer;

/**
 * 基础类的控制器
 * Class BaseController
 *
 * @package App\Controller
 * author MengShuai <133814250@qq.com>
 * date 2021/01/19 23:07
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
     * 成功返回请求结果
     * success
     * @param array $data
     * @param string|null $msg
     * @return \Psr\Http\Message\ResponseInterface
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 09:36
     */
    public function success($data = [], string $msg = null)
    {
        $msg = $msg ?? StatusCode::getMessage(StatusCode::SUCCESS);
        $data = ['code' => StatusCode::SUCCESS, 'msg' => $msg, 'data' => $data];
        $response = $this->response->json($data);
        $executionTime = microtime(true) - Context::get('request_start_time');
        $rbs = strlen($response->getBody()->getContents());
        // 获取日志实例，记录日志
//        $logger = ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get('success','response');
//        $logger->info($msg, getLogArguments($executionTime, $rbs)+['data' => $data]);
        //记录日志
        $message  = new LogsProducer(getLogArguments($executionTime, $rbs, $data));
        $producer = ApplicationContext::getContainer()->get(Producer::class);
        $producer->produce($message);
        return $response;
    }

    /**
     * 业务相关错误结果返回
     * error
     * @param int $code
     * @param null $msg
     * @return \Psr\Http\Message\ResponseInterface
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 09:36
     */
    public function error($code = StatusCode::ERR_EXCEPTION, $msg = null)
    {
        $msg = $msg ?? StatusCode::getMessage(StatusCode::ERR_EXCEPTION);
        $data = ['code' => $code, 'msg' => $msg, 'data' => []];
        $response = $this->response->json($data);
        $executionTime = microtime(true) - Context::get('request_start_time');
        $rbs = strlen($response->getBody()->getContents());
        // 获取日志实例，记录日志
        $logger = ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get('error','response');
        $logger->error($msg, getLogArguments($executionTime, $rbs));
        //记录日志
        $message  = new LogsProducer(getLogArguments($executionTime, $rbs, $data));
        $producer = ApplicationContext::getContainer()->get(Producer::class);
        $producer->produce($message);
        return $response;
    }
}