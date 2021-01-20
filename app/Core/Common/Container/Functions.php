<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 *​
 * common.php
 *
 * 公共函数，避免功能性函数重复书写
 * 书写规范，必须使用function_exists()方法判断
 *
 */

use Hyperf\Utils\Coroutine;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Server as SwooleServer;
use Hyperf\Utils\Context;
use Hyperf\Utils\ApplicationContext;
use Hyperf\HttpMessage\Cookie\Cookie as HyperfCookie;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Hyperf\Contract\SessionInterface;
use Jenssegers\Agent\Agent;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Utils\Arr;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Core\Common\Driver\CacheDriver;
use Core\Common\Container\Auth;
use Core\Common\Container\Ip2Region;
use Hyperf\Server\ServerFactory;

if (!function_exists('container')) {
    /**
     * 容器实例
     * container
     * @return \Psr\Container\ContainerInterface
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 22:57
     */
    function container()
    {
        return ApplicationContext::getContainer();
    }
}


if (!function_exists('requestEntry')) {
    /**
     * 根据异常返回信息，获取请求入口（模块-控制器-方法）
     * requestEntry
     * @param array $backTrace
     *
     * @return string|string[]
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 22:57
     */
    function requestEntry(array $backTrace)
    {
        $moduleName = '';
        foreach ($backTrace as $v) {
            if (isset($v['file']) && stripos($v['file'], 'CoreMiddleware.php')) {
                $tmp = array_reverse(explode('\\', trim($v['class'])));
                if (substr(strtolower($tmp[0]), -10) == 'controller') {
                    $module     = str_replace('controller', '', strtolower($tmp[1]));
                    $class      = str_replace('controller', '', strtolower($tmp[0]));
                    $function   = $v['function'];
                    $moduleName = $class . '-' . $function;
                    if ($module) {
                        $moduleName = $module . '-' . $moduleName;
                    }
                    break;
                }
            }
        }
        if (!$moduleName) {
            $request    = ApplicationContext::getContainer()->get(RequestInterface::class);
            $uri        = $request->getRequestUri();
            $moduleName = str_replace('/', '-', ltrim($uri, '/'));
        }
        $moduleName = $moduleName ?? 'hyperf';
        return $moduleName;
    }
}

if (!function_exists('getCoId')) {
    /**
     * 获取当前协程id
     * getCoId
     * @return int
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 22:57
     */
    function getCoId()
    {
        return Coroutine::id();
    }
}

if (!function_exists('getClientInfo')) {
    /**
     * 获取请求客户端信息，获取连接的信息
     * getClientInfo
     * @return mixed
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 22:57
     */
    function getClientInfo()
    {
        // 得从协程上下文取出请求
        $request = Context::get(ServerRequestInterface::class);
        $server  = make(SwooleServer::class);
        return $server->getClientInfo($request->getSwooleRequest()->fd);
    }
}

if (!function_exists('getServerLocalIp')) {
    /**
     * getServerLocalIp
     * 获取服务端内网ip地址
     * User：YM
     * Date：2019/12/19
     * Time：下午5:48
     * @return string
     */
    function getServerLocalIp()
    {
        $ip  = '127.0.0.1';
        $ips = array_values(swoole_get_local_ip());
        foreach ($ips as $v) {
            if ($v && $v != $ip) {
                $ip = $v;
                break;
            }
        }

        return $ip;
    }
}

if (!function_exists('setCookies')) {
    /**
     * setCookie
     * 设置cookie
     * User：YM
     * Date：2019/12/17
     * Time：下午12:16
     * @param string $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @param bool $raw
     * @param null|string $sameSite
     */
    function setCookies(string $key, $value = '', $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true, bool $raw = false, ?string $sameSite = null)
    {
        // convert expiration time to a Unix timestamp
        if ($expire instanceof \DateTimeInterface) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $expire = strtotime($expire);
            if ($expire === false) {
                throw new \RuntimeException('The cookie expiration time is not valid.');
            }
        }
        $response = ApplicationContext::getContainer()->get(ResponseInterface::class);
        $cookie   = new HyperfCookie($key, (string)$value, $expire, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
        $response = $response->withCookie($cookie);
        Context::set(PsrResponseInterface::class, $response);
        return;
    }
}

if (!function_exists('getCookie')) {
    /**
     * getCookie
     * 获取cookie
     * User：YM
     * Date：2019/12/17
     * Time：下午12:17
     * @param string $key
     * @param null|string $default
     * @return mixed
     */
    function getCookie(string $key, ?string $default = null)
    {
        $request = ApplicationContext::getContainer()->get(RequestInterface::class);
        return $request->cookie($key, $default);
    }
}

if (!function_exists('hasCookie')) {
    /**
     * hasCookie
     * 判断cookie是否存在
     * User：YM
     * Date：2019/12/17
     * Time：下午12:20
     * @param string $key
     * @return mixed
     */
    function hasCookie(string $key)
    {
        $request = ApplicationContext::getContainer()->get(RequestInterface::class);
        return $request->hasCookie($key);
    }
}

if (!function_exists('delCookie')) {
    /**
     * delCookie
     * 删除cookie
     * User：YM
     * Date：2019/12/17
     * Time：下午12:21
     * @param string $key
     * @return bool
     */
    function delCookie(string $key): bool
    {
        if (!hasCookie($key)) {
            return false;
        }

        setCookies($key, '', time() - 1);

        return true;
    }
}

if (!function_exists('setSessionId')) {
    /**
     * setSessionId
     * 设置sessionid
     * User：YM
     * Date：2019/12/19
     * Time：下午6:56
     * @param string $id
     */
    function setSessionId(string $id)
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        $session->setId($id);
        return;
    }
}

if (!function_exists('getSessionId')) {
    /**
     * getSessionId
     * 获取sessionid
     * User：YM
     * Date：2019/12/19
     * Time：下午6:56
     */
    function getSessionId()
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        return $session->getId();
    }
}

if (!function_exists('setSession')) {
    /**
     * setSession
     * 设置session
     * User：YM
     * Date：2019/12/19
     * Time：下午5:59
     * @param string $k
     * @param $v
     */
    function setSession(string $k, $v)
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        $session->set($k, $v);
        return;
    }
}

if (!function_exists('getSession')) {
    /**
     * getSession
     * 获取session
     * User：YM
     * Date：2019/12/19
     * Time：下午7:32
     * @param string $k
     * @param null $default
     * @return mixed
     */
    function getSession(string $k, $default = null)
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        return $session->get($k, $default = 0);
    }
}

if (!function_exists('getAllSession')) {
    /**
     * getAllSession
     * 获取所有session
     * User：YM
     * Date：2019/12/19
     * Time：下午7:32
     * @return mixed
     */
    function getAllSession()
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        return $session->all();
    }
}


if (!function_exists('hasSession')) {
    /**
     * hasSession
     * 判断session是否存在
     * User：YM
     * Date：2019/12/19
     * Time：下午7:52
     * @param string $name
     * @return bool
     */
    function hasSession(string $name)
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        return $session->has($name);
    }
}

if (!function_exists('removeSession')) {
    /**
     * removeSession
     * 从 Session 中获取并删除一条数据
     * User：YM
     * Date：2019/12/19
     * Time：下午7:54
     * @param string $name
     * @return mixed
     */
    function removeSession(string $name)
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        return $session->remove($name);
    }
}

if (!function_exists('forgetSession')) {
    /**
     * forgetSession
     * 从session中删除一条或多条数据
     * User：YM
     * Date：2019/12/19
     * Time：下午7:54
     * @param $keys string|array
     */
    function forgetSession($keys)
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        $session->forget($keys);
        return;
    }
}

if (!function_exists('clearSession')) {
    /**
     * clearSession
     * 清空当前 Session 里的所有数据
     * User：YM
     * Date：2019/12/19
     * Time：下午7:56
     */
    function clearSession()
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        return $session->clear();
    }
}

if (!function_exists('destroySession')) {
    /**
     * destroySession
     * 销毁session
     * User：YM
     * Date：2019/12/19
     * Time：下午7:56
     */
    function destroySession()
    {
        $session = ApplicationContext::getContainer()->get(SessionInterface::class);
        return $session->invalidate();
    }
}

if (!function_exists('verifyIp')) {
    function verifyIp($realip)
    {
        return filter_var($realip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}

if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        /**
         * @var ServerRequestInterface $request
         */
        $request = Context::get(ServerRequestInterface::class);
        $ip_addr = $request->getHeaderLine('x-forwarded-for');
        if (verifyIp($ip_addr)) {
            return $ip_addr;
        }
        $ip_addr = $request->getHeaderLine('remote-host');
        if (verifyIp($ip_addr)) {
            return $ip_addr;
        }
        $ip_addr = $request->getHeaderLine('x-real-ip');
        if (verifyIp($ip_addr)) {
            return $ip_addr;
        }
        $ip_addr = $request->getServerParams()['remote_addr'] ?? '0.0.0.0';
        if (verifyIp($ip_addr)) {
            return $ip_addr;
        }
        return '0.0.0.0';
    }
}


if (!function_exists('getLogArguments')) {
    /**
     * getLogArguments
     * 获取要存储的日志部分字段，monolog以外的业务信息
     *
     * @param float $executionTime 程序执行时间，运行时才能判断这里初始化为0
     * @param int $rbs 响应包体大小，初始化0，只有正常请求响应才有值
     * @param array $data 响应内容
     * @return array
     */
    function getLogArguments($executionTime = null, $rbs = 0, array $data)
    {
        $request        = ApplicationContext::getContainer()->get(RequestInterface::class);
        $requestHeaders = $request->getHeaders();
        $serverParams   = $request->getServerParams();
        $arguments      = $request->all();
        if (isset($arguments['password'])) {
            unset($arguments['password']);
        }

        $auth   = ApplicationContext::getContainer()->get(Auth::class);
        $userId = $auth->check(false);
        $uuid   = getCookie('HYPERF_SESSION_ID');
        $url    = $request->fullUrl();

        $agent = new Agent();
        $agent->setUserAgent($requestHeaders['user-agent'][0] ?? '');
        $ip = $requestHeaders['x-real-ip'][0] ?? $requestHeaders['x-forwarded-for'][0] ?? '';
        // ip转换地域
        if ($ip && ip2long($ip) != false) {
            $location = getIpLocation($ip);
            $cityId   = $location['city_id'] ?? 0;
        } else {
            $cityId = 0;
        }

        return [
            'qid'                => $requestHeaders['qid'][0] ?? '',
            'server_name'        => $requestHeaders['host'][0] ?? '',
            'server_addr'        => getServerLocalIp() ?? '',
            'remote_addr'        => $serverParams['remote_addr'] ?? '',
            'forwarded_for'      => $requestHeaders['x-forwarded-for'][0] ?? '',
            'real_ip'            => $ip,
            'city_id'            => $cityId,
            'user_agent'         => $requestHeaders['user-agent'][0] ?? '',
            'platform'           => $agent->platform() ?? '',
            'device'             => $agent->device() ?? '',
            'browser'            => $agent->browser() ?? '',
            'url'                => $url,
            'uri'                => $serverParams['request_uri'] ?? '',
            'arguments'          => $arguments ? json_encode($arguments) : '',
            'method'             => $serverParams['request_method'] ?? '',
            'execution_time'     => $executionTime,
            'request_body_size'  => $requestHeaders['content-length'][0] ?? 0,
            'response_body_size' => $rbs,
            'uuid'               => $uuid,
            'user_id'            => $userId ?? '',
            'referer'            => $requestHeaders['referer'][0] ?? '',
            'unix_time'          => $serverParams['request_time'] ?? '',
            'time_day'           => isset($serverParams['request_time']) ? date('Y-m-d', $serverParams['request_time']) : '',
            'time_hour'          => isset($serverParams['request_time']) ? date('Y-m-d H:00:00', $serverParams['request_time']) : '',
            'code'               => isset($data['code']) ? $data['code'] : 0,
            'msg'                => isset($data['msg']) ? $data['msg'] : '',
            'data'               => isset($data['data']) ? json_encode($data['data']) : [],
        ];
    }
}

if (!function_exists('getIpLocation')) {
    /**
     * getIpLocation
     * 获取ip对应的城市信息
     * User：YM
     * Date：2020/2/19
     * Time：下午8:42
     * @param $ip
     * @return mixed
     */
    function getIpLocation($ip)
    {
        $dbFile       = BASE_PATH . '/app/Core/Common/Container/ip2region.db';
        $ip2regionObj = new Ip2Region($dbFile);
        $ret          = $ip2regionObj->binarySearch($ip);
        return $ret;
    }
}

if (!function_exists('isStdoutLog')) {
    /**
     * isStdoutLog
     * 判断日志类型是否允许输出
     * User：YM
     * Date：2019/12/21
     * Time：下午7:13
     * @param string $level
     * @return bool
     */
    function isStdoutLog(string $level)
    {
        $config = config(StdoutLoggerInterface::class, ['log_level' => []]);
        return in_array(strtolower($level), $config['log_level'], true);
    }
}

if (!function_exists('isMobileNum')) {
    /**
     * isMobileNum
     * 判断是否为手机号
     * User：YM
     * Date：2020/1/10
     * Time：上午12:21
     * @param $v
     * @return bool
     */
    function isMobileNum($v)
    {
        $search = '/^0?1[3-9][0-9]\d{8}$/';
        if (preg_match($search, $v)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('encryptPassword')) {
    /**
     * encryptPassword
     * 加密密码
     * @param string $password 用户输入的密码
     * @access public
     * @return void
     */
    function encryptPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

if (!function_exists('checkPassword')) {
    /**
     * checkPassword
     * 检测密码
     * User：YM
     * Date：2020/1/10
     * Time：下午12:48
     * @param $value
     * @param $hashedValue
     * @return bool
     */
    function checkPassword($value, $hashedValue)
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }
}

if (!function_exists('getUserUniqueId')) {
    /**
     * getUserUniqueId
     * 获取用户唯一标示，用户ID生成规则，32位
     * @access public
     * @return string
     * @throws Exception
     */
    function getUserUniqueId()
    {
        // 前缀3位
        $prefix = config('app_uid_prefix');
        $prefix = substr($prefix, 0, 3);
        //随机字符串14位
        $rand = substr(str_replace(['/', '+', '='], '', base64_encode(random_bytes(14))), 0, 14);
        //根据当前时间生成的随机字符串11位
        $uniqid = substr(uniqid(), 2);
        //当前服务器ip后4位
        $ip     = getServerLocalIp();
        $ipList = explode('.', $ip);
        if (empty($ipList) || count($ipList) < 4) {
            $ipStr = '01';
        } else {
            $ipStr = $ipList[2] . $ipList[3];
        }
        $ip = dechex($ipStr);
        $ip = str_pad($ip, 6, 'f', STR_PAD_LEFT);
        if (PHP_SAPI != 'cli') {
            $ip = substr($ip, -4);
        } else {
            $ip = 'z' . substr($ip, -3);
        }

        //总共32位字符串
        return strtolower($prefix . $ip . $rand . $uniqid);
    }
}

if (!function_exists('handleTreeList')) {
    /**
     * handleTreeList
     * 建立数组树结构列表
     *
     * @datetime 2019/1/8 下午5:56
     * @param $arr 数组
     * @param int $pid 父级id
     * @param int $depth 增加深度标识
     * @param string $p_sub 父级别名
     * @param string $d_sub 深度别名
     * @param string $c_sub 子集别名
     * @return array
     * @author YM
     * @access public
     */
    function handleTreeList($arr, $pid = 0, $depth = 0, $p_sub = 'parent_id', $c_sub = 'children', $d_sub = 'depth')
    {
        $returnArray = [];
        if (is_array($arr) && $arr) {
            foreach ($arr as $k => $v) {
                if ($v[$p_sub] == $pid) {
                    $v[$d_sub] = $depth;
                    $tempInfo  = $v;
                    unset($arr[$k]); // 减少数组长度，提高递归的效率，否则数组很大时肯定会变慢
                    $temp = handleTreeList($arr, $v['id'], $depth + 1, $p_sub, $c_sub, $d_sub);
                    if ($temp) {
                        $tempInfo[$c_sub] = $temp;
                    }
                    $returnArray[] = $tempInfo;
                }
            }
        }
        return $returnArray;
    }
}

if (!function_exists('recur')) {
    /**
     * 生成LayUI—tree
     * 参考：https://blog.csdn.net/qq_40205116/article/details/100180581
     * recur
     *
     * @param       $arrs
     * @param       $category
     * @param int $parent_id
     *
     * @return mixed
     * author MengShuai <133814250@qq.com>
     * date 2020/11/26 11:14
     */
    function recur($arrs, $category, $parent_id = 0)
    {
        $spread = false; //是否展开
        foreach ($category as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $arr             = [
                    'title'    => $v["display_name"],
                    'id'       => $v['id'],
                    'spread'   => $spread,
                    'children' => [],
                ];
                $arr['children'] = recur($arr["children"], $category, $v['id']);
                array_push($arrs, $arr);
            }
        }
        return $arrs;
    }
}


if (!function_exists('generateMenu')) {
    /**
     * 生成LayUI—tree
     * 参考：https://blog.csdn.net/qq_40205116/article/details/100180581
     * recur
     *
     * @param       $arrs
     * @param       $category
     * @param int $parent_id
     *
     * @return mixed
     * author MengShuai <133814250@qq.com>
     * date 2020/11/26 11:14
     */
    function generateMenu($arrs, $category, $parent_id = 0)
    {
        $spread = false; //是否展开
        foreach ($category as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $arr             = [
                    'title'    => $v["display_name"],
                    'id'       => $v['id'],
                    'icon'     => $v['icon'],
                    'href'     => $v['effect_uri'],
                    'spread'   => $spread,
                    'children' => [],
                ];
                $arr['children'] = generateMenu($arr["children"], $category, $v['id']);
                array_push($arrs, $arr);
            }
        }
        return $arrs;
    }
}

if (!function_exists('array_pluck')) {
    /**
     * Pluck an array of values from an array.
     * 从数组中提取值组成新数组
     *
     * @param array $array
     * @param string|array $value
     * @param string|array|null $key
     * @return array
     */
    function array_pluck($array, $value, $key = null)
    {
        return Arr::pluck($array, $value, $key);
    }
}

if (!function_exists('flushAnnotationCache')) {
    /**
     * flushAnnotationCache
     * 刷新注解缓存，清楚注解缓存
     * User：YM
     * Date：2020/2/4
     * Time：下午12:13
     * @param string $listener
     * @param array $keys
     * @return bool
     */
    function flushAnnotationCache($listener = '', $keys = [])
    {
        if (!$listener || !$keys) {
            throw new \RuntimeException('参数不正确！');
        }
        $keys       = is_array($keys) ? $keys : [$keys];
        $dispatcher = ApplicationContext::getContainer()->get(EventDispatcherInterface::class);
        foreach ($keys as $key) {
            $dispatcher->dispatch(new DeleteListenerEvent($listener, [$key]));
        }
        return true;
    }
}

if (!function_exists('clearCache')) {
    /**
     * clearCache
     * 清空当前 缓存
     * User：YM
     * Date：2019/12/19
     * Time：下午7:56
     */
    function clearCache()
    {
        $config = config('cache.default');
        $cache  = make(CacheDriver::class, ['config' => $config]);
        return $cache->clear();
    }
}

if (!function_exists('delCache')) {
    /**
     * delCache
     * 删除缓存，1条/多条
     * User：YM
     * Date：2020/2/4
     * Time：下午4:25
     * @param array $keys
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    function delCache($keys = [])
    {
        $config = config('cache.default');
        $cache  = make(CacheDriver::class, ['config' => $config]);
        if (is_array($keys)) {
            $cache->deleteMultiple($keys);
        } else {
            $cache->delete($keys);
        }

        return true;
    }
}

if (!function_exists('clearPrefixCache')) {
    /**
     * clearPrefixCache
     * 根据前缀清楚缓存
     * 函数的含义说明
     * User：YM
     * Date：2020/2/4
     * Time：下午4:32
     * @param string $prefix
     * @return bool
     */
    function clearPrefixCache($prefix = '')
    {
        $config = config('cache.default');
        $cache  = make(CacheDriver::class, ['config' => $config]);
        $cache->clearPrefix($prefix);
        return true;
    }
}

if (!function_exists('setCache')) {
    /**
     * setCache
     * 设置缓存
     * User：YM
     * Date：2020/2/4
     * Time：下午4:58
     * @param $key
     * @param $value
     * @param null $ttl
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    function setCache($key, $value, $ttl = null)
    {
        $config = config('cache.default');
        $cache  = make(CacheDriver::class, ['config' => $config]);
        return $cache->set($key, $value, $ttl);
    }
}

if (!function_exists('setMultipleCache')) {
    /**
     * setMultipleCache
     * 批量设置缓存
     * User：YM
     * Date：2020/2/4
     * Time：下午4:59
     * @param $values
     * @param null $ttl
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    function setMultipleCache($values, $ttl = null)
    {
        $config = config('cache.default');
        $cache  = make(CacheDriver::class, ['config' => $config]);
        return $cache->setMultiple($values, $ttl);
    }
}

if (!function_exists('getCache')) {
    /**
     * getCache
     * 获取缓存
     * User：YM
     * Date：2020/2/4
     * Time：下午5:37
     * @param $key
     * @param null $default
     * @return iterable
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    function getCache($key, $default = null)
    {
        $config = config('cache.default');
        $cache  = make(CacheDriver::class, ['config' => $config]);
        return $cache->get($key, $default);
    }
}

if (!function_exists('getMultipleCache')) {
    /**
     * getMultipleCache
     * 获取多个缓存
     * User：YM
     * Date：2020/2/4
     * Time：下午5:37
     * @param array $keys
     * @param null $default
     * @return iterable
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    function getMultipleCache(array $keys, $default = null)
    {
        $config = config('cache.default');
        $cache  = make(CacheDriver::class, ['config' => $config]);
        return $cache->getMultiple($keys, $default);
    }
}

if (!function_exists('formatBytes')) {
    /**
     * formatBytes
     * 字节->兆转换
     * 字节格式化
     * User：YM
     * Date：2020/2/15
     * Time：下午7:29
     * @param $bytes
     * @return string
     */
    function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
        } elseif ($bytes >= 1048576) {
            $bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
        } elseif ($bytes >= 1024) {
            $bytes = round($bytes / 1024 * 100) / 100 . 'KB';
        } else {
            $bytes = $bytes . 'Bytes';
        }
        return $bytes;
    }
}

if (!function_exists('durationFormat')) {
    /**
     * durationFormat
     * 时间格式化，格式化秒
     * User：YM
     * Date：2020/2/15
     * Time：下午10:33
     * @param $number
     * @return string
     */
    function durationFormat($number)
    {
        if (!$number) {
            return '0分钟';
        }
        $newTime = '';
        if (floor($number / 3600) > 0) {
            $newTime .= floor($number / 3600) . '小时';
            $number  = $number % 3600;
        }
        if ($number / 60 > 0) {
            $newTime .= floor($number / 60) . '分钟';
            $number  = $number % 60;
        }
        if ($number < 60) {
            $newTime .= $number . '秒';
        }
        return $newTime;
    }
}


if (!function_exists('filterParams')) {
    /**
     * 请求参数过滤
     * filterParams
     * @param array $data
     * @return array
     * @author MengShuai <133814250@qq.com>
     * @date 2020/11/03 12:55
     */
    function filterParams(array $data): array
    {
        if (empty($data)) {
            return $data;
        }

        /**
         * 兼容ok框架
         */
        if (isset($data['page'])) {
            $data['current_page'] = $data['page'];
            unset($data['page']);
        }
        if (isset($data['limit'])) {
            $data['page_size'] = $data['limit'];
            unset($data['limit']);
        }

        return $data;
    }

}

if (!function_exists('toArr')) {
    /**
     * 将对象转为数组
     * toArr
     * @param $arr
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/11/27 21:03
     */
    function toArr($arr): array
    {
        return json_decode(json_encode($arr), true);
    }
}

if (!function_exists('redis')) {
    /**
     * 获取redis连接池对象
     * @return \Hyperf\Redis\Redis|null
     */
    function redis(): ?\Hyperf\Redis\Redis
    {
        $container = ApplicationContext::getContainer();
        $redis     = $container->get(\Hyperf\Redis\Redis::class);
        return $redis;
    }
}

if (!function_exists('buildStringHash')) {
    /**
     * 将字串符生成hash
     * buildStringHash
     * @param string $data
     * @return string
     */
    function buildStringHash(string $data): string
    {
        $data = hash('ripemd160', base64_encode(trim($data)));
        return $data;
    }
}

if (!function_exists('isIdCard')) {
    /**
     * 身份证号验证
     * isIdCard
     * @param string $id
     *
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 17:10
     */
    function isIdCard(string $id)
    {
        $id        = strtoupper($id);
        $regx      = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = [];
        if (!preg_match($regx, $id)) {
            return false;
        }
        if (15 == strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return false;
            } else {
                return true;
            }
        } else      //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return false;
            } else {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int =
                    [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                $arr_ch  =
                    ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
                $sign    = 0;
                for ($i = 0; $i < 17; $i++) {
                    $b    = (int)$id[$i];
                    $w    = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n       = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id, 17, 1)) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
}

if (!function_exists('getIdCardAge')) {
    /**
     * 根据身份证号码获取年龄
     * getIdCardAge
     * @param string $card_id
     *
     * @return int
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 17:11
     */
    function getIdCardAge(string $card_id): int
    {
        if (strlen($card_id) < 15) {
            return 0;
        }
        $age = date('Y') - substr($card_id, 6, 4) + (date('md') >= substr($card_id, 10, 4) ? 0 : -1);
        return (int)$age;
    }
}
if (!function_exists('isMobile')) {
    /**
     * 验证是否是正确的手机号
     * isMobile
     * @param string $value
     *
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 17:12
     */
    function isMobile(string $value): bool
    {
        $rule   = '/^0?(13|14|15|16|17|18|19)[0-9]{9}$/';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('isChineseName')) {
    /**
     * 判断中文姓名是否正确
     * isChineseName
     * @param string $name
     *
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 17:12
     */
    function isChineseName(string $name): bool
    {
        if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('OrderThreeInspect')) {
    /**
     * 订单三要素格式检查
     * OrderThreeInspect
     * @param string $name
     * @param int $idCard
     * @param int $phone
     *
     * @return bool|string
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 17:16
     */
    function OrderThreeInspect(string $name, string $idCard, string $phone)
    {
        if (!isChineseName($name)) {
            return '请输入正确的姓名';
        }
        if (!isIdCard((string)$idCard)) {
            return '请输入正确的身份证号码：' . (string)$idCard;
        }
        if (!isMobile($phone)) {
            return '请输入正确的手机号';
        }
        return true;
    }
}

if (!function_exists('setLog')) {
    /**
     * 打印单独的错误日志
     * @param string $path
     * @param string $content
     */
    function setLog(string $path, string $content): void
    {
        co(function () use ($path, $content) {
            co(function () use ($path, $content) {
                file_put_contents(
                    BASE_PATH . '/runtime/logs/' . $path,
                    '[' . date('Y-m-d H:i:s') . '] ' . $content . "\r\n",
                    FILE_APPEND
                );
            });
        });
    }
}

if (!function_exists('str_rand')) {
    /**
     * 随机字串符
     * str_rand
     * @param int $length
     * @param string $char
     *
     * @return string
     * author MengShuai <133814250@qq.com>
     * @date 2020/12/26 22:49
     */
    function str_rand(int $length = 32, string $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        if (!is_int($length) || $length < 0) {
            return '';
        }
        $string = '';
        for ($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }
        return $string;
    }
}
if (!function_exists('isHTTPS')) {
    /**
     * 判断url是否为https
     * isHTTPS
     * @param string|null $url
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2021/01/18 17:00
     */
    function isHTTPS(string $url = null): bool
    {
        if ($url === null) {
            $request = ApplicationContext::getContainer()->get(RequestInterface::class);
            $url     = $request->fullUrl();
        }
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        if (substr($url, 0, 5) !== 'https') {
            return false;
        }
        return true;
    }
}