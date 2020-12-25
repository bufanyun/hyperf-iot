<?php

declare(strict_types=1);

namespace Core\Repositories\Home;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;

/**
 * orderSubmit
 * 订单提交
 * @package Core\Repositories\Home
 *
 * @property \Core\Services\AttachmentService $attachmentService
 */
class OrderSubmitRepository extends BaseRepository
{

    /**
     * 默认模板
     * default
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 14:48
     */
    public function default(array $inputData)
    {
//        throw new BusinessException(StatusCode::ERR_EXCEPTION,'附件ID错误');
        return $inputData;
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