<?php

namespace app\admin\controller;

use data\model\AdminUsers;
use data\service\LoginService;
use data\util\ApiResult;
use think\captcha\Captcha;
use think\Controller;
use think\Request;
use think\Response;
use think\response\Json;
use SU;

class Login extends Controller
{
    use ApiResult;

    /**
     * @var LoginService
     */
    protected $service;


    public function initialize()
    {
        $this->service = new LoginService();
    }


    public function login()
    {
        if (!empty(SU::getSessionUserInfo())){
            $this->error('当前用户已登陆,请勿重复登陆!','admin/index/index');
        }
        return $this->fetch();
    }

    /**
     * 普通登录
     * @param Request $request
     * @return Json
     */
    public function defaultLogin(Request $request)
    {
        //数据接收
        $data = $request->post();

        $rule = [
            'username|账户名'=>'require|chsDash',
            'password|密码'=>'require|min:6|max:36',
            'verifyCode|验证码' => 'require|captcha'
        ];

        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }

        //登录验证
        $res = $this->service->defaultLogin($data['username'],$data['password']);
        if ((int)$res['code'] !== 1){
            return json(ApiResult::failResult($res['message']));
        }

       /* //生成token
        $access_token = AdminUsers::tokenRefresh($res['data']['user_info']['id']);
        if (empty($access_token)){
            return json(ApiResult::failResult('access_token生成失败,请稍后重试'));
        }*/
        SU::setSessionUserInfo($res['data']['user_info']);
        SU::setSessionUserRoleInfo($res['data']['role_info']);
        SU::setSessionUserAuthInfo($res['data']['auth_true']);
        return  json(ApiResult::successResult('用户登录成功!'));

    }

    /**
     * 用户退出后台
     */
    public function loginOut()
    {
       /* $data = $request->post();
        $update_field = [
            'access_token' => '',
            //access_token有效期(单位:秒)
            'access_token_expire_in' => 0,
            'access_token_create_at' => 0
        ];
        $update = AdminUsers::where([
            ['access_token','=',$data['access_token']]
        ])->update($update_field);*/

        SU::deleteLoginUserInfo();

        $this->success('用户退出成功!','admin/login/login');

    }

    /**
     * Token刷新
     * @param Request $request
     * @return Json
     */
    public function refreshToken(Request $request)
    {
        $data = $request->post();
        $field = ['id','username','password','phone','email','role_id','status'];
        $get_user_info = AdminUsers::where([
            ['access_token','=',$data['access_token']]
        ])->field($field)->find();

        if ($get_user_info === null){
            return json(ApiResult::failResult('未找到用户信息'));
        }

        if ($get_user_info['status'] !== 1){
            return json(ApiResult::failResult('当前账户已被禁用,请联系管理员'));
        }

        //生成token
        $access_token = AdminUsers::tokenRefresh($get_user_info['id']);
        if (empty($access_token)){
            return json(ApiResult::failResult('access_token生成失败,请稍后重试'));
        }

        return json(ApiResult::successResult('用户登录成功!', [
                'user_info'=>$get_user_info,
                'access_token'=>$access_token
            ]));
    }

    /**
     * 刷新验证码
     * @return Response
     */
    public function verify()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }

}
