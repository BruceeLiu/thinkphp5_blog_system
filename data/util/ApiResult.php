<?php


namespace data\util;


trait ApiResult
{
    public static $api_success_code = 1;

    public static $api_fail_code = 0;

    /**
     * 通用api接口返回形式
     * @param int $code
     * @param string $message
     * @param array $data
     * @return array
     */
    public static function apiResult(int $code, string $message, array $data = []) : array
    {
        return [
            'code'=>$code,
            'message'=>$message,
            'data' => $data
        ];
    }

    /**
     * 通用成功API接口返回形式
     * @param string $message
     * @param array $data
     * @return array
     */
    public static function successResult(string $message, array $data = []) : array
    {
        return self::apiResult(self::$api_success_code,$message,$data);
    }

    /**
     * 通用失败API接口返回形式
     * @param string $message
     * @param array $data
     * @return array
     */
    public static function failResult(string $message, array $data = []) : array
    {
        return self::apiResult(self::$api_fail_code,$message,$data);
    }

}