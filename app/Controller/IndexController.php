<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

class IndexController extends BaseController
{
    public function index()
    {
        $method = $this->request->getMethod();
        return $this->success(['$method' => $method]);
    }
}
