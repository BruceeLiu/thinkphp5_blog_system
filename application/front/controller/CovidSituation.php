<?php


namespace app\front\controller;


use data\util\ApiResult;
use think\exception\HttpException;
use think\Log;
use think\response\Json;

class CovidSituation extends Base
{
    use ApiResult;

    public const RESPONSE_SUCCESS_CODE = 200;

    protected static $app_code = '516724302b2f478994e67a6b8b1df6f1';

    protected static $app_key = '203909636';

    protected static $app_secret = '516724302b2f478994e67a6b8b1df6f1';

    public function index()
    {
        return $this->fetch();
    }

    /**
     * 各省疫情详情
     * @return Json
     */
    public function situations()
    {
        $res = $this->curl();
        if ($res['code'] !== 200) {
            return json(self::failResult('请求过于频繁,请稍后重试'));
        }
        $res['data'] = is_array($res['data']) ? $res['data'] : json_decode($res['data'], true);
        $data = [];
        $cureData = [];
        $deathData = [];
        foreach ($res['data']['provinceArray'] as $k => $v) {
            $name = $this->getProvinceName($v['childStatistic']);
            $data[] = [
                'name' => $name,
                'value' => $v['totalConfirmed']
            ];
            $cureData = [
                'name' => $name,
                'value' => $v['totalCured']
            ];
            $deathData[] = [
                'name' => $name,
                'value' => $v['totalDeath']
            ];
        }

        $title = '中国疫情图(截止时间:'.$res['data']['dataSourceUpdateTime']['updateTime'].') 数据来源:'.$res['data']['dataSourceUpdateTime']['dataSource'];
        return json(self::successResult('数据整合成功', ['structure' => $this->createMapCovidDataStructure($title, $data,$cureData,$deathData)]));
    }

    /**
     * 生成中国疫情图的数据结构
     * @param string $title
     * @param array $data
     * @param array $cureData
     * @param array $deathData
     * @return array
     */
    private function createMapCovidDataStructure(string $title, array $data,array $cureData=[],array $deathData=[])
    {
        return [
            'title' => [
                'text' => $title,
                'left' => 'center'
            ],
            'tooltip' => [
                'trigger' => 'item'
            ],
            'legend' => [
                'orient' => 'vertical',
                'left' => 'left',
                'data' => ['中国疫情图']
            ],
            'visualMap' => [
                'type' => 'piecewise',
                'pieces' => [
                    ['min' => 1000, 'max' => 1000000, 'label' => '大于等于1000人', 'color' => '#372a28'],
                    ['min' => 500, 'max' => 999, 'label' => '确诊500-999人', 'color' => '#4e160f'],
                    ['min' => 100, 'max' => 499, 'label' => '确诊100-499人', 'color' => '#974236'],
                    ['min' => 10, 'max' => 99, 'label' => '确诊10-99人', 'color' => '#ee7263'],
                    ['min' => 1, 'max' => 9, 'label' => '确诊1-9人', 'color' => '#f5bba7']
                ],
                'color' => ['#E0022B', '#E09107', '#A3E00B']
            ],
            'toolbox' => [
                'show' => true,
                'orient' => 'vertical',
                'left' => 'right',
                'top' => 'center',
                'feature' => [
                    'mark' => ['show' => true],
                    'dataView' => ['show' => true, 'readOnly' => false],
                    'restore' => ['show' => true],
                    'saveAsImage' => ['show' => true]
                ]
            ],
            'roamController' => [
                'show' => true,
                'left' => 'right',
                'mapTypeControl' => [
                    'china' => true
                ]
            ],
            'series' => [[
                'name' => '累计确诊数',
                'type' => 'map',
                'mapType' => 'china',
                'roam' => false,
                'label' => [
                    'show' => true,
                    'color' => 'rgb(249, 249, 249)'
                ],
                'data' => $data
            ]]
        ];
    }

    private function getProvinceName(string $name)
    {

        if ($name === '北京市'){
            $value = '北京';
        }else if($name === '天津市'){
            $value = '天津';
        }else if($name === '上海市'){
            $value = '上海';
        }else if($name === '重庆市'){
            $value = '重庆';
        }else if($name === '内蒙古自治区'){
            $value = '内蒙古';
        }else if($name === '宁夏回族自治区'){
            $value = '宁夏';
        }else if($name === '新疆维吾尔自治区'){
            $value = '新疆';
        }else if($name === '西藏自治区'){
            $value = '西藏';
        }else if($name === '广西壮族自治区'){
            $value = '广西';
        }else if($name === '中国香港'){
            $value = '香港';
        }else if($name === '中国澳门'){
            $value = '澳门';
        }else if($name === '中国台湾'){
            $value = '台湾';
        }else{
            $value = str_replace('省','',$name);
        }
        return $value;
    }

    /**
     * @return array
     */
    protected function curl(): array
    {
        $host = 'https://ncovdata.market.alicloudapi.com';
        $path = "/ncov/cityDiseaseInfoWithTrend";
        $method = "GET";
        $appcode = self::$app_code;
        $headers = array();
        $headers[] = "Authorization:APPCODE " . $appcode;
        $headers[] = "Content-Type: application/json; charset=UTF-8";
        $querys = "";
        $bodys = "";
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //只保留请求回来的数据,截取掉用户信息
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);  // 获取header长度
            // 截取掉header
            $body = substr($response, $headerSize);
            return ['code'=>200,'data'=>$body];
        }

        return ['code'=>$http_code,'data'=>$response];
    }
}