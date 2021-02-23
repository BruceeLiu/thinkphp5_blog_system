<?php

namespace app\admin\controller;

use think\Controller;
use SU;
abstract class Base extends Controller
{

    protected $users_info;

    public function initialize()
    {
        $user_info = SU::getSessionUserInfo();
        $role_info = SU::getSessionUserRoleInfo();
        $auth_tree = SU::getSessionUserAuthInfo();
        $this->assign('user_info',$user_info);
        $this->assign('role_info',$role_info);
        $this->assign('auth_tree',$auth_tree);
        $this->users_info = $user_info;
    }

}
