<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CategoryService.php
 *
 * User：YM
 * Date：2020/2/9
 * Time：下午9:41
 */


namespace Core\Services;


/**
 * CategoryService
 * 分类管理服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/9
 * Time：下午9:41
 *
 * @property \App\Models\Category $categoryModel
 * @property \Core\Services\AttachmentService $attachmentService
 */
class CategoryService extends BaseService
{
    /**
     * getList
     * 分类列表
     * User：YM
     * Date：2020/2/9
     * Time：下午9:42
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->categoryModel->getList($where, $order, $offset, $limit);
        foreach ($list as $k => $v) {
            $info = $this->attachmentService->getInfo($v['image']);
            $list[$k]['image_url'] = isset($info['full_path'])?$info['full_path']:'';
        }
        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/9
     * Time：下午9:42
     * @param array $where
     * @return array
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->categoryModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * saveCategoryData
     * 保存分类信息
     * User：YM
     * Date：2020/2/9
     * Time：下午9:42
     * @param $inputData
     * @return null
     */
    public function saveCategoryData($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['display_name']) && $inputData['display_name']){
            $saveData['display_name'] = $inputData['display_name'];
        }
        if (isset($inputData['name'])){
            $saveData['name'] = $inputData['name']?:NULL;
        }
        if (isset($inputData['url'])){
            $saveData['url'] = $inputData['url']?:NULL;
        }
        if (isset($inputData['parent_id'])){
            $saveData['parent_id'] = $inputData['parent_id'];
        }
        if (isset($inputData['order'])){
            $saveData['order'] = $inputData['order'];
        }
        if (isset($inputData['image'])){
            $saveData['image'] = $inputData['image'];
        }
        if (isset($inputData['description'])){
            $saveData['description'] = $inputData['description'];
        }
        if (isset($inputData['additional'])){
            $saveData['additional'] = $inputData['additional'];
        }

        $id = $this->categoryModel->saveInfo($saveData);

        return $id;
    }

    /**
     * getInfo
     * 获取分类详情
     * User：YM
     * Date：2020/2/9
     * Time：下午9:43
     * @param $id
     * @return \App\Models\BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id)
    {
        $info = $this->categoryModel->getInfo($id);
        $info['image_info'] = isset($info['image'])?$this->attachmentService->getInfo($info['image']):[];
        return $info;
    }

    /**
     * deleteInfo
     * 删除分类
     * User：YM
     * Date：2020/2/9
     * Time：下午9:43
     * @param $id
     * @return int
     */
    public function deleteInfo($id)
    {
        $info = $this->categoryModel->deleteInfo($id);
        return $info;
    }

    /**
     * getInfoByWhere
     * 根据条件获取信息
     * User：YM
     * Date：2020/2/9
     * Time：下午9:43
     * @param array $where
     * @return array
     */
    public function getInfoByWhere($where = [])
    {
        $info = $this->categoryModel->getInfoByWhere($where);

        return $info;
    }

    /**
     * getListByIdentify
     * 通过标识，获取对应的分类list
     * User：YM
     * Date：2020/2/9
     * Time：下午9:43
     * @param string $identify
     * @return array
     */
    public function getListByIdentify($identify = '')
    {
        if ($identify) {
            $info = $this->getInfoByWhere(['name' => $identify]);
            $pid = isset($info['id'])?$info['id']:0;
        } else {
            $pid = 0;
        }

        $list = $this->getList([],['order'=>'ASC'],0,0);
        $tree = handleTreeList($list,$pid);

        return $tree;
    }


    /**
     * getListById
     * 通过父id，获取对应的分类list
     * User：YM
     * Date：2020/2/9
     * Time：下午9:44
     * @param int $pid
     * @return array
     */
    public function getListById($pid = 0)
    {
        $pid = $pid?:0;

        $list = $this->getList([],['order'=>'ASC'],0,0);
        $tree = handleTreeList($list,$pid);

        return $tree;
    }

    /**
     * getCategoryCount
     * 获取总数
     * User：YM
     * Date：2020/2/11
     * Time：下午5:51
     * @param array $where
     * @return int
     */
    public function getCategoryCount($where = [])
    {
        $count = $this->categoryModel->getCount($where);

        return $count;
    }
}