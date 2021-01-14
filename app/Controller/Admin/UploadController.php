<?php

declare(strict_types=1);

namespace App\Controller\Admin;


use App\Constants\StatusCode;
use App\Controller\BaseController;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * UploadController
 * 上传文件
 * @package App\Controller\Admin
 *
 *
 * @Controller(prefix="admin_api/upload")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 * @property \Core\Repositories\Admin\UploadRepository $uploadRepo
 */
class UploadController extends BaseController
{
    /**
     * getUploadToken
     * 获取上传凭证
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_upload_token")
     */
    public function getUploadToken()
    {
        $tokenInfo = $this->uploadRepo->getUploadToken();

        return $this->success($tokenInfo);
    }

    /**
     * uploadFile
     * 文件上传
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="file")
     */
    public function uploadFile()
    {
        $reqParam = $this->request->all();
        $files = $this->request->getUploadedFiles();
        if(empty($files)){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'上传文件为空');
        }
        $res = $this->uploadRepo->uploadFiles($files,$reqParam);
        return $this->success($res);
    }
}