<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * SettingRepository.php
 *
 * 文件描述
 *
 * User：YM
 * Date：2020/2/5
 * Time：下午5:53
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * SettingRepository
 * 类的介绍
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/5
 * Time：下午5:53
 *
 * @property \Core\Services\SettingService $settingService
 * @property \Core\Services\AttachmentService attachmentService
 */
class SettingRepository extends BaseRepository
{
    /**
     * getSiteInfo
     * 获取站点信息
     * User：YM
     * Date：2020/2/5
     * Time：下午5:57
     * @return mixed
     */
    public function getSiteInfo()
    {
        $siteConfig = config('dictionary.site_set');
        $info = $this->settingService->getListByNames($siteConfig);

        if ( isset($info['web_logo']) && $info['web_logo'] ) {
            $tmp = $this->attachmentService->getInfo($info['web_logo']);
            $info['web_logo_info'] = $tmp;
        }

        return $info;
    }

    /**
     * saveSettingInfo
     * 保存站点设置信息
     * User：YM
     * Date：2020/2/5
     * Time：下午5:57
     * @param array $inputData
     * @return bool
     */
    public function saveSettingInfo($inputData = [])
    {
        $siteConfig = config('dictionary.site_set');
        foreach ($inputData as $k => $v) {
            if (!in_array($k,$siteConfig)) {
                continue;
            }
            $data = [
                'name' => $k,
                'value' => $v
            ];
            $temInfo = $this->settingService->getInfoByName($k);
            if ($temInfo) {
                $data['id'] = $temInfo['id'];
            }
            $this->settingService->saveSetting($data);
        }

        return true;
    }
}