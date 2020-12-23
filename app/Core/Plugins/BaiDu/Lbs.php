<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * Lbs.php
 *
 * User：YM
 * Date：2020/2/21
 * Time：下午11:00
 */


namespace Core\Plugins\BaiDu;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Hyperf\Guzzle\ClientFactory as GuzzleClientFactory;
use Hyperf\Guzzle\HandlerStackFactory;
use GuzzleHttp\Client;


/**
 * Lbs
 * 类的介绍
 * @package Core\Plugins\BaiDu
 * User：YM
 * Date：2020/2/21
 * Time：下午11:00
 */
class Lbs
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var Closure
     */
    private $client;

    /**
     * @var Closure
     */
    private $stackClient;

    public function __construct(ContainerInterface $container,ServerRequestInterface $request)
    {
        $this->container = $container;
        $this->request = $request;
        $this->client = $container->get(GuzzleClientFactory::class)->create();
        $factory = new HandlerStackFactory();
        $stack = $factory->create(['max_connections' => 500]);
        $this->stackClient = make(Client::class, [
            'config' => [
                'handler' => $stack,
            ],
        ]);
    }

    /**
     * getGeoCoding
     * 根据地名获取位置编码
     * User：YM
     * Date：2020/2/22
     * Time：下午2:31
     * @param string $address
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getGeoCoding($address = '北京')
    {
        $baseUrl = config('baidu_lbs.base_url');
        $uri = '/'.rtrim(ltrim(config('baidu_lbs.uri'),'/'),'/').'/';
        $ak = config('baidu_lbs.ak');
        $sk = config('baidu_lbs.sk');
        $output = 'json';

        $temUrl = rtrim($baseUrl,'/').$uri."?address=%s&output=%s&ak=%s&sn=%s";
        $params = [
            'address' => $address,
            'output' => $output,
            'ak' => $ak
        ];
        $sn = $this->calculateSign($sk,$uri,$params);
        $url = sprintf($temUrl, urlencode($address), $output, $ak, $sn);
//        $response = $this->client->get($url);
        $response = $this->stackClient->get($url);
        return $response->getBody()->getContents();
    }

    /**
     * calculateSign
     * 计算签名
     * User：YM
     * Date：2020/2/22
     * Time：下午2:31
     * @param $sk
     * @param $uri
     * @param $params
     * @param string $method
     * @return string
     */
    private function calculateSign($sk, $uri, $params, $method = 'GET')
    {
        if ($method === 'POST'){
            ksort($params);
        }
        $queryString = http_build_query($params);
        return md5(urlencode($uri.'?'.$queryString.$sk));
    }
}