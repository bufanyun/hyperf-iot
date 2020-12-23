<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * AttachmentService.php
 *
 * User：YM
 * Date：2020/2/5
 * Time：下午8:57
 */


namespace Core\Services;


/**
 * AttachmentService
 * 附件服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/5
 * Time：下午8:57
 *
 * @property \App\Models\Attachment $attachmentModel
 */
class AttachmentService extends BaseService
{
    /**
     * getList
     * 条件获取友情链接列表
     * User：YM
     * Date：2020/2/10
     * Time：下午10:34
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {

        $list = $this->attachmentModel->getList($where,$order,$offset,$limit);
        foreach ($list as &$v) {
            $v['size_alias'] = formatBytes($v['size']);
            $v['path_alias'] =  $v['path'] && mb_strlen($v['path']) > 32?mb_substr($v['path'],0,32).'...':'';
            $v['title_alias'] = $v['title'] &&  mb_strlen($v['title']) > 16?mb_substr($v['title'],0,16).'...':'';
        }
        unset($v);

        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/10
     * Time：下午10:35
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->attachmentModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * getInfo
     * 获取附件信息
     * User：YM
     * Date：2020/2/5
     * Time：下午8:59
     * @param $id
     * @return \App\Models\BaseModel|array|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id)
    {
        if (!$id) {
            return [];
        }
        $info = $this->attachmentModel->getInfo($id);
        if ($info && $info['path']) {
            $info['full_path'] = $this->getAttachmentFullUrl($info['path']);
        }

        return $info;
    }

    /**
     * getAttachmentFullUrl
     * 获取附件全路径
     * User：YM
     * Date：2020/2/5
     * Time：下午9:21
     * @param $path 相对路径包含文件名
     * @return string
     */
    public function getAttachmentFullUrl($path)
    {
        if (!$path) {
            return '';
        }
        $uploadSave = config('upload.upload_save');
        if ($uploadSave == 'oss') {
            $host = config('aliyun_oss.bucket.data.host');
            $host = substr($host,0,4) == 'http'?$host:'http://'.$host;
        } else {
            $domain = config('app_domain');
            $domain = substr($domain,0,4) == 'http'?$domain:'http://'.$domain;
            $attachments = ltrim(config('upload.attachments'),'/');
            $host = $domain.'/'.$attachments;
        }
        $fullUrl = rtrim($host,'/').'/'.ltrim($path,'/');
        return $fullUrl;
    }

    /**
     * newFileName
     * 生成一个文件名，不包含后缀
     * User：YM
     * Date：2020/2/6
     * Time：下午8:54
     * @return mixed|null|string|string[]
     */
    public function newFileName()
    {
        //替换日期事件
        $t = date('YmdHis');
        $format = config('upload.file_name_format');
        $format = str_replace("{time}", $t, $format);
        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, (int)$matches[1]), $format);
        }
        return $format;
    }

    /**
     * addAttachment
     * 添加附件
     * @param mixed $userId
     * @access public
     * @return void
     */
    public function addAttachment($userId)
    {
        $saveData = [
            'title' => time(),
            'user_id' => $userId
        ];
        return $this->attachmentModel->saveInfo($saveData);
    }

    /**
     * saveAttachment
     * 保存附件信息
     * User：YM
     * Date：2020/2/7
     * Time：下午8:11
     * @param $inputData
     * @return null
     */
    public function saveAttachment($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['title'])){
            $saveData['title'] = $inputData['title'];
        }
        if (isset($inputData['filename'])){
            $saveData['filename'] = $inputData['filename'];
        }
        if (isset($inputData['original_name'])){
            $saveData['original_name'] = $inputData['original_name'];
        }
        if (isset($inputData['path'])){
            $saveData['path'] = $inputData['path'];
        }
        if (isset($inputData['type'])){
            $saveData['type'] = $inputData['type'];
        }
        if (isset($inputData['size'])){
            $saveData['size'] = $inputData['size'];
        }
        if (isset($inputData['user_id'])){
            $saveData['user_id'] = $inputData['user_id'];
        }
        return $this->attachmentModel->saveInfo($saveData);
    }

    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/10
     * Time：下午10:35
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->attachmentModel->deleteInfo($id);

        return $info;
    }
}