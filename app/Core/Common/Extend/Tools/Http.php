<?php

namespace Core\Common\Extend\Tools;

use Swoole\Coroutine\Http\Client;

class Http
{

    /**
     * 协程-发送get请求
     * curl_get
     * @param $url
     * @param  array  $header
     *
     * @return string
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 14:28
     */
    public function curl_get(string $url, array $header = []) : string
    {
        $urlsInfo = \parse_url($url);
        $queryUrl = $urlsInfo['path'];
        if (isset($urlsInfo['query'])) {
            $queryUrl .= '?'.$urlsInfo['query'];
        }
        $domain = $urlsInfo['host'];
        if (isset($urlsInfo['port'])) {
            $port = $urlsInfo['port'];
        } else {
            $port = ($urlsInfo['scheme'] == 'https' ? 443 : 80);
        }
        $isSsl = $urlsInfo['scheme'] == 'https' ? true : false;
        $cli = make(Client::class, [$domain, $port, $isSsl]);
        $cli->setHeaders($header);
        $cli->set(['timeout' => 10]);
        $cli->get($queryUrl);
        $output = $cli->body;
        $cli->close();
        return $output;
    }

    /**
     * 协程-发送post请求
     * curl_post
     * @param $url
     * @param $post_data
     * @param  array  $header
     *
     * @return string
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 14:31
     */
    public function curl_post(string $url, array $post_data, array $header = []) : string
    {
        $urlsInfo = \parse_url($url);
        $queryUrl = $urlsInfo['path'];
        if (isset($urlsInfo['query'])) {
            $queryUrl .= '?'.$urlsInfo['query'];
        }
        $domain = $urlsInfo['host'];
        if (isset($urlsInfo['port'])) {
            $port = $urlsInfo['port'];
        } else {
            $port = ($urlsInfo['scheme'] == 'https' ? 443 : 80);
        }
        $isSsl = $urlsInfo['scheme'] == 'https' ? true : false;
        $cli = make(Client::class, [$domain, $port, $isSsl]);
        $cli->setHeaders($header);
        $cli->set(['timeout' => 10]);
        $cli->post($queryUrl, $post_data);
        $output = $cli->body;
        $cli->close();
        return $output;
    }

}