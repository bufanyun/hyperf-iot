<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * AttachmentRepository.php
 *
 * User：YM
 * Date：2020/2/7
 * Time：下午6:26
 */


namespace Core\Repositories\Home;


use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;

/**
 * AttachmentRepository
 * 附件处理
 * @package Core\Repositories\Home
 * User：YM
 * Date：2020/2/7
 * Time：下午6:26
 *
 * @property \Core\Services\AttachmentService $attachmentService
 */
class AttachmentRepository extends BaseRepository
{
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