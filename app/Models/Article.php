<?php

declare (strict_types=1);
namespace App\Models;

/**
 * @property int $id 
 * @property string $user_id 
 * @property string $title 
 * @property int $category_id 
 * @property string $category_ids 
 * @property int $category_2_id 
 * @property string $category_2_ids 
 * @property string $source 
 * @property string $excerpt 
 * @property string $additional 
 * @property string $content 
 * @property int $attachment 
 * @property int $cover 
 * @property string $published_time 
 * @property int $is_published 
 * @property int $is_top 
 * @property int $is_recommend 
 * @property int $order 
 * @property int $hits 
 * @property int $comment 
 * @property string $seo_title 
 * @property string $seo_keywords 
 * @property string $seo_description 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class Article extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'title', 'category_id', 'category_ids', 'category_2_id', 'category_2_ids', 'source', 'excerpt', 'additional', 'content', 'attachment', 'cover', 'published_time', 'is_published', 'is_top', 'is_recommend', 'order', 'hits', 'comment', 'seo_title', 'seo_keywords', 'seo_description', 'created_at', 'updated_at', 'deleted_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'category_id' => 'integer', 'category_2_id' => 'integer', 'attachment' => 'integer', 'cover' => 'integer', 'is_published' => 'integer', 'is_top' => 'integer', 'is_recommend' => 'integer', 'order' => 'integer', 'hits' => 'integer', 'comment' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getCount
     * 重写父类的该方法，用于条件查询计算总数
     * User：YM
     * Date：2020/2/11
     * Time：下午9:22
     * @param array $where
     * @return int
     */
    public function getCount($where = [])
    {
        $query = $this->query();
        foreach ($where as $k => $v) {
            if ($k === 'title') {
                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
                continue;
            }
            if ($k === 'start_time') {
                $query = $query->where($this->table . '.published_time', '>', $v . ' 00:00:00');
                continue;
            }
            if ($k === 'end_time') {
                $query = $query->where($this->table . '.published_time', '<', $v . ' 23:59:59');
                continue;
            }
            if ($k == 'category_ids') {
                $query = $query->whereIn($this->table . '.category_id', $v);
                continue;
            }
            $query = $query->where($this->table . '.' . $k, $v);
        }
        $query = $query->count();
        return $query > 0 ? $query : 0;
    }
    /**
     * getList
     * 获取视频列表
     * User：YM
     * Date：2020/2/11
     * Time：下午9:22
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.user_id', $this->table . '.title', $this->table . '.category_id', $this->table . '.category_ids', $this->table . '.source', $this->table . '.additional', $this->table . '.excerpt', $this->table . '.content', $this->table . '.cover', $this->table . '.published_time', $this->table . '.is_published', $this->table . '.is_top', $this->table . '.is_recommend', $this->table . '.hits', $this->table . '.comment', $this->table . '.seo_title', $this->table . '.seo_keywords', $this->table . '.seo_description', $this->table . '.created_at');
        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($k === 'title') {
                $query = $query->where($this->table . '.' . $k, 'LIKE', '%' . $v . '%');
                continue;
            }
            if ($k === 'start_time') {
                $query = $query->where($this->table . '.published_time', '>', $v . ' 00:00:00');
                continue;
            }
            if ($k === 'end_time') {
                $query = $query->where($this->table . '.published_time', '<', $v . ' 23:59:59');
                continue;
            }
            if ($k == 'category_ids') {
                $query = $query->whereIn($this->table . '.category_id', $v);
                continue;
            }
            if ($v || $v != null) {
                $query = $query->where($this->table . '.' . $k, $v);
            }
        }
        // 追加排序
        if ($order && is_array($order)) {
            foreach ($order as $k => $v) {
                $query = $query->orderBy($this->table . '.' . $k, $v);
            }
        }
        // 是否分页
        if ($limit) {
            $query = $query->offset($offset)->limit($limit);
        }
        $query = $query->get();
        return $query ? $query->toArray() : [];
    }
}