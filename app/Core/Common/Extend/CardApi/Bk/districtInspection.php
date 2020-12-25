<?php
namespace Core\Common\Extend\CardApi\Bk;

use Hyperf\DbConnection\Db;

/**
 * 地区格式检查
 * Class districtInspection
 *
 * @package extend\Bk
 */
class districtInspection
{
    /**
     * 收货地表名
     * @var string
     */
    private $area_table = 'com_area';
    /**
     * 归属地省、市、区域字段
     * @var string
     */
    private $area_field = ['province_name', 'city_name', 'district_name'];
    private $area_field_code = ['province_code', 'city_code', 'district_code'];
    //////////////////////////////////////////////////////////////////////

    /**归属地表名
     * @var string
     */
    private $ascription_table = 'com_ascription';
    /**
     * 归属地省、市字段
     * @var string
     */
    private $ascription_field = ['province_name', 'city_name'];
    private $ascription_field_code = ['ess_province_code', 'ess_city_code'];

    /**
     * 获取接口中的归属地格式信息
     * getAscription
     * @param  string  $province
     * @param  string  $city
     *
     * @return mixed
     */
    public function getAscription(string $province, string $city) :? array
    {
        if($province === "" || $city === ""){
            return null;
        }
        $province = $this->filter_city($province);
        $city = $this->filter_city($city);
        //  var_export([$province, $city]);exit;
        $where = [
            [$this->ascription_field[0], 'LIKE', "%{$province}%"],
            [$this->ascription_field[1], 'LIKE', "%{$city}%"],
        ];
        $res = Db::table($this->ascription_table)->where($where)->first();
        return $res ? (array)$res : [];
    }

    /**
     * 获取接口中的收货地区格式信息
     * getArea
     * @param  string  $province
     * @param  string  $city
     * @param  string  $district
     *
     * @return mixed
     */
    public function getArea(string $province, string $city, string $district) :? array
    {
        if($province === "" || $city === "" || $district === ""){
            return null;
        }
        $province = $this->filter_city($province);
        $city = $this->filter_city($city);
        $district = $this->filter_city($district);
        //  var_export([$province, $city, $district]);exit;
        $where = [
            [$this->area_field[0], 'LIKE', "%{$province}%"],
            [$this->area_field[1], 'LIKE', "%{$city}%"],
            [$this->area_field[2], 'LIKE', "%{$district}%"],
        ];
        $res = Db::table($this->area_table)->where($where)->first();
        return $res ? (array)$res : [];
    }

    /**
     * 通过编码获取接口中的归属地格式信息
     * getAscriptionCode
     * @param  int  $province
     * @param  int  $city
     *
     * @return array|null
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 11:36
     */
    public function getAscriptionCode(int $province, int $city) :? array
    {
        if($province === "" || $city === ""){
            return null;
        }
        //  var_export([$province, $city]);exit;
        $where = [
            [$this->ascription_field_code[0], '=', $province],
            [$this->ascription_field_code[1], '=', $city],
        ];
        $res = Db::table($this->ascription_table)->where($where)->first();
        return $res ? (array)$res : [];
    }

    /**
     * 通过编码获取接口中的收货地区格式信息
     * getAreaCode
     * @param  int  $province
     * @param  int  $city
     * @param  int  $district
     *
     * @return array|null
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 11:36
     */
    public function getAreaCode(int $province, int $city, int $district) :? array
    {
        if($province === "" || $city === "" || $district === ""){
            return null;
        }
        //  var_export([$province, $city, $district]);exit;
        $where = [
            [$this->area_field_code[0], '=', $province],
            [$this->area_field_code[1], '=', $city],
            [$this->area_field_code[2], '=', $district],
        ];
        $res = Db::table($this->area_table)->where($where)->first();
        return $res ? (array)$res : [];
    }

    /**
     * 过滤地区中的市、县、区等附属词
     * filter_city
     * @param string $string
     * @return string
     */
    private function filter_city(string  $string) : string
    {
        $string = trim($string);
        if ($string == '西藏自治区') {
            $string = '西藏';
        }elseif ($string == '新疆维吾尔自治区') {
            $string = '新疆';
        }elseif ($string == '宁夏回族自治区') {
            $string = '宁夏';
        }elseif ($string == '广西壮族自治区') {
            $string = '广西';
        }elseif ($string == '内蒙古自治区') {
            $string = '内蒙古';
        }

        $string = str_replace('省','',$string);
        $string = str_replace('地区','',$string);
        $string = str_replace('州','',$string);
        $string = str_replace('市','',$string);
        $string = str_replace('区','',$string);
        $string = str_replace('县','',$string);

        return $string;
    }

}