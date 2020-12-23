<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LogSlsHandler.php
 *
 * 阿里云sls日志处理
 *
 * User：YM
 * Date：2019/12/31
 * Time：下午3:17
 */


namespace Core\Common\Handler;

use Hyperf\Di\Annotation\Inject;
use Monolog\Handler\AbstractProcessingHandler;
use Ym\AliyunSls\ClientInterface;


/**
 * LogSlsHandler
 * 阿里云sls日志处理
 * @package Core\Common\LogHandler
 * User：YM
 * Date：2019/12/31
 * Time：下午3:17
 */
class LogSlsHandler extends AbstractProcessingHandler
{
    /**
     * @Inject
     * @var ClientInterface
     */
    protected $sls;

    /**
     * write
     * 记录日志
     * User：YM
     * Date：2019/12/21
     * Time：下午4:15
     * @param array $record
     * @return bool|void
     */
    public function write(array $record): void
    {
        // 判断是否开始日志记录
        if ( !config('app_log') ) {
            return false;
        }
        // 判断是否处理框架日志
        if ( !config('hf_log')  && $record['channel'] == 'hyperf' ) {
            return false;
        }
        // 判断系统允许日志类型
        if ( ! isStdoutLog($record['level_name']) ) {
            return false;
        }
        $saveData = $record['context'];
        $saveData['channel'] = $record['channel'];
        $saveData['message'] = is_array($record['message'])?json_encode($record['message']):$record['message'];
        $saveData['level_name'] = $record['level_name'];
        // 阿里云日志不能有空字段
        foreach ($saveData as &$v) {
            if (!$v) {
                $v = 0;
            }
        }
        unset($v);
        $this->sls->putLogs($saveData);
    }
}