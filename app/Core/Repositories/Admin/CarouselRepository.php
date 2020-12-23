<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CarouselRepository.php
 *
 * User：YM
 * Date：2020/2/9
 * Time：下午5:39
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * CarouselRepository
 * 轮播图管理仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/9
 * Time：下午5:39
 *
 * @property \Core\Services\CarouselService $carouselService
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\CategoryService $categoryService
 */
class CarouselRepository extends BaseRepository
{
    /**
     * getCarouselList
     * 获取列表
     * User：YM
     * Date：2020/2/9
     * Time：下午5:49
     * @param $inputData
     * @return array
     */
    public function getCarouselList($inputData)
    {
        $pagesInfo = $this->carouselService->getPagesInfo($inputData);
        $order = ['order'=>'ASC'];
        $list = $this->carouselService->getList([],$order,$pagesInfo['offset'],$pagesInfo['page_size']);
        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }

    /**
     * saveCarousel
     * 保存
     * User：YM
     * Date：2020/2/9
     * Time：下午5:49
     * @param $data
     * @return mixed
     */
    public function saveCarousel($data)
    {
        if ( !(isset($data['id']) && $data['id']) ) {
            $data['order'] = $this->carouselService->getCarouselCount();
        }
        return $this->carouselService->saveCarousel($data);
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/9
     * Time：下午5:49
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->carouselService->getInfo($id);
        return $info;
    }

    /**
     * deleteInfo
     * 删除信息
     * User：YM
     * Date：2020/2/9
     * Time：下午5:49
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->carouselService->deleteInfo($id);
        return $info;
    }

    /**
     * orderCarousel
     * 轮播图排序
     * User：YM
     * Date：2020/2/9
     * Time：下午5:49
     * @param array $ids
     * @return bool
     */
    public function orderCarousel($ids = [])
    {
        if (count($ids) <= 1) {
            return true;
        }

        $order = 0; // 排序计数器
        foreach ($ids as $v) {
            $saveData = [
                'id' => $v,
                'order' => $order
            ];
            $this->carouselService->saveCarousel($saveData);
            $order++;
        }

        return true;
    }

    /**
     * typeList
     * 获取类别
     * User：YM
     * Date：2020/2/9
     * Time：下午9:40
     * @return mixed
     */
    public function typeList()
    {
        $list = $this->categoryService->getListByIdentify('carousel-category');

        return $list;
    }

}