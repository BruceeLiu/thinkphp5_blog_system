<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use data\util\facade\SessionUtilFacade;
use data\util\facade\WeChatUtilFacade;
use data\util\SessionUtil;
use data\util\WeChatUtil;
use think\Facade;
use think\facade\Env;
use think\facade\Request;
use think\Loader;

Loader::addNamespace('data',Loader::getRootPath() . 'data' . DIRECTORY_SEPARATOR);

$env = '.env';

$domain = Request::ip();

//切换环境文件
if ( $domain === '0.0.0.0' || $domain === '127.0.0.1'){
    $env .= '.local';
}

$env_path = Loader::getRootPath().$env;

Env::load($env_path);

//绑定门面类
Facade::bind([
    SessionUtilFacade::class => SessionUtil::class,
    WeChatUtilFacade::class => WeChatUtil::class
]);

//门面别名
Loader::addClassAlias([
    'SU' => SessionUtilFacade::class,
    'WU' => WeChatUtilFacade::class
]);


if (!function_exists('calDays')){
    /**
     * 计算天数
     * @param int $startTimestamp
     * @param int $endTimestamp
     * @return false|string
     */
    function calDays(int $startTimestamp, int $endTimestamp){
        $timestamp = 86400;
        $days = (int)(($endTimestamp - $startTimestamp)/$timestamp);
        if($days >= 365){
            $year = (int)($days / 365);
            $publish_time = $year.'年前';
        } else if($days >= 30){
            $month = (int)($days / 30);
            $publish_time = $month.'月前';
        } else if($days >= 7){
            $week = (int)($days / 7);
            $publish_time = $week.'周前';
        } else if ($days >= 2){
            $publish_time = $days.'天前';
        } else{
            $publish_time = date('Y-m-d',$startTimestamp);
        }
        return $publish_time;
    }
}


if (!function_exists('sortLists')){
    /**
     * 根据指定字段进行排序
     * @param array $data - 要排序的字段
     * @param string $field - 根据指定字段进行排序
     * @param string $symbol - 从大到小排序还是从小到大排序的符号(默认:从大到小排序)
     * @return array
     */
    function sortLists(array $data,string $field,string $symbol='<')
    {
        $count_data = count($data);
        for ($i=0;$i<$count_data - 1;$i++){
            for ($j=0;$j<$count_data - $i - 1; $j++){
                if ($symbol === '<'){
                    if ($data[$j][$field] < $data[$j + 1][$field]){
                        $temp = $data[$j];
                        $data[$j] = $data[$j + 1];
                        $data[$j + 1] = $temp;
                    }
                }else{
                    if ($data[$j][$field] > $data[$j + 1][$field]){
                        $temp = $data[$j];
                        $data[$j] = $data[$j + 1];
                        $data[$j + 1] = $temp;
                    }
                }
            }
        }
        return $data;
    }
}

