<?php

namespace app\admin\controller;

use data\model\SystemConfig;
use data\model\SystemGroup;
use data\util\ApiResult;
use think\facade\Log;
use think\Request;

class SystemsConfig extends Base
{

    use ApiResult;

    public function index(Request $request)
    {

        $group_id = $request->get('group_id',1);
        $where = [['id','=',$group_id], ['status','=',1]];
        $lists = SystemGroup::where($where)->with('configInfos')->find();
        if ((int)$group_id > 1){
            $lists = is_array($lists) ? $lists : $lists->toArray();
            return json(self::successResult('获取成功',$lists));
        }
        $all_enable_group = SystemGroup::where('status','=',1)->field(['id','name'])->select();
        $this->assign('enable_group',$all_enable_group);
        $this->assign('configs_info',$lists);
        return $this->fetch();
    }

    public function update(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');
        $fields = [
            'value','update_time'
        ];
        foreach ($data['id'] as $k=>$v){
            $res = SystemConfig::update(['value'=>$data['value'][$k],'update_time'=> date('Y-m-d H:i:s',time())],[['id','=',$v]],$fields)
                ->getNumRows();
           $this->errLog('更新情况',['id'=>$v,'value'=>$data['value'][$k]]);
            if ($res < 1){
                return json(self::failResult('更新失败,请稍后重试'));
            }

        }
        return json(self::successResult('配置信息更新成功'));
    }

    private function errLog(string $message,array $data)
    {
        $file_name = '../runtime/log/'.date('Y-m-d',time()).'-Log.txt';
        if (file_exists($file_name)){
            touch($file_name);
        }
        $error_data = ['message'=>$message,'data'=>$data,'date'=>date('Y-m-d H:i:s',time())];
        file_put_contents($file_name,json_encode($error_data,JSON_UNESCAPED_UNICODE).PHP_EOL,FILE_APPEND);
        return true;
    }

}
