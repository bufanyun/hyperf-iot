<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LiveRepository.php
 *
 * User：YM
 * Date：2020/2/14
 * Time：下午11:27
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * LiveRepository
 * 直播管理仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/14
 * Time：下午11:27
 *
 * @property \Core\Services\LiveService $liveService
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\LecturerService $lecturerService
 */
class LiveRepository extends BaseRepository
{
    /**
     * getLiveList
     * 获取列表
     * User：YM
     * Date：2020/2/14
     * Time：下午11:37
     * @param $inputData
     * @return array
     */
    public function getLiveList($inputData)
    {
        $pagesInfo = $this->liveService->getPagesInfo($inputData);
        $where = $inputData;
        unset($where['page_size']);
        unset($where['current_page']);
        $order = ['id'=>'DESC'];
        $list = $this->liveService->getList($where,$order,$pagesInfo['offset'],$pagesInfo['page_size']);
        foreach ($list as $k => $v) {
            $info = $this->attachmentService->getInfo($v['cover']);
            $list[$k]['image_url'] = $info['full_path'];
            $lecturerInfo = $this->lecturerService->getInfo($v['lecturer_id']);
            if (isset($lecturerInfo['user_info']['mobile']) && isset($lecturerInfo['famous_nickname'])) {
                $list[$k]['lecturer_info'] = $lecturerInfo['user_info']['mobile'].'('.$lecturerInfo['famous_nickname'].')' ;
            } elseif (isset($lecturerInfo['famous_nickname'])) {
                $list[$k]['lecturer_info'] = $lecturerInfo['famous_nickname'];
            } else {
                $list[$k]['lecturer_info'] = '';
            }
        }
        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }

    /**
     * saveLive
     * 保存
     * User：YM
     * Date：2020/2/14
     * Time：下午11:44
     * @param $data
     * @return mixed
     */
    public function saveLive($data)
    {
        if (isset($data['time_value'])) {
            $data['start_time'] = $data['time_value'][0]??null;
            $data['end_time'] = $data['time_value'][0]??null;
        }
        return $this->liveService->saveLive($data);
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:45
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->liveService->getInfo($id);
        $mobile = '';
        $str = '';
        if ($info['user_info']) {
            $mobile = $info['user_info']['mobile'];
            $str = $info['user_info']['nickname'];
            unset($info['user_info']);
        }
        if ($info['lecturer_info'] && $info['lecturer_info']['famous_nickname']) {
            $str = $info['lecturer_info']['famous_nickname'];
            unset($info['lecturer_info']);
        }
        $str = $str?'('.$str.')':$str;
        $info['mobile_alias'] = $mobile.$str;
        $info['time_value'] = [$info['start_time'],$info['end_time']];

        return $info;
    }

    /**
     * deleteInfo
     * 删除信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:45
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->liveService->deleteInfo($id);
        return $info;
    }

    /**
     * searchLecturer
     * 搜索讲师
     * User：YM
     * Date：2020/2/14
     * Time：下午11:45
     * @param $inputData
     * @return array
     */
    public function searchLecturer($inputData)
    {
        $data = [];
        if (isset($inputData['search']) && $inputData['search']) {
            $data = $this->lecturerService->searchLecturerList($inputData['search'],[],[]);
        }

        return $data;
    }

    /**
     * getStreamInfo
     * 获取直播流详情
     * User：YM
     * Date：2020/2/14
     * Time：下午11:45
     * @param $id
     * @return array
     */
    public function getStreamInfo($id)
    {
        $info = $this->liveService->getLiveStreamInfo($id);

        return $info;
    }
}