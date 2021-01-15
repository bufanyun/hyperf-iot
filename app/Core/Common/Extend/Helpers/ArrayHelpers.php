<?php

declare(strict_types=1);

namespace Core\Common\Extend\Helpers;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Swoole\Coroutine\Http\Client;


class ArrayHelpers
{

    /**
     * 移除多维数组中指定键，支持多个
     * hidden
     *
     * @param array $arr
     * @param array $fields
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/15 21:03
     */
    public static function hidden(array $arr, array $fields): array
    {
        if (count($arr) < 1) {
            return [];
        }
        foreach ($fields as $vo) {
            unset($arr[$vo]);
        }
        unset($vo);
        return $arr;
    }

    /**
     * 多维数组查找，不选择返回字段时直接返回找到的索引key
     * searchColumn
     * @param array $array
     * @param string $key
     * @param string $value
     * @param string $field
     *
     * @return array|bool|false|int|string
     * author MengShuai <133814250@qq.com>
     * date 2021/01/04 15:20
     */
    public static function searchColumn(array $array, string $key, string $value, string $field = '')
    {
        if (empty($array)) {
            return $field == '' ? '' : [];
        }
        $keys = array_column($array, $key);
        $i    = array_search($value, $keys);
        if (!isset($array[$i]) || empty($array[$i])) {
            return $field == '' ? '' : [];
        }
        if ($field == '') {
            return $i;
        }
        return isset($array[$i][$field]) ? $array[$i][$field] : '';
    }
}