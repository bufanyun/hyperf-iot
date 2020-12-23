<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * AdPositionRepository.php
 *
 * User：YM
 * Date：2020/2/10
 * Time：下午5:02
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * AdPositionRepository
 * 广告位管理仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/10
 * Time：下午5:02
 *
 * @property \Core\Services\AdPositionService $adPositionService
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\VideoService $videoService
 * @property \Core\Services\CategoryService categoryService
 */
class AdPositionRepository extends BaseRepository
{
    /**
     * getAdPositionList
     * 获取列表
     * User：YM
     * Date：2020/2/10
     * Time：下午5:03
     * @param $inputData
     * @return array
     */
    public function getAdPositionList($inputData)
    {
        $pagesInfo = $this->adPositionService->getPagesInfo($inputData);
        $order = ['order'=>'ASC'];
        $list = $this->adPositionService->getList([],$order,$pagesInfo['offset'],$pagesInfo['page_size']);
        foreach ($list as $k => $v) {
            $info = $this->attachmentService->getInfo($v['image']);
            $list[$k]['image_url'] = $info['full_path'];
            $videoInfo = $this->videoService->getInfo($v['video_id']);
            $list[$k]['aliyun_video_id'] = $videoInfo['aliyun_video_id']??'';
            $list[$k]['video_status'] = $videoInfo['status']??0;
        }

        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }

    /**
     * saveAdPosition
     * 保存
     * User：YM
     * Date：2020/2/10
     * Time：下午5:04
     * @param $data
     * @return mixed
     */
    public function saveAdPosition($data)
    {
        if ( isset($data['aliyun_video_id']) && $data['aliyun_video_id']) {
            $videoData = [
                'title' => $data['title'],
                'intro' => $data['description'],
                'aliyun_video_id' => $data['aliyun_video_id'],
                'filename' => $data['filename']
            ];
            $data['video_id'] = $this->videoService->saveInfo($videoData);
        }

        return $this->adPositionService->saveAdPosition($data);
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:07
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->adPositionService->getInfo($id);
        return $info;
    }

    /**
     * deleteInfo
     * 删除信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:07
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->adPositionService->deleteInfo($id);
        return $info;
    }

    /**
     * getVideoPreviewInfo
     * 获取视频的预览信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:07
     * @param $data
     * @return array
     */
    public function getVideoPreviewInfo($data)
    {
        $data = [
            'play_info' => '',
            'auth_info' => ''
        ];

        return $data;
    }

    /**
     * typeList
     * 获取类别
     * User：YM
     * Date：2020/2/10
     * Time：下午5:07
     * @return mixed
     */
    public function typeList()
    {
        $list = $this->categoryService->getListByIdentify('ad_position-category');
        return $list;
    }
}