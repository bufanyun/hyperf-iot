<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LinkService.php
 *
 * User：YM
 * Date：2020/2/10
 * Time：下午10:34
 */


namespace Core\Services;


/**
 * LinkService
 * 友情链接服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/10
 * Time：下午10:34
 *
 * @property \App\Models\AdLink $adLinkModel
 * @property \Core\Services\AttachmentService $attachmentService
 */
class LinkService extends BaseService
{
    /**
     * getList
     * 条件获取友情链接列表
     * User：YM
     * Date：2020/2/10
     * Time：下午10:34
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {

        $list = $this->adLinkModel->getList($where,$order,$offset,$limit);
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
     * Time：下午10:35
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->adLinkModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * saveLink
     * 保存友情链接，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/10
     * Time：下午10:35
     * @param $inputData
     * @return mixed
     */
    public function saveLink($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['title']) && $inputData['title']){
            $saveData['title'] = $inputData['title'];
        }
        if (isset($inputData['image'])){
            $saveData['image'] = $inputData['image'];
        }
        if (isset($inputData['target']) && $inputData['target']){
            $saveData['target'] = $inputData['target'];
        }
        if (isset($inputData['is_show'])){
            $saveData['is_show'] = $inputData['is_show'];
        }
        if (isset($inputData['c_type'])){
            $saveData['c_type'] = $inputData['c_type']?:null;
        }
        if (isset($inputData['url'])){
            $saveData['url'] = $inputData['url'];
        }
        if (isset($inputData['order'])){
            $saveData['order'] = $inputData['order'];
        }
        if (isset($inputData['additional'])){
            $saveData['additional'] = $inputData['additional'];
        }
        if (isset($inputData['description'])){
            $saveData['description'] = $inputData['description'];
        }
        $id = $this->adLinkModel->saveInfo($saveData);

        return $id;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/10
     * Time：下午10:35
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->adLinkModel->getInfo($id);
        $info['image_info'] = $this->attachmentService->getInfo($info['image']);
        $info['is_show'] = (string)$info['is_show'];
        return $info;
    }

    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/10
     * Time：下午10:35
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->adLinkModel->deleteInfo($id);

        return $info;
    }
}