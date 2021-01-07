<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * upload.php
 *
 * 文件上传配置
 *
 * User：YM
 * Date：2020/2/5
 * Time：下午9:09
 */

return [
    // 上传文件保存配置，本地local，阿里云oss
    'upload_save' => env('UPLOAD_SAVE', 'local'),

    // 文件上传允许类型
    'file_allow_files' => [
        "png", "jpg", "jpeg", "gif", "bmp",
        "flv", "swf", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg",
        "ogg", "ogv", "mov", "wmv", "mp4", "webm", "mp3", "wav", "mid",
        "rar", "zip", "tar", "gz", "7z", "bz2", "cab", "iso",
        "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf", "txt", "md", "xml","apk"
    ],
    // 文件上传大小限制（单位字节B） 5MB
    'file_max_size' => 1024*1024*5,
    // 静态资源根目录
    'upload_path' => BASE_PATH.'/public/static/',
    // 上传文件目录
    'attachments' => 'attachments',
    // 文件名:P生产环境，D开发环境，T测试环境
    'file_name_format' => env('UPLOAD_PREFIX', 'T').'{time}_{rand:5}',

];
