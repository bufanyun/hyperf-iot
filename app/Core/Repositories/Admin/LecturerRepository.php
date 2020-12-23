<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LecturerRepository.php
 *
 * User：YM
 * Date：2020/2/15
 * Time：上午11:32
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * LecturerRepository
 * 讲师管理仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/15
 * Time：上午11:32
 *
 * @property \Core\Services\LecturerService $lecturerService
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\UserService $userService
 */
class LecturerRepository extends BaseRepository
{
    /**
     * getLecturerList
     * 获取列表
     * User：YM
     * Date：2020/2/15
     * Time：上午11:33
     * @param $inputData
     * @return array
     */
    public function getLecturerList($inputData)
    {
        $pagesInfo = $this->lecturerService->getPagesInfo($inputData);
        $where = $inputData;
        unset($where['page_size']);
        unset($where['current_page']);
        $order = [];
        $list = $this->lecturerService->getList($where,$order,$pagesInfo['offset'],$pagesInfo['page_size']);

        foreach ($list as $k => $v) {
            $info = $this->attachmentService->getInfo($v['image']);
            $list[$k]['image_url'] = $info['full_path'];
            $userInfo = $this->userService->getInfo($v['user_id']);
            if ($userInfo) {
                $list[$k]['relation_user'] = $userInfo['nickname']?$userInfo['mobile'].'('.$userInfo['nickname'].')':$userInfo['mobile'];
            }
            $list[$k]['intro_alias'] = $v['intro'] && mb_strlen($v['intro']) > 24?mb_substr($v['intro'],0,24).'...':'';
        }
        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }

    /**
     * saveLecturer
     * 保存
     * User：YM
     * Date：2020/2/15
     * Time：上午11:33
     * @param $data
     * @return mixed
     */
    public function saveLecturer($data)
    {
        $id =  $this->lecturerService->saveLecturer($data);
//        if (isset($data['user_id']) && $data['user_id']){
//            $parms['nickname'] = isset($data['famous_nickname']) && $data['famous_nickname']?$data['famous_nickname']:'';
//            $parms['is_lecturer'] = 1;
//        }

        return $id;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/15
     * Time：上午11:33
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->lecturerService->getInfo($id);
        if ($info['user_info']) {
            $info['mobile_alias'] = $info['user_info']['nickname']?$info['user_info']['mobile'].'('.$info['user_info']['nickname'].')':$info['user_info']['mobile'];
            unset($info['user_info']);
        }
        return $info;
    }

    /**
     * deleteInfo
     * 删除信息
     * User：YM
     * Date：2020/2/15
     * Time：上午11:34
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->lecturerService->deleteInfo($id);
        return $info;
    }

    /**
     * searchUser
     * 搜索用户
     * User：YM
     * Date：2020/2/15
     * Time：上午11:34
     * @param $inputData
     * @return array
     */
    public function searchUser($inputData)
    {
        $data = [];
        if (isset($inputData['search']) && $inputData['search']) {
            $ids = $this->lecturerService->getLecturerUserIds();
            $data = $this->userService->searchUserList($inputData['search'],[],$ids);
        }

        return $data;
    }

}