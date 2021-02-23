<?php


namespace data\util;


use think\facade\Session;

class SessionUtil
{
    public const SESSION_USER_INFO = 'session_user_info';

    public const SESSION_USER_ROLE_INFO = 'session_user_role_info';

    public const SESSION_USER_AUTH_INFO = 'session_user_auth_info';

    private $expire_in = 7200;

    public function setSessionUserInfo($value)
    {
        Session::set(self::SESSION_USER_INFO,$value);
    }

    public function getSessionUserInfo()
    {
        return Session::get(self::SESSION_USER_INFO);
    }

    public function setSessionUserRoleInfo($value)
    {
        Session::set(self::SESSION_USER_ROLE_INFO,$value);
    }

    public function getSessionUserRoleInfo()
    {
        return Session::get(self::SESSION_USER_ROLE_INFO);
    }

    public function setSessionUserAuthInfo($value)
    {
        Session::set(self::SESSION_USER_AUTH_INFO,$value);
    }

    public function getSessionUserAuthInfo()
    {
        return Session::get(self::SESSION_USER_AUTH_INFO);
    }


    public function deleteLoginUserInfo()
    {
        Session::delete(self::SESSION_USER_INFO);
        Session::delete(self::SESSION_USER_AUTH_INFO);
        Session::delete(self::SESSION_USER_ROLE_INFO);
    }

    public function setExpireIn($value){
        return $this->expire_in = $value;
    }

    public function getExpireIn()
    {
        return $this->expire_in;
    }

}