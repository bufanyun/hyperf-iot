<?php

declare(strict_types=1);

namespace Core\Repositories\Home;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;


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
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

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
        var_export([
            '$this->validationFactory' => $this->validationFactory,
        ]);

        $ValidatorFactoryInterface = make(ValidatorFactoryInterface::class);
        $validator = $this->validationFactory->make(
            $inputData,
            [
                'sid' => 'required',
                'job_number' => 'required',
//                'certInfo.certName' => 'required|string',
            ],
            [
                'sid.required' => '商品id不能为空',
                'job_number.required' => '推广工号不能为空',
//                'province.required' => '归属省不能为空',
//                'certInfo.certName.required' => '收货人姓名不能为空',
                //                'province.string' => '归属省格式错误',
                //                'city.string' => '归属市格式错误',
            ]
        );

        if ($validator->fails()){
            return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
        }


        return $this->success(StatusCode::SUCCESS, '提交成功');
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