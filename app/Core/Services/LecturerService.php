<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LecturerService.php
 *
 * User：YM
 * Date：2020/2/14
 * Time：下午11:38
 */


namespace Core\Services;


/**
 * LecturerService
 * 讲师服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/14
 * Time：下午11:38
 *
 * @property \App\Models\Lecturer $lecturerModel
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\UserService $userService
 */
class LecturerService extends BaseService
{
    /**
     * getList
     * 条件获取讲师列表
     * User：YM
     * Date：2020/2/14
     * Time：下午11:39
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->lecturerModel->getList($where,$order,$offset,$limit);

        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:40
     * @param array $where
     * @return array
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->lecturerModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * saveLecturer
     * 保存讲师，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/14
     * Time：下午11:40
     * @param $inputData
     * @return null
     */
    public function saveLecturer($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['user_id']) && $inputData['user_id']){
            $saveData['user_id'] = $inputData['user_id'];
        }
        if (isset($inputData['famous_nickname']) && $inputData['famous_nickname']){
            $saveData['famous_nickname'] = $inputData['famous_nickname'];
        }
        if (isset($inputData['image']) && $inputData['image']){
            $saveData['image'] = $inputData['image'];
        }
        if (isset($inputData['additional'])){
            $saveData['additional'] = $inputData['additional'];
        }
        if (isset($inputData['intro'])){
            $saveData['intro'] = $inputData['intro'];
        }
        if (isset($inputData['details'])){
            $saveData['details'] = $inputData['details'];
        }

        $id = $this->lecturerModel->saveInfo($saveData);

        return $id;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:40
     * @param $id
     * @return \App\Models\BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id)
    {
        $info = $this->lecturerModel->getInfo($id);
        $info['image_info'] = isset($info['image'])?$this->attachmentService->getInfo($info['image']):'';
        $info['user_info'] = isset($info['user_id'])?$this->userService->getInfo($info['user_id']):'';

        return $info;
    }

    /**
     * getInfoByUid
     * 获取讲师信息通过用户id
     * User：YM
     * Date：2020/2/14
     * Time：下午11:41
     * @param $userId
     * @return array
     */
    public function getInfoByUid($userId)
    {
        $where = ['user_id' => $userId];
        $info = $this->lecturerModel->getInfoByWhere($where);

        return$info;
    }

    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:41
     * @param $id
     * @return int
     */
    public function deleteInfo($id)
    {
        $info = $this->lecturerModel->deleteInfo($id);

        return $info;
    }

    /**
     * searchLecturerList
     * 根据搜索条件返回list
     * User：YM
     * Date：2020/2/14
     * Time：下午11:41
     * @param $search
     * @param array $userIds
     * @param array $notIds
     * @param int $limit
     * @return mixed
     */
    public function searchLecturerList($search, $userIds=[], $notIds = [], $limit = 10)
    {
        $list = $this->lecturerModel->getSearchList($search, $userIds, $notIds, $limit);

        foreach ($list as $k => $v) {
            if ($v['famous_nickname']) {
                $list[$k]['value'] = $v['mobile'].'('.$v['famous_nickname'].')';
            } elseif ($v['nickname']) {
                $list[$k]['value'] = $v['mobile'].'('.$v['nickname'].')';
            } else {
                $list[$k]['value'] = $v['mobile'];
            }
        }

        return $list;
    }

    /**
     * getLecturerUserIds
     * 获取已存在讲师的用户id的集合
     * User：YM
     * Date：2020/2/14
     * Time：下午11:41
     * @return array
     */
    public function getLecturerUserIds()
    {
        $list = $this->getList();
        $ids = array_pluck($list,'user_id');
        return $ids;
    }
}