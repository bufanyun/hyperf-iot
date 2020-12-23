<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * SettingService.php
 *
 * 文件描述
 *
 * User：YM
 * Date：2020/2/5
 * Time：下午5:54
 */


namespace Core\Services;


/**
 * SettingService
 * 类的介绍
 * @package Core\Services
 * User：YM
 * Date：2020/2/5
 * Time：下午5:54
 *
 * @property \App\Models\Setting $settingModel
 */
class SettingService extends BaseService
{
    /**
     * getListByNames
     * 通过name值取数据列表
     * User：YM
     * Date：2020/2/5
     * Time：下午8:52
     * @param array $nameArr
     * @return array
     */
    public function getListByNames($nameArr = [])
    {
        $list = $this->settingModel->getList($nameArr);
        $temp = [];
        foreach ($list as $v) {
            $temp[$v['name']] = $v['value'];
        }
        if (!$temp) {
            foreach ($nameArr as $v) {
                $temp[$v] = '';
            }
        }

        return $temp;
    }

    /**
     * getInfoByName
     * 获取设置信息通过name
     * User：YM
     * Date：2020/2/5
     * Time：下午10:43
     * @param $name
     * @return array
     */
    public function getInfoByName($name)
    {
        $info = $this->settingModel->getInfoByName($name);

        return $info;
    }

    /**
     * saveSetting
     * 保存网站设置
     * User：YM
     * Date：2020/2/5
     * Time：下午10:43
     * @param $inputData
     * @return null
     */
    public function saveSetting($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['name']) && $inputData['name']){
            $saveData['name'] = $inputData['name'];
        }
        if (isset($inputData['value'])){
            $saveData['value'] = $inputData['value'];
        }

        $id = $this->settingModel->saveInfo($saveData);

        return $id;
    }

}