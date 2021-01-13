<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *
 * UserService.php
 *
 * 文件描述
 *
 * User：YM
 * Date：2020/1/8
 * Time：下午5:16
 */


namespace Core\Services;

use App\Constants\StatusCode;
use App\Exception\BusinessException;

/**
 * UserService
 * 类的介绍
 * @package Core\Services
 * User：YM
 * Date：2020/1/8
 * Time：下午5:16
 *
 * @property \APP\Models\User $userModel
 */
class UserService extends BaseService
{
    /**
     * getList
     * 条件获取用户列表
     * User：YM
     * Date：2020/2/5
     * Time：下午4:08
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->userModel->getList($where,$order,$offset,$limit);

        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:09
     * @param array $where
     * @return array
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->userModel->getPagesInfo($where);
        return $pageInfo;
    }

    /**
     * getInfo
     * 获取用户数据
     * User：YM
     * Date：2020/1/8
     * Time：下午7:52
     * @param $id 可以传入数组
     * @param bool $type 是否使用缓存
     * @return \App\Models\BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id,$type=true)
    {
        $type = false;
        $res = $this->userModel->getInfo($id,$type);
        if (count($res) == count($res,1)) {
            unset($res['password']);
            unset($res['session_id']);
            unset($res['deleted_at']);
        } else {
            foreach ($res as &$v) {
                unset($v['password']);
                unset($v['session_id']);
                unset($v['deleted_at']);
            }
            unset($v);
        }

        return $res;
    }

    /**
     * saveUser
     * 保存用户，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/5
     * Time：下午4:50
     * @param $inputData
     * @param bool $type
     * @return null
     * @throws \Exception
     */
    public function saveUser($inputData, $type = false)
    {
        $saveData = [];
        if ($type === true) {
            $saveData['id'] = getUserUniqueId();
        } elseif (isset($inputData['id']) && $inputData['id']) {
            $saveData['id'] = $inputData['id'];
        } else {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '请求参数错误！');
        }
        if (isset($inputData['username']) && $inputData['username']){
            $saveData['username'] = $inputData['username'];
        }
        if (isset($inputData['mobile']) && $inputData['mobile']){
            $saveData['mobile'] = $inputData['mobile'];
        }
        if (isset($inputData['password']) && $inputData['password']){
            $saveData['password'] = encryptPassword($inputData['password']);
        }
        if (isset($inputData['nickname']) && $inputData['nickname']){
            $saveData['nickname'] = $inputData['nickname'];
        }
        if (isset($inputData['email']) && $inputData['email']){
            $saveData['email'] = $inputData['email'];
        }
        if (isset($inputData['avatar'])){
            $saveData['avatar'] = $inputData['avatar'];
        }
        if (isset($inputData['job_number']) && $inputData['job_number']){
            $saveData['job_number'] = $inputData['job_number'];
        }
        $id = $this->userModel->saveInfo($saveData,$type);
        return $id;
    }


    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:55
     * @param $id
     * @return int
     */
    public function deleteInfo($id)
    {
        $info = $this->userModel->deleteInfo($id);

        return $info;
    }


    /**
     * getInfoByWhere
     * 根据条件获取用户信息
     * User：YM
     * Date：2020/1/10
     * Time：上午12:32
     * @param $where
     * @param bool $type 是否多条
     * @return array
     */
    public function getInfoByWhere($where,$type=false)
    {
        $res = $this->userModel->getInfoByWhere($where,$type);
        return $res;
    }

    /**
     * searchUserList
     * 根据搜索条件返回list
     * User：YM
     * Date：2020/2/5
     * Time：下午2:27
     * @param $search
     * @param array $userIds
     * @param array $notIds
     * @param int $limit
     * @return mixed
     */
    public function searchUserList($search, $userIds=[], $notIds = [], $limit = 10)
    {
        $list = $this->userModel->getSearchList($search, $userIds, $notIds, $limit);

        foreach ($list as $k => $v) {
            $list[$k]['value'] = $v['nickname']?$v['mobile'].'('.$v['nickname'].')':$v['mobile'];
        }

        return $list;
    }

}