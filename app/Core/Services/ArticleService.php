<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * ArticleService.php
 *
 * User：YM
 * Date：2020/2/11
 * Time：下午9:13
 */


namespace Core\Services;


/**
 * ArticleService
 * 文章服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/11
 * Time：下午9:13
 *
 * @property \App\Models\Article $articleModel
 * @property \App\Models\ArticleAttachment $articleAttachmentModel
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\CategoryService $categoryService
 * @property \Core\Services\UserService $userService
 */
class ArticleService extends BaseService
{
    /**
     * getList
     * 条件获取文章列表
     * User：YM
     * Date：2020/2/11
     * Time：下午9:15
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = ['is_top' => 'DESC','is_recommend' => 'DESC'], $offset = 0, $limit = 0)
    {
        $list = $this->articleModel->getList($where,$order,$offset,$limit);

        foreach ($list as &$v) {
            $coverInfo = $this->attachmentService->getInfo($v['cover']);
            $v['cover_pc_url'] = $coverInfo['full_path']??'';
            $categoryInfo = $this->categoryService->getInfo($v['category_id']);
            $v['category_alias'] = $categoryInfo['display_name']??'';
            $userInfo = $this->userService->getInfo($v['user_id']);
            $v['author_name'] = $userInfo['nickname']??'';
            $v['published_alias'] = $v['is_published']?'已发布':'未发布';
            $v['recommend_alias'] = $v['is_recommend']?'已推荐':'未推荐';
            $v['top_alias'] = $v['is_top']?'已置顶':'未置顶';
            $v['title_alias'] = $v['title'] && mb_strlen($v['title']) > 32?mb_substr($v['title'],0,32).'...':'';
            unset($v['content']);
        }
        unset($v);

        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:15
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->articleModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * saveArticle
     * 保存文章，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/11
     * Time：下午9:36
     * @param $inputData
     * @return null
     */
    public function saveArticle($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['user_id']) && $inputData['user_id']){
            $saveData['user_id'] = $inputData['user_id'];
        }
        if (isset($inputData['title']) && $inputData['title']){
            $saveData['title'] = $inputData['title'];
        }
        if (isset($inputData['category_id']) && $inputData['category_id']){
            $saveData['category_id'] = $inputData['category_id'];
        }
        if (isset($inputData['category_ids']) && $inputData['category_ids']){
            $saveData['category_ids'] = $inputData['category_ids'];
        }
        if (isset($inputData['category_2_id'])){
            $saveData['category_2_id'] = $inputData['category_2_id'];
        }
        if (isset($inputData['category_2_ids'])){
            $saveData['category_2_ids'] = $inputData['category_2_ids'];
        }
        if (isset($inputData['source'])){
            $saveData['source'] = $inputData['source'];
        }
        if (isset($inputData['excerpt'])){
            $saveData['excerpt'] = $inputData['excerpt'];
        }
        if (isset($inputData['additional'])){
            $saveData['additional'] = $inputData['additional '];
        }
        if (isset($inputData['order'])){
            $saveData['order'] = $inputData['order'];
        }
        if (isset($inputData['content'])){
            $saveData['content'] = $inputData['content'];
        }
        if (isset($inputData['cover'])){
            $saveData['cover'] = $inputData['cover'];
        }
        if (isset($inputData['attachment'])){
            $saveData['attachment'] = $inputData['attachment'];
        }
        if (isset($inputData['published_time'])){
            $saveData['published_time'] = $inputData['published_time'];
        }
        if (isset($inputData['is_published'])){
            $saveData['is_published'] = $inputData['is_published'];
        }
        if (isset($inputData['is_top'])){
            $saveData['is_top'] = $inputData['is_top'];
        }
        if (isset($inputData['is_recommend'])){
            $saveData['is_recommend'] = $inputData['is_recommend'];
        }
        if (isset($inputData['hits'])){
            $saveData['hits'] = $inputData['hits'];
        }
        if (isset($inputData['comment'])){
            $saveData['comment'] = $inputData['comment'];
        }
        if (isset($inputData['seo_title'])){
            $saveData['seo_title'] = $inputData['seo_title'];
        }
        if (isset($inputData['seo_keywords'])){
            $saveData['seo_keywords'] = $inputData['seo_keywords'];
        }
        if (isset($inputData['seo_description'])){
            $saveData['seo_description'] = $inputData['seo_description'];
        }
        $id = $this->articleModel->saveInfo($saveData);

        return $id;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:16
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->articleModel->getInfo($id);
        $info['cover_info'] = $this->attachmentService->getInfo($info['cover']);
        $info['attachment_info'] = $this->attachmentService->getInfo($info['attachment']);
        $tmp = explode(',', $info['category_ids']??'');
        $tmpArr = [];
        foreach ($tmp as $v) {
            $tmpArr[] = (int)$v;
        }
        $tmp2 = explode(',', $info['category_2_ids']??'');
        $tmpArr2 = [];
        foreach ($tmp2 as $v) {
            $tmpArr2[] = (int)$v;
        }
        $info['category_ids'] = $tmpArr;
        $info['category_2_ids'] = $tmpArr2;
        return $info;
    }

    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:16
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->articleModel->deleteInfo($id);

        return $info;
    }

    /**
     * getArticleAttachmentList
     * 获取文章附件
     * User：YM
     * Date：2020/2/11
     * Time：下午9:17
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public function getArticleAttachmentList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->articleAttachmentModel->getList($where,$order,$offset,$limit);

        foreach ($list as &$v) {
            $tmp = $this->attachmentService->getInfo($v['attachment_id']);
            $v['attachment_url'] = $tmp['full_path']??'';
        }
        unset($v);

        return $list;
    }

    /**
     * saveArticleAttachment
     * 保存文章附件
     * User：YM
     * Date：2020/2/11
     * Time：下午9:17
     * @param $inputData
     * @return mixed
     */
    public function saveArticleAttachment($inputData)
    {
        $saveData = [];
        $type = 'update';
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        } else {
            $type = 'create';
        }
        if (isset($inputData['title']) && $inputData['title']){
            $saveData['title'] = $inputData['title'];
        }
        if (isset($inputData['attachment_id'])){
            $saveData['attachment_id'] = $inputData['attachment_id'];
        }
        if (isset($inputData['intro'])){
            $saveData['intro'] = $inputData['intro'];
        }
        if (isset($inputData['article_id'])){
            $saveData['article_id'] = $inputData['article_id'];
        }
        $id = $this->articleAttachmentModel->saveInfo($saveData,$type);

        return $id;
    }

    /**
     * deleteAttachmentInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:34
     * @param $id
     * @return mixed
     */
    public function deleteAttachmentInfo($id)
    {
        $info = $this->articleAttachmentModel->deleteInfo($id);

        return $info;
    }

    /**
     * getAttachmentInfo
     * 获取附件信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:34
     * @param $id
     * @return mixed
     */
    public function getAttachmentInfo($id)
    {
        $info = $this->articleAttachmentModel->getInfo($id);
        $tmp = $this->attachmentService->getInfo($info['attachment_id']);
        $info['type'] = $tmp['type']??'';
        $info['path'] = $tmp['path']??'';
        $info['full_path'] = $tmp['full_path']??'';
        return $info;
    }

    /**
     * getAttachmentList
     * 条件获取附件列表
     * User：YM
     * Date：2020/2/11
     * Time：下午9:20
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public function getAttachmentList($where = [], $order = ['created_at' => 'DESC'], $offset = 0, $limit = 0)
    {
        $list = $this->articleAttachmentModel->getList($where,$order,$offset,$limit);

        return $list;
    }

    /**
     * getAttachmentPagesInfo
     * 获取附件分页信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:20
     * @param array $where
     * @return mixed
     */
    public function getAttachmentPagesInfo($where = [])
    {
        $pageInfo = $this->articleAttachmentModel->getPagesInfo($where);

        return $pageInfo;
    }
}