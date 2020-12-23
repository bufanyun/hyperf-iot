<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * FileUpload.php
 *
 * User：YM
 * Date：2020/2/21
 * Time：下午11:00
 */


namespace Core\Plugins;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Common\Container\Auth;
use Core\Services\AttachmentService;
use Hyperf\Di\Annotation\Inject;

/**
 * FileUpload
 * 文件上传
 *
 * 该类不能直接new，需要使用make方法实例化
 * 因为该类里面使用了依赖注入
 * @package Core\Plugins
 * User：YM
 * Date：2020/2/27
 * Time：上午12:29
 */
class FileUpload
{
    private $file;     //上传对象
    private $config;   //配置信息
    private $oriName;  //原始文件名，包含扩展名
    private $filename; //新文件名，包含扩展名
    private $uploadPath; //相对路径
    private $fileSize; //文件大小
    private $fileType; //文件类型，后缀名
    private $fileMd5; //文件类型
    private $stateInfo; //上传状态信息,
    private $id = null; // 数据库存储主键id
    private $stateMap = array( //上传状态
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE" => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
        "ERROR_CREATE_DIR" => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
        "ERROR_FILE_MOVE" => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND" => "找不到上传文件",
        "ERROR_WRITE_CONTENT" => "写入文件内容错误",
        "ERROR_UNKNOWN" => "未知错误",
        "ERROR_DEAD_LINK" => "链接不可用",
        "ERROR_HTTP_LINK" => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE" => "链接contentType不正确"
    );
    private $fileInfo = [];

    /**
     *
     * @Inject()
     * @var AttachmentService
     */
    private $attachmentService;

    /**
     * @Inject()
     * @var Auth
     */
    private $auth;


    /**
     * 构造函数
     * @param string $fileField 文件对象/ base64 值 / remote 链接
     * @param array $config 配置项
     * @param bool $base64 是否解析base64编码，可省略。若开启，则$fileField代表的是base64编码的字符串表单名
     * $param string $imgType 图片缩略图样式
     */
    /**
     * FileUpload constructor.
     * @param $file 文件对象
     * @param array $uploadParams 上传参数
     */
    public function __construct($file, $uploadParams = [])
    {
        $this->file = $file;
        $this->config    = config('upload');
        $this->uploadPath = $uploadParams['upload_path']??$this->getUploadPath();
    }

    /**
     * uploadFile
     * 上传文件
     * User：YM
     * Date：2020/2/28
     * Time：上午1:11
     * @param string $type
     */
    public function uploadFile($type = 'upload')
    {
        if($type == "upload") {
            $this->upFile();
        } else {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_UPLOAD,'错误的上传类型！');
        }
        $fileInfo = $this->getFileInfo();
        if ($fileInfo['state'] == 'SUCCESS') {
            $this->saveDatabase($fileInfo);
        }else{
            $error = $fileInfo['state'] ?? '未知错误';
            throw new BusinessException(StatusCode::ERR_EXCEPTION_UPLOAD,$error);
        }
    }

    /**
     * upFile
     * 处理上传
     * User：YM
     * Date：2020/2/28
     * Time：下午11:48
     */
    private function upFile()
    {
        // 校验上传的合法性
        if (!$this->file) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return;
        }
        // 获取上传文件信息
        $arr = $this->file->toArray();
        if ( $arr['error'] ) {
            $this->stateInfo = $this->getStateInfo($arr['error']);
            return;
        }
        if ( !file_exists($arr['tmp_file']) ) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMP_FILE_NOT_FOUND");
            return;
        }
        if ( !is_uploaded_file($arr['tmp_file']) ) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMP_FILE");
            return;
        }
        $this->oriName  = $arr['name'];
        $this->fileSize = $arr['size'];
        // 后缀名
        $this->fileType = $this->file->getExtension();
        $this->filename = $this->getFilename();
        //检查文件大小是否超出限制
        if ( !$this->checkSize() ) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }
        //检查是否不允许的文件格式
        if ( !$this->checkType() ) {
            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");
            return;
        }
        // 存储位置
        $savePath = $this->getSavePath();
        $targetPath = $this->getSavePath().$this->filename;
        //创建目录失败
        if ( !file_exists($savePath) && !mkdir($savePath, 0777, true) ) {
            $this->stateInfo = $this->getStateInfo("ERROR_CREATE_DIR");
            return;
        }
        if ( !is_writeable($savePath) ) {
            $this->stateInfo = $this->getStateInfo("ERROR_DIR_NOT_WRITEABLE");
            return;
        }
        // 存储文件
        $this->file->moveTo($targetPath);
        // 判断文件是否已经移动
        if ( $this->file->isMoved() ) {
            $this->stateInfo = $this->stateMap[0];
            $this->fileMd5 = md5_file($targetPath);
        } else {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_MOVE");
        }
    }


    /**
     * getSavePath
     * 获取保存目录
     * User：YM
     * Date：2020/2/28
     * Time：上午1:48
     * @return string
     */
    private function getSavePath()
    {
        $rootPath = rtrim($this->config['upload_path'],DS);
        $uploadPath = trim($this->uploadPath,DS);
        $savePath = $rootPath.DS.$uploadPath.DS;
        return $savePath;
    }

    /**
     * getUploadPath
     * 获取文件上传相对目录
     * User：YM
     * Date：2020/2/28
     * Time：上午12:54
     * @return string
     */
    private function getUploadPath()
    {
        $attachments = trim($this->config['attachments'],DS);
        $timePath = date('Ymd');
        $uploadPath = $attachments.DS.$timePath.DS;
        return $uploadPath;
    }

    /**
     * getFilename
     * 生成文件名（存储重命名）
     * User：YM
     * Date：2020/2/27
     * Time：下午11:45
     * @return string
     */
    private function getFilename()
    {
        //替换日期事件
        $t = date('YmdHis');
        $format = $this->config["file_name_format"];
        $format = str_replace("{time}", $t, $format);
        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, (int)$matches[1]), $format);
        }
        $ext = $this->fileType;
        return $format .'.'. $ext;
    }

    /**
     * getStateInfo
     * 获取状态信息
     * User：YM
     * Date：2020/2/27
     * Time：下午11:25
     * @param $key
     * @return bool|mixed
     */
    private function getStateInfo($key)
    {
        return !$this->stateMap[$key]?? $this->stateMap["ERROR_UNKNOWN"];
    }

    /**
     * checkType
     * 文件类型检测
     * User：YM
     * Date：2020/2/27
     * Time：下午11:07
     * @return bool
     */
    private function checkType()
    {
        return in_array(strtolower($this->fileType), $this->config["file_allow_files"]);
    }

    /**
     * checkSize
     * 文件大小检测
     * User：YM
     * Date：2020/2/27
     * Time：下午11:08
     * @return bool
     */
    private function  checkSize()
    {
        return $this->fileSize <= ($this->config["file_max_size"]);
    }

    /**
     * saveDatabase
     * 保存文件到数据库
     * User：YM
     * Date：2020/2/28
     * Time：上午1:30
     * @param array $fileInfo
     * @return bool|null
     */
    private function saveDatabase($fileInfo = [])
    {
        if (!$fileInfo) {
            return null;
        }
        $userId= $this->auth->check(false);
        $saveData = [
            'title' => $fileInfo['title'],
            'original_name' => $fileInfo['original'],
            'filename' => $fileInfo['filename'],
            'path' => $fileInfo['path'],
            'type' => $fileInfo['type'],
            'size' => $fileInfo['size'],
            'user_id' => $userId
        ];
        $this->id = $this->attachmentService->saveAttachment($saveData);
        return true;
    }

    /**
     * getFileInfo
     * 获取当前上传成功文件的各项信息
     * User：YM
     * Date：2020/2/28
     * Time：上午2:39
     * @return array
     */
    public function getFileInfo()
    {
        $this->fileInfo['id'] = $this->id;
        $this->fileInfo['state'] = $this->stateInfo;
        $filePath =  DS.trim($this->uploadPath,DS).DS.$this->filename;
        // 配置文件附件目录参数过滤，改参数不存库
        $attachments = $this->config['attachments'];
        if($attachments){
            $this->fileInfo['path'] = str_replace("/{$attachments}", '', $filePath);
        }else{
            $this->fileInfo['path'] = $filePath;
        }
        $this->fileInfo['full_path'] =  $this->attachmentService->getAttachmentFullUrl($this->fileInfo['path']);
        $this->fileInfo['title'] = str_replace('.'.$this->fileType,'',$this->filename);
        $this->fileInfo['original'] = $this->oriName;
        $this->fileInfo['filename'] = $this->filename;
        $this->fileInfo['type'] = $this->fileType;
        $this->fileInfo['size'] = $this->fileSize;
        $this->fileInfo['md5'] = $this->fileMd5;
        return $this->fileInfo;
    }
}
