<?php

declare(strict_types=1);


namespace Core\Repositories\Common\Bufan;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;
use Hyperf\DbConnection\Db;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;

/**
 * 开卡记录
 * Class ImplRepository
 *
 * @package Core\Repositories\Common\Bufan
 * author MengShuai <133814250@qq.com>
 * date 2021/01/02 21:33
 */
class ImplRepository extends BaseRepository
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct()
    {
        $Container    = ApplicationContext::getContainer();
        $this->logger = $Container->get(LoggerFactory::class)->get(__CLASS__);
    }

    /**
     * qh模版 -- 创建订单
     * templateBkApi
     * @param array $inputData
     * @param array $Ascription
     * @param object $product
     * author MengShuai <133814250@qq.com>
     * date 2021/01/11 18:01
     */
    public function templateQhApi(array $inputData, array $Ascription, object $product): void
    {
        if ($inputData['job_number'] === 'bufanyun' && isset($inputData['sub_agent'])) {
            $agent_id = Db::connection('bufan')->table('admin')->where(['id' => (int)$inputData['sub_agent']])->exists() ? (int)$inputData['sub_agent'] : 1;
            $insert   = [
                'admin_id'    => $agent_id,
                'type'        => $product->name,
                'ip'          => getClientIp(),
                'province'    => $Ascription[0],
                'city'        => $Ascription[1],
                'number'      => $inputData['phoneNum'],
                'name'        => $inputData['name'],
                'idcard'      => $inputData['cardNumber'],
                'tel'         => $inputData['phone'],
                'newname'     => $inputData['name'],
                'newprovince' => $inputData['province'],
                'newcity'     => $inputData['city'],
                'newdistrict' => $inputData['country'],
                'address'     => $inputData['shippingAddress'],
                'addtime'     => time(),
            ];
            $this->createOrder($insert);
        }
    }

    /**
     * bk模版 -- 创建订单
     * templateBkApi
     * @param array $inputData
     * @param array $Ascription
     * @param array $Area
     * @param object $product
     * author MengShuai <133814250@qq.com>
     * date 2021/01/02 21:54
     */
    public function templateBkApi(array $inputData, array $Ascription, array $Area, object $product): void
    {
        if ($inputData['job_number'] === 'bufanyun' && isset($inputData['sub_agent'])) {
            $agent_id = Db::connection('bufan')->table('admin')->where(['id' => (int)$inputData['sub_agent']])->exists() ? (int)$inputData['sub_agent'] : 1;
            $insert   = [
                'admin_id'    => $agent_id,
                'type'        => $product->name,
                'ip'          => getClientIp(),
                'province'    => $Ascription['province_name'],
                'city'        => $Ascription['city_name'],
                'number'      => $inputData['numInfo']['number'],
                'name'        => $inputData['certInfo']['certName'],
                'idcard'      => $inputData['certInfo']['certId'],
                'tel'         => $inputData['certInfo']['contractPhone'],
                'newname'     => $inputData['certInfo']['certName'],
                'newprovince' => $Area['province_name'],
                'newcity'     => $Area['city_name'],
                'newdistrict' => $Area['district_name'],
                'address'     => $inputData['postInfo']['address'],
                'addtime'     => time(),
            ];
            $this->createOrder($insert);
        }
    }

    /**
     * gt模版 -- 创建订单
     * templateGtApi
     * @param array $inputData
     * @param array $Area
     * @param object $product
     * author MengShuai <133814250@qq.com>
     * date 2021/01/02 22:26
     */
    public function templateGtApi(array $inputData, array $Area, object $product): void
    {
        if ($inputData['job_number'] === 'bufanyun' && isset($inputData['sub_agent'])) {
            $agent_id = Db::connection('bufan')->table('admin')->where(['id' => (int)$inputData['sub_agent']])->exists() ? (int)$inputData['sub_agent'] : 1;
            $insert   = [
                'admin_id'    => $agent_id,
                'type'        => $product->name,
                'ip'          => getClientIp(),
                'name'        => $inputData['certInfo']['certName'],
                'idcard'      => $inputData['certInfo']['certId'],
                'tel'         => $inputData['certInfo']['contractPhone'],
                'newname'     => $inputData['certInfo']['certName'],
                'newprovince' => $Area['province_name'],
                'newcity'     => $Area['city_name'],
                'newdistrict' => $Area['district_name'],
                'address'     => $inputData['postInfo']['address'],
                'addtime'     => time(),
            ];
            $this->createOrder($insert);
        }
    }


    /**
     * 创建同步订单
     * createOrder
     *
     * @param array $insert
     * author MengShuai <133814250@qq.com>
     * date 2021/01/02 21:45
     */
    public function createOrder(array $insert): void
    {
        Db::connection("bufan")->beginTransaction();
        try {
            $res = Db::connection('bufan')->table('impl')->insert($insert);
            Db::connection("bufan")->commit();
        } catch (\Throwable $ex) {
            Db::connection("bufan")->rollBack();
            $this->logger->info('布帆云订单插入失败,' . __LINE__ . '行：' . json_encode($insert,
                    JSON_UNESCAPED_UNICODE) . "\r\n 错误提示：" . $ex->getMessage());
            throw new BusinessException(StatusCode::ERR_EXCEPTION,
                $ex->getMessage());
        }
        if (!$res) {
            Db::connection("bufan")->rollBack();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,
                '订单提交失败，稍后再试！');
        }
    }
}