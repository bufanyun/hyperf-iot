<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * VideoService.php
 *
 * User：YM
 * Date：2020/2/10
 * Time：下午5:09
 */


namespace Core\Services;


/**
 * VideoService
 * 视频服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/10
 * Time：下午5:09
 *
 * @property \App\Models\Video $videoModel
 * @property \App\Models\VideoLog $videoLogModel
 */
class VideoService extends BaseService
{
    /**
     * getVideoInfo
     * 获取视频信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:37
     * @param $id
     * @return \App\Models\BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id)
    {
        $info = $this->videoModel->getInfo($id);

        return $info;
    }

    /**
     * getVideoCount
     * 获取视频条数
     * User：YM
     * Date：2020/2/10
     * Time：下午5:37
     * @param array $where
     * @return int
     */
    public function getCount($where = [])
    {
        $count = $this->videoModel->getCount($where);

        return $count;
    }

    /**
     * saveVideoInfo
     * 课程视频信息保存
     * User：YM
     * Date：2020/2/10
     * Time：下午5:37
     * @param $inputData
     * @return null
     */
    public function saveInfo($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['course_id']) && $inputData['course_id']){
            $saveData['course_id'] = $inputData['course_id'];
        }
        if (isset($inputData['chapter_id']) && $inputData['chapter_id']){
            $saveData['chapter_id'] = $inputData['chapter_id'];
        }
        if (isset($inputData['title']) && $inputData['title']){
            $saveData['title'] = $inputData['title'];
        }
        if (isset($inputData['intro'])){
            $saveData['intro'] = $inputData['intro'];
        }
        if (isset($inputData['filename']) && $inputData['filename']){
            $saveData['filename'] = $inputData['filename'];
        }
        if (isset($inputData['material_id'])){
            $saveData['material_id'] = $inputData['material_id'];
        }
        if (isset($inputData['cover']) && $inputData['cover']){
            $saveData['cover'] = $inputData['cover'];
        }
        if (isset($inputData['duration']) && $inputData['duration']){
            $saveData['duration'] = $inputData['duration'];
        }
        if (isset($inputData['size']) && $inputData['size']){
            $saveData['size'] = $inputData['size'];
        }
        if (isset($inputData['status'])){
            $saveData['status'] = $inputData['status'];
        }
        if (isset($inputData['aliyun_video_id'])){
            $saveData['aliyun_video_id'] = $inputData['aliyun_video_id'];
        }
        if (isset($inputData['order'])){
            $saveData['order'] = $inputData['order'];
        }
        if (isset($inputData['seo_title']) && $inputData['seo_title']){
            $saveData['seo_title'] = $inputData['seo_title'];
        }
        if (isset($inputData['seo_keywords']) && $inputData['seo_keywords']){
            $saveData['seo_keywords'] = $inputData['seo_keywords'];
        }
        if (isset($inputData['seo_description']) && $inputData['seo_description']){
            $saveData['seo_description'] = $inputData['seo_description'];
        }

        $id = $this->videoModel->saveInfo($saveData);

        return $id;
    }

    /**
     * deleteVideoInfo
     * 删除视频章节信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:37
     * @param $id
     * @return int
     */
    public function deleteInfo($id)
    {
        $info = $this->videoModel->deleteInfo($id);

        return $info;
    }

    /**
     * getVideoList
     * 获取视频list
     * User：YM
     * Date：2020/2/10
     * Time：下午5:38
     * @param array $where
     * @param array $order
     * @return mixed
     */
    public function getList($where = [], $order = [])
    {
        $list = $this->videoModel->getList($where,$order);

        return $list;
    }

    /**
     * getInfoByWhere
     * 根据条件获取视频信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:38
     * @param array $where
     * @return array
     */
    public function getInfoByWhere($where = [])
    {
        $info = $this->videoModel->getInfoByWhere($where);

        return $info;
    }

    /**
     * saveVideoLogsInfo
     * 保存视频日志信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:39
     * @param $inputData
     * @return mixed
     */
    public function saveLogsInfo($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['video_id']) && $inputData['video_id']){
            $saveData['video_id'] = $inputData['video_id'];
        }
        if (isset($inputData['aliyun_video_id']) && $inputData['aliyun_video_id']){
            $saveData['aliyun_video_id'] = $inputData['aliyun_video_id'];
        }
        if (isset($inputData['type']) && $inputData['type']){
            $saveData['type'] = $inputData['type'];
        }
        if (isset($inputData['info']) && $inputData['info']){
            $saveData['info'] = $inputData['info'];
        }
        $id = $this->videoLogModel->saveInfo($saveData);

        return $id;
    }

}