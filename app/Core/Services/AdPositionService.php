<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * AdPositionService.php
 *
 * User：YM
 * Date：2020/2/10
 * Time：下午5:15
 */


namespace Core\Services;


/**
 * AdPositionService
 * 广告位服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/10
 * Time：下午5:15
 *
 * @property \App\Models\AdPosition $adPositionModel
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\VideoService $videoService
 */
class AdPositionService extends BaseService
{
    /**
     * getList
     * 条件获取广告位列表
     * User：YM
     * Date：2020/2/10
     * Time：下午5:16
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->adPositionModel->getList($where,$order,$offset,$limit);
        foreach ($list as &$v) {
            $v['show_alias'] = $v['is_show']?'显示':'隐藏';
        }
        unset($v);

        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:16
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->adPositionModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * saveAdPosition
     * 保存广告位，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/10
     * Time：下午5:16
     * @param $inputData
     * @return mixed
     */
    public function saveAdPosition($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['title']) && $inputData['title']){
            $saveData['title'] = $inputData['title'];
        }
        if (isset($inputData['unique_identify']) && $inputData['unique_identify']){
            $saveData['unique_identify'] = $inputData['unique_identify'];
        } else {
            $saveData['unique_identify'] = null;
        }
        if (isset($inputData['image']) && $inputData['image']){
            $saveData['image'] = $inputData['image'];
        }
        if (isset($inputData['video_id'])){
            $saveData['video_id'] = $inputData['video_id'];
        }
        if (isset($inputData['c_type'])){
            $saveData['c_type'] = $inputData['c_type']?:null;
        }
        if (isset($inputData['url'])){
            $saveData['url'] = $inputData['url'];
        }
        if (isset($inputData['is_show'])){
            $saveData['is_show'] = $inputData['is_show'];
        }
        if (isset($inputData['additional'])){
            $saveData['additional'] = $inputData['additional'];
        }
        if (isset($inputData['description'])){
            $saveData['description'] = $inputData['description'];
        }
        $id = $this->adPositionModel->saveInfo($saveData);

        return $id;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:17
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->adPositionModel->getInfo($id);
        $info['is_show'] = (string)$info['is_show'];
        $info['image_info'] = $this->attachmentService->getInfo($info['image']);
        if ($info['video_id']) {
            $info['video_info'] = $this->videoService->getInfo($info['video_id']);
        }
        return $info;
    }

    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:25
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->adPositionModel->deleteInfo($id);

        return $info;
    }

    /**
     * getListByIdentify
     * 模糊匹配唯一标识获取list
     * 协定只模糊匹配内容后半段
     * User：YM
     * Date：2020/2/10
     * Time：下午5:25
     * @param string $identify
     * @return mixed
     */
    public function getListByIdentify($identify = '')
    {
        $list = $this->adPositionModel->getListByIdentify($identify);
        foreach ($list as $k => $v) {
            $info = $this->attachmentService->getInfo($v['image']);
            $list[$k]['image_url'] = $info['full_path'];
        }
        return $list;
    }

    /**
     * getInfoByWhere
     * 根据条件获取信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:26
     * @param array $where
     * @return mixed
     */
    public function getInfoByWhere($where = [])
    {
        $info = $this->adPositionModel->getInfoByWhere($where);
        if ($info) {
            $tmp = $this->attachmentService->getInfo($info['image']);
            $info['image_url'] = $tmp['full_path'];
        }
        return $info;
    }
}