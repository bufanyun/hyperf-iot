<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory as GuzzleClientFactory;

/**
 * OssCallbackMiddleware
 * oss文件上传后回调
 * @package App\Middleware
 * User：YM
 * Date：2020/2/7
 * Time：下午8:26
 */
class OssCallbackMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var Closure
     */
    private $client;

    public function __construct(ContainerInterface $container,ServerRequestInterface $request)
    {
        $this->container = $container;
        $this->request = $request;
        $this->config = $container->get(ConfigInterface::class);
        $this->client = $container->get(GuzzleClientFactory::class)->create();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $signStatus = $this->checkSign();
        if ($signStatus === false) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'签名验证失败！');
        }
        return $handler->handle($request);
    }

    /**
     * checkSign
     * 验证签名
     * 函数的含义说明
     * User：YM
     * Date：2020/2/7
     * Time：下午10:00
     * @return bool
     */
    public function checkSign()
    {
        $authorization = $this->getAuthorization();
        $pubKeyUrl = $this->getPubKeyUrl();
        if ($authorization == '' || $pubKeyUrl == '') {
            return false;
        }
        $pubKey = $this->getPubKey($pubKeyUrl);
        if(!$pubKey){
            return false;
        };
        $authStr = $this->getAuthStr();
        // 6.验证签名
        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
        if ($ok == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * getAuthorization
     * 获取授权信息
     * User：YM
     * Date：2020/2/7
     * Time：下午9:13
     * @return bool|string
     */
    private function getAuthorization()
    {
        $authorizationBase64 = $this->request->getHeader('AUTHORIZATION');
        if(isset($authorizationBase64[0]) && $authorizationBase64[0]){
            return base64_decode($authorizationBase64[0]);
        }else{
            return '';
        }
    }

    /**
     * getPubKeyUrl
     * 获取秘钥地址
     * User：YM
     * Date：2020/2/7
     * Time：下午9:23
     * @return bool|string
     */
    private function getPubKeyUrl()
    {
        $pubKeyUrlBase64 = $this->request->getHeader('X-OSS-PUB-KEY-URL');;
        if(isset($pubKeyUrlBase64[0]) && $pubKeyUrlBase64[0]){
            return base64_decode($pubKeyUrlBase64[0]);
        }else{
            return '';
        }
    }

    /**
     * getPubKey
     * 获取公钥
     * User：YM
     * Date：2020/2/7
     * Time：下午9:46
     * @param $pubKeyUrl
     * @return bool|ResponseInterface
     */
    public function getPubKey($pubKeyUrl)
    {
        $response = $this->client->request('GET',$pubKeyUrl);
        $pubKey = $response->getBody()->getContents();
        if ($pubKey == "") {
            return false;
        } else {
            return $pubKey;
        }
    }


    /**
     * getAuthStr
     * 获取签名字符串
     * User：YM
     * Date：2020/2/7
     * Time：下午9:56
     * @return string
     */
    public function getAuthStr()
    {
        $body = $this->request->getBody();
        $serverParams = $this->request->getServerParams();
        $path = $serverParams['request_uri'];
        $pos = strpos($path, '?');
        if ($pos === false) {
            $authStr = urldecode($path)."\n".$body;
        } else {
            $authStr = urldecode(substr($path, 0, $pos)).substr($path, $pos, strlen($path) - $pos)."\n".$body;
        }

        return $authStr;
    }

}