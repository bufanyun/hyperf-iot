<?php
/**
 * Created by PhpStorm.
 *​
 * ReqResponse.php
 *
 * User：YM
 * Date：2019/11/15
 * Time：下午5:35
 */


namespace Core\Common\Container;

use CharlotteDunois\Validation\Rules\JSON;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpMessage\Cookie\Cookie;
use App\Constants\StatusCode;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * ReqResponse
 * 请求响应结果
 *
 * @package Core\Common\Container
 * User：YM
 * Date：2019/11/15
 * Time：下午5:35
 */
class Response
{

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * 成功返回请求结果
     * success
     * @param  array  $res
     * @param  string|null  $msg
     *
     * @return \Psr\Http\Message\ResponseInterface
     * author MengShuai <133814250@qq.com>
     * date 2021/01/04 17:37
     */
    public function success($res = [], string $msg = null)
    {
        $msg = $msg ?? StatusCode::getMessage(StatusCode::SUCCESS);
        if (isset($res['count']) && isset($res['list'])) { //兼容ok列表模式
            $data = [
                'count' => $res['count'],
                'data'  => $res['list'],
            ];
        } else {
            $data = [
                'data' => $res,
            ];
        }
        $data = array_merge(
            $data,
            [
                'code' => StatusCode::SUCCESS,
                'msg'  => $msg,
            ]
        );
        return $this->response->json($data);
    }

    /**
     * 业务相关错误结果返回
     * error
     * @param  int  $code
     * @param  string|null  $msg
     *
     * @return \Psr\Http\Message\ResponseInterface
     * author MengShuai <133814250@qq.com>
     * date 2021/01/04 17:37
     */
    public function error(
        int $code = StatusCode::ERR_EXCEPTION,
        string $msg = null
    ) {
        $msg = $msg ?? StatusCode::getMessage(StatusCode::ERR_EXCEPTION);
        $data = [
            'code' => $code,
            'msg'  => $msg,
            'data' => [],
        ];
        return $this->response->json($data);
    }

    /**
     * json
     * 直接返回数据
     * User：YM
     * Date：2019/12/16
     * Time：下午4:22
     *
     * @param $data
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function json(array $data)
    {
        return $this->response->json($data);
    }

    /**
     * xml
     * 返回xml数据
     * User：YM
     * Date：2019/12/16
     * Time：下午4:58
     *
     * @param $data
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function xml(array $data)
    {
        return $this->response->xml($data);
    }

    /**
     * redirect
     * 重定向
     * User：YM
     * Date：2019/12/16
     * Time：下午5:00
     *
     * @param  string  $url
     * @param  string  $schema
     * @param  int  $status
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function redirect(
        string $url,
        string $schema = 'http',
        int $status = 302
    ) {
        return $this->response->redirect($url, $status, $schema);
    }

    /**
     * download
     * 下载文件
     * User：YM
     * Date：2019/12/16
     * Time：下午5:04
     *
     * @param  string  $file
     * @param  string  $name
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function download(string $file, string $name = '')
    {
        return $this->response->redirect($file, $name);
    }

    /**
     * cookie
     * 设置cookie
     * User：YM
     * Date：2019/12/16
     * Time：下午10:17
     *
     * @param  string  $name
     * @param  string  $value
     * @param  int  $expire
     * @param  string  $path
     * @param  string  $domain
     * @param  bool  $secure
     * @param  bool  $httpOnly
     * @param  bool  $raw
     * @param  null|string  $sameSite
     */
    public function cookie(
        string $name,
        string $value = '',
        $expire = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = true,
        bool $raw = false,
        ?string $sameSite = null
    ) {
        // convert expiration time to a Unix timestamp
        if ($expire instanceof \DateTimeInterface) {
            $expire = $expire->format('U');
        } elseif ( ! is_numeric($expire)) {
            $expire = strtotime($expire);
            if ($expire === false) {
                throw new \RuntimeException('The cookie expiration time is not valid.');
            }
        }

        $cookie = new Cookie($name, $value, $expire, $path, $domain, $secure,
            $httpOnly, $raw, $sameSite);
        $response = $this->response->withCookie($cookie);
        Context::set(PsrResponseInterface::class, $response);
        return;
    }

}