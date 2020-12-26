<?php
namespace Core\Common\Extend\Tools;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Swoole\Coroutine\Http\Client;

/**
 * Class CUrl
 * 协程请求
 * @package Core\Common\Extend\Tools
 */
class CUrl {

    /**
     * 最大重试次数
     */
    const MAX_RETRY_TIMES = 10;

    /**
     * @var int $retryTimes 超时重试次数；注意，此为失败重试的次数，即：总次数 = 1 + 重试次数
     */
    protected $retryTimes;

    protected $header = array();

    protected $option = array();

    protected $hascookie = FALSE;

    protected $cookie = array();

    /**
     * @param int $retryTimes 超时重试次数，默认为1
     */
    public function __construct($retryTimes = 1) {
        $this->retryTimes = $retryTimes < static::MAX_RETRY_TIMES
            ? $retryTimes : static::MAX_RETRY_TIMES;
    }

    /** ------------------ 核心使用方法 ------------------ **/

    /**
     * GET方式的请求
     * @param string $url 请求的链接
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     */
    public function get($url, $timeoutMs = 3000) {
        return $this->request($url, array(), $timeoutMs);
    }

    /**
     * POST方式的请求
     * @param string $url 请求的链接
     * @param array $data POST的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     */
    public function post($url, $data, $timeoutMs = 3000) {
        return $this->request($url, $data, $timeoutMs);
    }

    /** ------------------ 前置方法 ------------------ **/

    /**
     * 设置请求头，后设置的会覆盖之前的设置
     *
     * @param array $header 传入键值对如：
    ```
     * array(
     *     'Accept' => 'text/html',
     *     'Connection' => 'keep-alive',
     * )
    ```
     *
     * @return $this
     */
    public function setHeader($header) {
        $this->header = array_merge($this->header, $header);
        return $this;
    }

    /**
     * 设置curl配置项
     *
     * - 1、后设置的会覆盖之前的设置
     * - 2、开发者设置的会覆盖框架的设置
     *
     * @param array $option 格式同上
     *
     * @return $this
     */
    public function setOption($option) {
        $this->option = $option + $this->option;
        return $this;
    }

    /**
     * @param array $cookie
     */
    public function setCookie($cookie) {
        $this->cookie = $cookie;
        return $this;
    }

    /**
     * @return array
     */
    public function getCookie() {
        return $this->cookie;
    }

    public function withCookies() {
        $this->hascookie = TRUE;

        if (!empty($this->cookie)) {
            $this->setHeader(array('Cookie' => $this->getCookieString()));
        }
        $this->setOption(array(CURLOPT_COOKIEFILE => ''));

        return $this;
    }

    /** ------------------ 辅助方法 ------------------ **/

    /**
     * 统一接口请求
     * @param string $url 请求的链接
     * @param array $data POST的数据
     * @param int $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     * @throws Exception
     */
    protected function request($url, $data, $timeoutMs = 3000) {
        $options = array(
            CURLOPT_URL                 => $url,
            CURLOPT_RETURNTRANSFER      => TRUE,
            CURLOPT_HEADER              => 0,
            CURLOPT_CONNECTTIMEOUT_MS   => $timeoutMs,
            CURLOPT_HTTPHEADER          => $this->getHeaders(),
        );

        if (!empty($data)) {
            $options[CURLOPT_POST]          = 1;
            $options[CURLOPT_POSTFIELDS]    = $data;
        }

        $options = $this->option + $options; //$this->>option优先

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $curRetryTimes = $this->retryTimes;
        do {
            $rs = curl_exec($ch);
            $curRetryTimes--;
        } while ($rs === FALSE && $curRetryTimes >= 0);
        $errno = curl_errno($ch);
        if ($errno) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, $errno);
        }

        //update cookie
        if ($this->hascookie) {
            $cookie = $this->getRetCookie(curl_getinfo($ch, CURLINFO_COOKIELIST));
            !empty($cookie) && $this->cookie = $cookie + $this->cookie;
            $this->hascookie = FALSE;
            unset($this->header['Cookie']);
            unset($this->option[CURLOPT_COOKIEFILE]);
        }
        curl_close($ch);

        return $rs;
    }

    /**
     *
     * @return array
     */
    protected function getHeaders() {
        $arrHeaders = array();
        foreach ($this->header as $key => $val) {
            $arrHeaders[] = $key . ': ' . $val;
        }
        return $arrHeaders;
    }

    protected function getRetCookie(array $cookies) {
        $ret = array();
        foreach ($cookies as $cookie) {
            $arr = explode("\t", $cookie);
            if (!isset($arr[6])) {
                continue;
            }
            $ret[$arr[5]] = $arr[6];
        }
        return $ret;
    }

    protected function getCookieString() {
        $ret = '';
        foreach ($this->getCookie() as $key => $val) {
            $ret .= $key . '=' . $val . ';';
        }
        return trim($ret, ';');
    }

    /** ------------------ 协程化 ------------------ **/

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
