<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LiveService.php
 *
 * User：YM
 * Date：2020/2/14
 * Time：下午11:28
 */


namespace Core\Services;


/**
 * LiveService
 * 直播服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/14
 * Time：下午11:28
 *
 * @property \App\Models\Live $liveModel
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\LecturerService $lecturerService
 */
class LiveService extends BaseService
{
    /**
     * getTreeLiveList
     * 获取直播节树list
     * User：YM
     * Date：2020/2/14
     * Time：下午11:46
     * @param $courseId
     * @return mixed
     */
    public function getTreeLiveList($courseId)
    {
        $where = ['course_id' => $courseId];
        $order = ['order' => 'ASC'];
        $list = $this->liveModel->getList($where,$order);
        foreach ($list as &$v) {
            $v['data_type'] = 'live';
            $v['unique_key'] = $v['id'];
            $v['identify_key'] = $v['order']+1;
        }
        unset($v);

        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:46
     * @param array $where
     * @return array
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->liveModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * getList
     * 获取直播list
     * User：YM
     * Date：2020/2/14
     * Time：下午11:46
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {

        $list = $this->liveModel->getList($where,$order,$offset,$limit);

        return $list;
    }

    /**
     * getLiveCount
     * 获取直播条数
     * User：YM
     * Date：2020/2/14
     * Time：下午11:47
     * @param array $where
     * @return int
     */
    public function getLiveCount($where = [])
    {
        $count = $this->liveModel->getCount($where);

        return $count;
    }

    /**
     * saveLive
     * 保存直播
     * User：YM
     * Date：2020/2/14
     * Time：下午11:47
     * @param $inputData
     * @return null
     */
    public function saveLive($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['course_id']) && $inputData['course_id']){
            $saveData['course_id'] = $inputData['course_id'];
        }
        if (isset($inputData['lecturer_id']) && $inputData['lecturer_id']){
            $saveData['lecturer_id'] = $inputData['lecturer_id'];
        }
        if (isset($inputData['title']) && $inputData['title']){
            $saveData['title'] = $inputData['title'];
        }
        if (isset($inputData['intro'])){
            $saveData['intro'] = $inputData['intro'];
        }
        if (isset($inputData['cover'])){
            $saveData['cover'] = $inputData['cover'];
        }
        if (isset($inputData['order'])){
            $saveData['order'] = $inputData['order'];
        }
        if (isset($inputData['start_time']) && $inputData['start_time']){
            $saveData['start_time'] = $inputData['start_time'];
        }
        if (isset($inputData['end_time']) && $inputData['end_time']){
            $saveData['end_time'] = $inputData['end_time'];
        }

        $id = $this->liveModel->saveInfo($saveData);
        return $id;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:48
     * @param $id
     * @return \App\Models\BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id)
    {
        $info = $this->liveModel->getInfo($id);
        $info['image_info'] = $this->attachmentService->getInfo($info['cover']);
        $lecturerInfo = $this->lecturerService->getInfo($info['lecturer_id']);
        $info['user_info'] = $lecturerInfo['user_info'];
        $info['lecturer_info'] = [
            'famous_nickname' => $lecturerInfo['famous_nickname']
        ];

        return $info;
    }

    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:50
     * @param $id
     * @return int
     */
    public function deleteInfo($id)
    {
        $info = $this->liveModel->deleteInfo($id);

        return $info;
    }

    /**
     * getLiveStreamInfo
     * 获取直播流信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:50
     * @param $id
     * @return array
     */
    public function getLiveStreamInfo($id)
    {

        $info = $this->getInfo($id);
        if (!(isset($info['user_info']['id']) && $info['user_info']['id'])) {
            throw new Exception('讲师信息不存在！');
        }
        return $this->getPushPlayStreamInfo($info['user_info']['id']);
    }

    /**
     * getPushPlayStreamInfo
     * 获取推流播放信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:50
     * @param $uid
     * @return array
     */
    public function getPushPlayStreamInfo($uid)
    {
        $data =  [
            'push_info' => '',
            'play_info' => [],
        ];

        return $data;
    }
}