<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * AttachmentRepository.php
 *
 * User：YM
 * Date：2020/2/15
 * Time：下午3:51
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * AttachmentRepository
 * 附件管理仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/15
 * Time：下午3:51
 *
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\UserService $userService
 */
class AttachmentRepository extends BaseRepository
{
    /**
     * getAttachmentList
     * 获取列表
     * User：YM
     * Date：2020/2/15
     * Time：下午5:17
     * @param $inputData
     * @return array
     */
    public function getAttachmentList($inputData)
    {
        $pagesInfo = $this->attachmentService->getPagesInfo($inputData);
        $order = ['id' => 'DESC'];
        $list = $this->attachmentService->getList([],$order,$pagesInfo['offset'],$pagesInfo['page_size']);
        foreach ($list as &$v) {
            $tmp = $this->userService->getInfo($v['user_id']);
            $v['username'] = isset($tmp['nickname']) && $tmp['nickname']?$tmp['nickname']:$tmp['mobile'];
        }
        unset($v);

        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }

    /**
     * saveAttachment
     * 保存
     * User：YM
     * Date：2020/2/10
     * Time：下午10:38
     * @param $data
     * @return mixed
     */
    public function saveAttachment($data)
    {
        if (isset($data['attachment_id']) && $data['attachment_id']) {
            $data['id'] = $data['attachment_id'];
        }
        return $this->attachmentService->saveAttachment($data);
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/10
     * Time：下午10:38
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->attachmentService->getInfo($id);
        return $info;
    }

    /**
     * deleteInfo
     * 删除信息
     * User：YM
     * Date：2020/2/10
     * Time：下午10:38
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->attachmentService->deleteInfo($id);
        return $info;
    }
}