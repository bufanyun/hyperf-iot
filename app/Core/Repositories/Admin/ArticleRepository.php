<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * ArticleRepository.php
 *
 * User：YM
 * Date：2020/2/11
 * Time：下午9:05
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * ArticleRepository
 * 文章管理仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/11
 * Time：下午9:05
 *
 * @property \Core\Services\ArticleService $articleService
 * @property \Core\Services\CategoryService $categoryService
 * @property \Core\Common\Container\Auth $auth
 */
class ArticleRepository extends BaseRepository
{
    /**
     * getList
     * 获取列表
     * @param $inputData
     * @return array
     */
    public function getList($inputData)
    {
        $pagesInfo = $this->articleService->getPagesInfo($inputData);
        $where = $inputData;
        unset($where['page_size']);
        unset($where['current_page']);
        $order = ['is_top' => 'DESC','is_recommend' => 'DESC','order' => 'ASC'];
        $list = $this->articleService->getList($where,$order,$pagesInfo['offset'],$pagesInfo['page_size']);

        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }

    /**
     * getCategoryList
     * 通过分类标识获取对应的list
     * User：YM
     * Date：2020/2/11
     * Time：下午9:06
     * @return mixed
     */
    public function getCategoryList()
    {
        $list = $this->categoryService->getListByIdentify('article-category');

        return $list;
    }

    /**
     * getCategoryLabelList
     * 获取所有分类作为标签
     * User：YM
     * Date：2020/2/11
     * Time：下午9:06
     * @return mixed
     */
    public function getCategoryLabelList()
    {
        $list = $this->categoryService->getListByIdentify();

        return $list;
    }

    /**
     * saveArticle
     * 保存文章
     * User：YM
     * Date：2020/2/11
     * Time：下午9:07
     * @param $data
     * @return mixed
     */
    public function saveArticle($data)
    {
        if (isset($data['category_ids'])) {
            $data['category_ids'] = implode(',',$data['category_ids']);
        }
        if (isset($data['category_2_ids'])) {
            $data['category_2_ids'] = implode(',',$data['category_2_ids']);
        }
        if (isset($data['article_status']) && is_array($data['article_status'])) {
            $data['is_published'] = in_array('is_published',$data['article_status'])?1:0;
            $data['is_recommend'] = in_array('is_recommend',$data['article_status'])?1:0;
            $data['is_top'] = in_array('is_top',$data['article_status'])?1:0;

        }
        if (!isset($data['id'])) {
            $userInfo = $this->auth->check();
            if ($userInfo) {
                $data['user_id'] = $userInfo['id'];
            }
        }
        return $this->articleService->saveArticle($data);
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:07
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->articleService->getInfo($id);
        $info['article_status'] = [];
        if ($info['is_published']) {
            $info['article_status'][] = 'is_published';
        }
        if ($info['is_recommend']) {
            $info['article_status'][] = 'is_recommend';
        }
        if ($info['is_top']) {
            $info['article_status'][] = 'is_top';
        }

        return $info;
    }

    /**
     * deleteInfo
     * 删除信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:07
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->articleService->deleteInfo($id);
        return $info;
    }

    /**
     * getAttachment
     * 获取文章附件
     * User：YM
     * Date：2020/2/11
     * Time：下午9:08
     * @param $inputData
     * @return mixed
     */
    public function getAttachment($inputData)
    {
        $where = [
            'article_id' => $inputData['id']
        ];
        $list = $this->articleService->getArticleAttachmentList($where);

        return $list;
    }

    /**
     * saveArticleAttachment
     * 保存文章附件
     * User：YM
     * Date：2020/2/11
     * Time：下午9:08
     * @param $data
     * @return mixed
     */
    public function saveArticleAttachment($data)
    {
        return $this->articleService->saveArticleAttachment($data);
    }

    /**
     * deleteAttachmentInfo
     * 删除附件信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:08
     * @param $id
     * @return mixed
     */
    public function deleteAttachmentInfo($id)
    {
        $info = $this->articleService->deleteAttachmentInfo($id);
        return $info;
    }

}