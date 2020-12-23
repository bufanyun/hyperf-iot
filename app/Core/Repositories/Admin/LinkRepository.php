<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LinkRepository.php
 *
 * User：YM
 * Date：2020/2/10
 * Time：下午10:32
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * LinkRepository
 * 友情链接管理仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/10
 * Time：下午10:32
 *
 * @property \Core\Services\LinkService $linkService
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\CategoryService $categoryService
 */
class LinkRepository extends BaseRepository
{
    /**
     * getLinkList
     * 获取列表
     * User：YM
     * Date：2020/2/10
     * Time：下午10:38
     * @param $inputData
     * @return array
     */
    public function getLinkList($inputData)
    {
        $pagesInfo = $this->linkService->getPagesInfo($inputData);
        $order = ['order'=>'ASC','id'=>'DESC'];
        $list = $this->linkService->getList([],$order,$pagesInfo['offset'],$pagesInfo['page_size']);
        foreach ($list as $k => $v) {
            $info = $this->attachmentService->getInfo($v['image']);
            $list[$k]['image_url'] = $info['full_path']??'';
        }
        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }

    /**
     * saveLink
     * 保存
     * User：YM
     * Date：2020/2/10
     * Time：下午10:38
     * @param $data
     * @return mixed
     */
    public function saveLink($data)
    {
        return $this->linkService->saveLink($data);
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
        $info = $this->linkService->getInfo($id);
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
        $info = $this->linkService->deleteInfo($id);
        return $info;
    }

    /**
     * orderLink
     * 友情链接排序
     * User：YM
     * Date：2020/2/10
     * Time：下午10:38
     * @param array $ids
     * @return bool
     */
    public function orderLink($ids = [])
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
            $this->linkService->saveLink($saveData);
            $order++;
        }

        return true;
    }

    /**
     * typeList
     * 获取类别
     * User：YM
     * Date：2020/2/10
     * Time：下午10:39
     * @return mixed
     */
    public function typeList()
    {
        $list = $this->categoryService->getListByIdentify('link-category');

        return $list;
    }
}