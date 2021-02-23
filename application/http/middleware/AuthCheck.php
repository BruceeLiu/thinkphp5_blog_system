<?php

namespace app\http\middleware;

use data\model\AdminUsers;
use data\model\SystemLog;
use data\service\AuthService;
use think\facade\Request;
use SU;

class AuthCheck
{

    protected static $auth_no_check = false;

    public function handle($request, \Closure $next)
    {
        $method = Request::method();
        $module = strtolower(Request::module());
        $controller = strtolower(Request::controller());
        $action = strtolower(Request::action());
        //权限菜单
        $auth_check = config('auth_check.');
        //判断访问的方法是否存在
        if (!isset($auth_check[$module][$controller][$action])){
            return redirect(url('admin/login/login'));
        }

        //判断访问属性是否一致
        if (strtolower($method) !== strtolower($auth_check[$module][$controller][$action]['method'])){
            return redirect(url('admin/login/login'));
        }

        //判断是否不需要权限验证
        if ($auth_check[$module][$controller][$action]['is_check'] === self::$auth_no_check){
            return $next($request);
        }

        /*$access_token = explode(' ',Request::header('authorization'));
        $request_info =  Request::request();
        $request_access_token = $request_info['access_token'] ?? '';
        $token = empty($access_token) ? $request_access_token  : $access_token[1] ?? '';
        $this->errLog('请求参数信息>>>>',[$request_info,[$module.'/'.$controller.'/'.$action]]);*/

        //获取用户信息
       /* $user_field = ['id','role_id','access_token_expire_in','access_token_create_at'];
        $user_info = AdminUsers::where('access_token','=',$token)->field($user_field)->find();
        if ($user_info === null){
            return redirect(url('admin/login/login'));
        }
        $use_time = time() - $user_info['access_token_create_at'];
        if ($use_time > $user_info['access_token_expire_in']){
            return redirect(url('admin/login/login'));
        }*/

        $time = time();
        $user_info = SU::getSessionUserInfo();
        if (empty($user_info)){
            return redirect(url('admin/login/login'));
        }
        $user_role_info = SU::getSessionUserRoleInfo();
        $user_auth_info = SU::getSessionUserAuthInfo();
        $expire_in = SU::getExpireIn();
        $use_time = $time - $user_info['create_at'];
        $time_remaining = $expire_in - $use_time;



        if ($time_remaining <= 60 && $time_remaining > 0){
            SU::deleteLoginUserInfo();
            SU::setSessionUserInfo($user_info);
            SU::setSessionUserRoleInfo($user_role_info);
            SU::setSessionUserAuthInfo($user_auth_info);
        }

        if ($use_time > $expire_in){
            SU::deleteLoginUserInfo();
            return redirect(url('admin/login/login'));
        }

        //权限验证
        $request_rule = $module.'/'.$controller.'/'.$action;

        //添加日志
        $data = [
            'uid'=>$user_info['id'],
            'url'=>$request_rule,
            'ip' => Request::ip()
        ];
        $this->addSystemLog($data);


        //如果访问首页,是当前登录用户通用页面
        if ($request_rule === 'admin/index/index'){
            return $next($request);
        }

        if (!$this->auth_check($user_info['role_id'],$request_rule)){
            return redirect(url('admin/login/login'));
        }

        return $next($request);
    }

    protected function auth_check(int $roleId,string $requestRule)
    {
        $auth_service = new AuthService();
        $check_res = $auth_service->authCheck($roleId,$requestRule);
        return !($check_res['code'] !== 1);
    }

    protected function addSystemLog(array $data)
    {
        $filed = [
            'ip','url','uid','create_time','update_time'
        ];
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        return SystemLog::create($data,$filed);
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
