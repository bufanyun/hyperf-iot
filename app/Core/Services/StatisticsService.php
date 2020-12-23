<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * StatisticsService.php
 *
 * User：YM
 * Date：2020/2/18
 * Time：下午5:14
 */


namespace Core\Services;


/**
 * StatisticsService
 * 数据统计服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/18
 * Time：下午5:14
 *
 * @property \App\Models\Log $logModel
 * @property \App\Models\IpRegion $ipRegionModel
 */
class StatisticsService extends BaseService
{
    /**
     * getFlowDataByDay
     * 请求流量
     * User：YM
     * Date：2020/2/18
     * Time：下午5:59
     * @param $inputData
     * @param $range
     * @return array
     */
    public function getFlowDataByDay($inputData, $range)
    {
        $list = $this->logModel->getFlowData($inputData,'time_day');
        $list = $this->formatFlowData($range, $list, 'time_day');
        return $list;
    }

    /**
     * getFlowHourData
     * 请求流量
     * User：YM
     * Date：2020/2/18
     * Time：下午9:52
     * @param $inputData
     * @param $range
     * @return array
     */
    public function getFlowDataByHour($inputData, $range)
    {
        $list = $this->logModel->getFlowData($inputData,'time_hour');
        $list = $this->formatFlowData($range, $list, 'time_hour');
        return $list;
    }

    /**
     * formatFlowData
     * 格式化流量数据
     * User：YM
     * Date：2020/2/18
     * Time：下午9:12
     * @param $timeRange
     * @param $data
     * @param $column
     * @return array
     */
    public function formatFlowData($timeRange, $data, $column)
    {
        $result = [
            [
                'name' => 'PV',
                'type' => 'line',
                'itemStyle' => ['color' => '#F56C6C'],
                'data' => [],
                'data_field' => 'num',
            ],
            [
                'name' => 'UV',
                'type' => 'line',
                'itemStyle' => ['color' => '#409EFF'],
                'data' => [],
                'data_field' => 'uv',
            ],
            [
                'name' => 'IP',
                'type' => 'line',
                'itemStyle' => ['color' => '#67C23A'],
                'data' => [],
                'data_field' => 'ip',
            ],
        ];
        if($data){
            $data = array_column($data, null, $column);
        }
        foreach($timeRange as $row){
            if(isset($data[$row])){
                foreach($result as $key => $item){
                    $result[$key]['data'][] = $data[$row][$item['data_field']];
                }
            }else{
                foreach($result as $key => $item){
                    $result[$key]['data'][] = 0;
                }
            }
        }
        return $result;
    }

    /**
     * getRegionData
     * 请求地域
     * User：YM
     * Date：2020/2/19
     * Time：下午9:09
     * @param $inputData
     * @return array
     */
    public function getRegionData($inputData)
    {
        $list = $this->logModel->getRegionData($inputData);
        $res = [];
        foreach ($list as $v) {
            $tmp = $this->ipRegionModel->getInfo($v['city_id']);
            if ( !(isset($tmp['lng']) && $tmp['lng'] && isset($tmp['lat']) && $tmp['lat']) ) {
                continue;
            }
            $res[] = [
                'name' => $tmp['name'],
                'value' => [$tmp['lng'],$tmp['lat'],$v['value'],$v['uv'],$v['ip']]
            ];
        }
        return $res;
    }
}