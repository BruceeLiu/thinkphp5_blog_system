<?php


namespace data\service;


use data\model\AdminUsers;
use data\model\Auths;
use data\model\Roles;
use data\util\ApiResult;

class LoginService
{
    use ApiResult;

    /**
     * @var int 账户启用状态码
     */
    protected static $account_enable_code = 1;

    /**
     * 普通登录
     * @param string $username
     * @param string $password
     * @return array
     */
    public function defaultLogin(string $username,string $password) : array
    {
        $where = [
            ['username|phone|email','=',$username]
        ];
        return $this->loginCheck($where,$password);
    }

    /**
     * 登录检查
     * @param array $defaultWhere
     * @param string $password
     * @return array
     */
    protected function loginCheck(array $defaultWhere,string $password) : array
    {
        $field = ['id','username','password','phone','email','role_id','status'];

        $user_info = AdminUsers::where($defaultWhere)->field($field)->find();
        if ($user_info === null){
            return ApiResult::failResult('没有找到当前账户信息,请重新输入!');
        }

        if (!$this->passwordVerify($password,$user_info['password'])){
            return ApiResult::failResult('密码错误,请重新输入!');
        }

        if ($user_info['status'] !== self::$account_enable_code){
            return ApiResult::failResult('当前账户已被禁用,请联系管理员!');
        }

        $role_info = Roles::where('id','=',$user_info['role_id'])->find();

        if ($role_info === null){
            return ApiResult::failResult('没有找到当前账户的角色信息,请联系管理员!');
        }

        if ($role_info['status'] !== self::$account_enable_code){
            return ApiResult::failResult('没有找到当前账户的角色信息已被禁用,请联系管理员');
        }
        $auth_service = new AuthService();
        if ($role_info['auth_id'] !== AuthService::AUTH_ALL){
            $auth_id_arr = explode(',',$role_info['auth_id']);
            $auth_true = $auth_service->getAuthTree($auth_id_arr);
        }else{
            $auth_id_arr = Auths::where('status','=',1)->column('id');
            $auth_true = $auth_service->getAuthTree($auth_id_arr);
        }

        $user_info['create_at'] = time();

        return ApiResult::successResult('用户登录成功!',[
            'user_info'=>$user_info,
            'role_info' => $role_info,
            'auth_true' => $auth_true
        ]);
    }

    /**
     * 密码验证
     * @param string $password
     * @param string $salt
     * @return bool
     */
    private function passwordVerify(string $password,string $salt) : bool
    {
        return password_verify($password,$salt);
    }

    /**
     * 密码加密
     * @param string $password
     * @return bool|string
     */
    private function passwordHash(string $password)
    {
        return password_hash($password,PASSWORD_DEFAULT);
    }

}