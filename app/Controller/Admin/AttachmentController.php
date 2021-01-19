<?php
declare (strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Exception\DatabaseExceptionHandler;
use Crypto\Rand;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use http\Exception;
use App\Exception\BusinessException;
use Core\Plugins\BaiDu\Lbs;
use Hyperf\DbConnection\Db;
use App\Models\IpRegion;
use Hyperf\Utils\Parallel;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Constants\StatusCode;
use App\Models\Attachment;
use Core\Plugins\FileUpload;

/**
 * AttachmentController
 * 附件管理
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/attachment")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class AttachmentController extends BaseController
{

    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     *
     * @Inject()
     * @var Attachment
     */
    private $model;


    /**
     * 删除
     * del
     *
     * @RequestMapping(path="del")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 11:02
     */
    public function del()
    {
        if ($this->request->isMethod('post')) {
            if (!$this->request->has($this->model->getKeyName())) {
                return $this->error(StatusCode::ERR_EXCEPTION, '缺少编辑的条件');
            }


            $ids   = explode(",", (string)$this->request->input($this->model->getKeyName()));
            $query = $this->model->query();
            $where = [
                //管理员访问权限
            ];
            $query->where($where);
            $list = $query->whereIn($this->model->getKeyName(), $ids)->get()->toArray();
            if (!$list) {
                throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                    '未找到需要删除的数据');
            }

            $count       = 0;
            $isPseudoDel = $this->model->isPseudoDel();
            $config      = config('upload');
            Db::beginTransaction();
            try {
                foreach ($list as $k => $v) {
                    $db    = Db::table($this->model->getTable())->where([$this->model->getKeyName() => $v[$this->model->getKeyName()]]);
                    $count += $isPseudoDel
                        ? $db->update([$this->model::DELETED_AT => date("Y-m-d H:i:s")])
                        : $db->delete();
                    if (!$isPseudoDel) {
                        $path = $config['upload_path'] . '/' . $config['attachments'] . $v['path'];
                        file_exists($path) && unlink($path);
                    }
                }
                unset($v);
                Db::commit();
            } catch (\Throwable $ex) {
                Db::rollBack();
                throw new DatabaseExceptionHandler(StatusCode::ERR_EXCEPTION_DATABASE, $ex->getMessage(), $ex);
            }

            if ($count) {
                return $this->success([], '成功删除' . $count . '条数据');
            } else {
                return $this->error(StatusCode::ERR_EXCEPTION, '删除失败');
            }
        }

        return $this->error(StatusCode::ERR_EXCEPTION, '访问非法');
    }

    /**
     * list
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $reqParam = $this->request->all();
        $where    = []; //额外条件
        $query    = $this->model->query()->where($where);

        [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $query);
        $total = $querys
            ->orderBy($sort, $order)
            ->count();

        $list = $querys
            ->orderBy($sort, $order)
            ->offset($offset)->limit($limit)
            ->get()
            ->toArray();

        $config = config('upload');
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['path'] = '/' . $config['rewrite'] . '/' . $config['attachments'] . $v['path'];
                $list[$k]['url']  = env('CDN_DOMAIN', '') . $list[$k]['path'];
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

}