<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * UploadRepository.php
 *
 * User：YM
 * Date：2020/2/6
 * Time：下午8:34
 */


namespace Core\Repositories\Admin;


use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;
use Core\Plugins\FileUpload;

/**
 * UploadRepository
 * 上传仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/6
 * Time：下午8:34
 *
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Common\Container\Auth $auth
 */
class UploadRepository extends BaseRepository
{
    /**
     * getUploadToken
     * 获取上传凭证
     * User：YM
     * Date：2020/2/6
     * Time：下午8:58
     * @return array
     */
    public function getUploadToken()
    {
        $uploadSave = config('upload.upload_save');
        if($uploadSave == 'oss'){
            return $this->getOssToken();
        }else{
            $host = config('app_domain').'/admin_api/upload/file';
            $host = substr($host,0,3) == 'http'?$host:'http://'.$host;
            $dir = trim(config('upload.attachments'),'/').'/'.date('Ymd').'/';
            $result = [
                'host' => $host,
                'dir' => $dir,
                'upload_save' => 'local'
            ];

            return $result;
        }
    }

    /**
     * getOssToken
     * 获取阿里云oss上传凭证
     * User：YM
     * Date：2020/2/6
     * Time：下午11:12
     * @return array
     */
    public function getOssToken(){
        $dir = $this->getOssUploadPath();
        // 附件表初始化数据
        $userInfo = $this->auth->check();
        $attachmentId = $this->attachmentService->addAttachment($userInfo['id']);

        // Callback参数
        $callback_param = [
            'callbackUrl' => config('aliyun_oss.callback_url'),
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}&aid='.$attachmentId,
            'callbackBodyType' => "application/x-www-form-urlencoded"
        ];
        $callbackString = json_encode($callback_param);
        $base64CallbackBody = base64_encode($callbackString);

        // policy
        $expire = time()+30;
        $expiration = $this->gmtIso8601($expire);
        // 最大文件大小.用户可以自己设置
        $conditions[] = ['content-length-range', 0, 1048576000];
        // 表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $conditions[] = [ 'starts-with', '$key', $dir ];
        $arr = [
            'expiration' => $expiration,
            'conditions' => $conditions
        ];
        $policy = json_encode($arr);
        $base64Policy = base64_encode($policy);

        // 签名
        $signature = base64_encode(hash_hmac('sha1', $base64Policy, config('aliyun_oss.secret_key'), true));
        // host
        $host = config('aliyun_oss.bucket.data.host');
        $host = substr($host,0,3) == 'http'?$host:'http://'.$host;
        $response = [];
        $response['accessid'] = config('aliyun_oss.access_key');
        $response['host'] = $host;
        $response['policy'] = $base64Policy;
        $response['signature'] = $signature;
        $response['expire'] = $expire;
        $response['callback'] = $base64CallbackBody;
        $response['upload_save'] = 'oss';
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        $response['filename'] = $this->attachmentService->newFilename();

        return $response;
    }

    /**
     * getOssUploadPath
     * 获取OSS上传路径
     * User：YM
     * Date：2020/2/6
     * Time：下午9:08
     * @return string
     */
    private function getOssUploadPath()
    {
        return config('app_name').'/'.date('Ymd').'/';
    }

    /**
     * gmtIso8601
     * 格式化过期时间
     * User：YM
     * Date：2020/2/6
     * Time：下午11:07
     * @param $time
     * @return string
     */
    private function gmtIso8601($time)
    {
        $dtStr = date("c", $time);
        $pos = strpos($dtStr, '+');
        $expiration = substr($dtStr, 0, $pos);
        return $expiration."Z";
    }

    /**
     * uploadFiles
     * 上传文件
     * User：YM
     * Date：2020/2/28
     * Time：下午10:45
     * @param $files
     * @param $data
     * @return array
     */
    public function uploadFiles($files, $data)
    {
        if (isset($files['file'])) {
            $upFiles = $files['file'];
        } else {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_UPLOAD,'上传文件不存在！');
        }
        $fileList = [];
        if (is_array($upFiles)) {
            foreach ($upFiles as $k => $v) {
                $instance = make(FileUpload::class,[$v,$data]);
                $instance->uploadFile();
                $fileList[] = $instance->getFileInfo();
            }
        } else {
            $instance = make(FileUpload::class,[$upFiles,$data]);
            $instance->uploadFile();
            $fileList[] = $instance->getFileInfo();
        }

        return $fileList;
    }

}