<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CategoryRepository.php
 *
 * User：YM
 * Date：2020/2/11
 * Time：下午4:58
 */


namespace Core\Repositories\Admin;


use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;

/**
 * CategoryRepository
 * 分类管理
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/11
 * Time：下午4:58
 *
 * @property \Core\Services\CategoryService $categoryService
 */
class CategoryRepository extends BaseRepository
{
    /**
     * getCategoryList
     * 分类管理
     * User：YM
     * Date：2020/2/11
     * Time：下午4:59
     * @return array
     */
    public function getCategoryList()
    {
        $list = $this->categoryService->getList([], ['order' => 'ASC'], 0, 0);
        $tree = handleTreeList($list);
        return $tree;
    }

    /**
     * saveCategory
     * 保存分类信息
     * User：YM
     * Date：2020/2/11
     * Time：下午4:59
     * @param $inputData
     * @return null
     */
    public function saveCategory($inputData)
    {
        if (!isset($inputData['display_name'])){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'分类名称不能为空');
        }
        if (!isset($inputData['name']) && isset($inputData['parent_id']) && !$inputData['parent_id']){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'分类标识不能为空');
        }
        if ( !(isset($inputData['id']) && $inputData['id']) ) {
            $inputData['order'] = $this->categoryService->getCategoryCount(['parent_id' => $inputData['parent_id']]);
        }
        return $this->categoryService->saveCategoryData($inputData);
    }

    /**
     * getCategoryInfo
     * 获取分类详情
     * User：YM
     * Date：2020/2/11
     * Time：下午5:00
     * @param $id
     * @return \App\Models\BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getCategoryInfo($id)
    {
        $info = $this->categoryService->getInfo($id);

        return $info;
    }

    /**
     * deleteInfo
     * 删除信息
     * User：YM
     * Date：2020/2/11
     * Time：下午5:00
     * @param $id
     * @return int
     */
    public function deleteInfo($id)
    {
        if(empty($id)){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'id为空');
        }
        $count = $this->categoryService->getCategoryCount(['parent_id' => $id]);
        if ($count) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'存在子节点不允许删除');
        }

        $info = $this->categoryService->deleteInfo($id);

        return $info;
    }

    /**
     * orderCategory
     * 排序
     * User：YM
     * Date：2020/2/11
     * Time：下午5:01
     * @param array $ids
     * @return bool
     */
    public function orderCategory($ids = [])
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
            $this->categoryService->saveCategoryData($saveData);
            $order++;
        }

        return true;
    }

}