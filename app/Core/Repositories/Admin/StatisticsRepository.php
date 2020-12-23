<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * StatisticsRepository.php
 *
 * User：YM
 * Date：2020/2/18
 * Time：下午4:59
 */


namespace Core\Repositories\Admin;


use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;

/**
 * StatisticsRepository
 * 数据统计仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/18
 * Time：下午4:59
 *
 * @property \Core\Services\StatisticsService $statisticsService
 */
class StatisticsRepository extends BaseRepository
{
    /**
     * getFlowData
     * 统计流量
     * User：YM
     * Date：2020/2/18
     * Time：下午9:14
     * @param $inputData
     * @return array
     */
    public function getFlowData($inputData)
    {
        if(!isset($inputData['start_time']) || empty($inputData['start_time'])){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'开始时间为空');
        }
        if(!isset($inputData['end_time']) || empty($inputData['end_time'])){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'结束时间为空');
        }
        if ( (strtotime($inputData['end_time'])-strtotime($inputData['start_time']))/86400 > 30 ) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'最大查询30天');
        }
        if($inputData['start_time'] == $inputData['end_time']){
            $inputData['start_time'] = strtotime($inputData['start_time'].' 00:00:00');
            $inputData['end_time'] = strtotime($inputData['end_time']. '23:59:59');
            $timeRange = $this->getTimeRangeHour($inputData['start_time'], $inputData['end_time']);
            $data = $this->statisticsService->getFlowDataByHour($inputData, $timeRange);
        }else{
            $inputData['start_time'] = strtotime($inputData['start_time'].' 00:00:00');
            $inputData['end_time'] = strtotime($inputData['end_time']. '23:59:59');
            $timeRange = $this->getTimeRangeDay($inputData['start_time'], $inputData['end_time']);
            $data = $this->statisticsService->getFlowDataByDay($inputData, $timeRange);
        }
        return [
            'x_axis' => $timeRange,
            'series' => $data,
            'legend_data' => array_pluck($data,'name')
        ];
    }

    /**
     * getTimeRangeDay
     * 获取天日期
     * User：YM
     * Date：2020/2/18
     * Time：下午9:15
     * @param $startTime
     * @param $endTime
     * @return array
     */
    public function getTimeRangeDay($startTime, $endTime)
    {
        $result = [];
        for($time = $startTime; $time<= $endTime; $time+= 86400){
            $result[] = date('Y-m-d', $time);
        }
        return $result;
    }

    /**
     * getTimeRangeHour
     * 获取小时日期
     * User：YM
     * Date：2020/2/18
     * Time：下午9:15
     * @param $startTime
     * @param $endTime
     * @return array
     */
    public function getTimeRangeHour($startTime, $endTime)
    {
        $result = [];
        $endTime = $endTime + 1;
        for($time = $startTime; $time<= $endTime; $time+= 3600){
            $result[] = date('Y-m-d H:00:00', $time);
        }
        return $result;
    }

    /**
     * getRegionData
     * 请求地域
     * User：YM
     * Date：2020/2/19
     * Time：下午9:11
     * @param $inputData
     * @return array
     */
    public function getRegionData($inputData)
    {
        if(!isset($inputData['start_time']) || empty($inputData['start_time'])){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'开始时间为空');
        }
        if(!isset($inputData['end_time']) || empty($inputData['end_time'])){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'结束时间为空');
        }
        if ( (strtotime($inputData['end_time'])-strtotime($inputData['start_time']))/86400 > 30 ) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'最大查询30天');
        }
        $inputData['start_time'] = strtotime($inputData['start_time'].' 00:00:00');
        $inputData['end_time'] = strtotime($inputData['end_time']. '23:59:59');
        $data = $this->statisticsService->getRegionData($inputData);

        return [
            'all_data' => $data
        ];
    }
}