<?php

declare(strict_types=1);

namespace Core\Repositories\Home;

use App\Constants\RedisCode;
use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;
use http\Exception\BadConversionException;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Core\Common\Extend\CardApi\Bk\Tools as BkApi;
use Core\Common\Container\Redis;


/**
 * orderSubmit
 * 订单提交
 * @package Core\Repositories\Home
 *
 * @property \Core\Services\AttachmentService $attachmentService
 */
class orderSubmitRepository extends BaseRepository
{

    /**
     * @var Redis
     */
    private $Redis;

    /**
     * @var BkApi
     */
    private $BkApi;

    public function __construct()
    {
        $this->BkApi = make(BkApi::class);
        $this->Redis = make(Redis::class);
    }

    /**
     * 默认模板
     * default
     * @param $inputData
     *
     * @return mixed
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 15:41
     */
    public function default($inputData)
    {
        $product = Db::table('product_sale')
//            ->select('product_access.*', 'product_access.*')
            ->join('product_access', 'product_access.id', '=', 'product_sale.access')
            ->where(['product_sale.status' => 1, 'product_sale.id' => $inputData['sid']])
            ->first();
        if($product == null){
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '商品不存在或已停售');
        }
        $params = [  //统一检查参数规则
                     $inputData
        ];
        $ValidationAccess = $this->ValidationAccess($params, $product);
        if($ValidationAccess !== true){
            throw new BusinessException(StatusCode::ERR_EXCEPTION, $ValidationAccess);
        }
        switch ($product->api_model){
            case 'BkApi':
                if($product->captcha_switch){
                    if(!isset($reqParam['captchaInfo']['captcha'])){
                        throw new BusinessException(StatusCode::ERR_EXCEPTION,'验证码错误');
                    }

                    // TODO
                    //...
                }
                //                $num = (isset($params['num']) && $params['num'] == "10") ? "10" : "100";
//                $region = $this->BkApi->getAscriptionCode((int)$params['province'], (int)$params['city']);
//                if(!$region){
//                    return $this->error(StatusCode::ERR_EXCEPTION, '获取接口中归属地信息失败，请联系管理员处理');
//                }
//                $data = [
//                    'num'              => $num,
//                    'province'         => $region['province_name'],
//                    'city'             => $region['city_name'],
//                    'productCode'      => $product->kind,
//                    'development_code' => $this->BkApi->config['development_code'],
//                ];
//                if (isset($params['searchNumber']) && $params['searchNumber'] != '') {
//                    if(!is_numeric($params['searchNumber'])){
//                        return $this->error(StatusCode::ERR_EXCEPTION, '只能搜索数字，请重新输入');
//                    }
//                    $data = array_merge($data,
//                        ['searchNumber' => $params['searchNumber']]);
//                }
//
//                $key = RedisCode::SELECT_PHONES . buildStringHash(json_encode($data));
//                if($res = $this->Redis->get($key)){
//                    $res = json_decode($res,true);
//                    shuffle($res['data']['flexData']);
//                    return $res;
//                }
//
//                $res = $this->BkApi->request('selectPhones', $data);
//                if($res['code'] == StatusCode::SUCCESS && !empty($res['data'])){
//                    $this->Redis->set($key, json_encode($res), 60);
//                }
                return [
                    'code' => StatusCode::SUCCESS,
                    'msg' =>  '提交成功',
                ];
            case "str2":
                throw new BusinessException(StatusCode::ERR_EXCEPTION,'未开放模块');
            default:
                throw new BusinessException(StatusCode::ERR_EXCEPTION,'未开放模块1');
        }
    }


    /**
     * 模型规则检查
     * ValidationAccess
     * @param $inputData
     * @param $product
     *
     * @return bool|string
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 18:03
     */
    private function ValidationAccess($params, $product)
    {
        if($product->age_limit !== 'null'){
            $product->age_limit = json_decode($product->age_limit, true);
            $age = getIdCardAge($params['certId']);
            if($age < $product->age_limit[0] || $age > $product->age_limit[1]){
                return '年龄需在'.$product->age_limit[0] . '至' . $product->age_limit[1] .'才能申请！';
            }
        }
        if($product->pay_limit !== 'null'){
            $product->pay_limit = json_decode($product->pay_limit, true);
            //TODO  下单数量限制
        }
        if($product->stocks < 1){
            return $product->name . '太火爆啦，已经没有库存啦，联系客服试试吧~';
        }

        return true;
    }

    /**
     * saveAttachment
     * 处理回调，保存附件信息
     * User：YM
     * Date：2020/2/7
     * Time：下午6:31
     * @param $inputData
     * @return array
     */
    public function saveAttachment($inputData)
    {
        if(!isset($inputData['aid'])) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'附件ID错误');
        }
        $id = $inputData['aid'];
        $fileInfo = pathinfo($inputData['filename']);
        $result = [
            'state' => 'FAIL',
            'id' => 0,
        ];
        if($fileInfo){
            $fileType = @stristr($inputData['mimeType'], "image");
            $size = $inputData['size'] ?? 0;
            $filePath = '/'.$inputData['filename'];
            $saveData = [
                'id' => $id,
                'title' => $fileInfo['filename'],
                'filename' => $fileInfo['basename'],
                'path' => $filePath,
                'type' => $fileType,
                'size' => $size
            ];
            $this->attachmentService->saveAttachment($saveData);
            $result['state'] = 'SUCCESS';
            $result['id'] = $id;
            $result['title'] = $fileInfo['filename'];
            $result['size'] = $size;
            $result['type'] = $fileInfo['extension'];
            $result['full_path'] = $this->attachmentService->getAttachmentFullUrl($filePath);
            $result['path'] = $filePath;
            $result['original'] = $fileInfo['filename'];
            return [$result];
        }else{
            return [$result];
        }
    }
}