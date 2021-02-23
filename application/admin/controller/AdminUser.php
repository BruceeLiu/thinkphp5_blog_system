<?php

namespace app\admin\controller;

use data\service\AdminUserService;
use data\util\ApiResult;
use think\exception\DbException;
use think\Request;
use think\response\Json;

class AdminUser extends Base
{
    use ApiResult;

    /**
     * @var AdminUserService
     */
    protected $service;

    public function initialize(): void
    {
        parent::initialize();
        $this->service = new AdminUserService();
    }

    /**
     * 管理员列表
     * @param Request $request
     * @return mixed
     * @throws DbException
     */
    public function index(Request $request)
    {
        $keywords = $request->get('keywords', '');
        $list = $this->service->userLists($keywords);
        $page = $list->render();
        $this->assign('users', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    /**
     * 管理员详情
     * @param Request $request
     * @return mixed|Json
     */
    public function detail(Request $request)
    {
        $uid = $request->get('uid', 0);
        if ($uid < 1) {
            return json(ApiResult::failResult('请选择要查看的用户'));
        }
        $detail = $this->service->userDetail($uid);
        $role_info = $this->service->enableRoleLists();
        $this->assign('detail', $detail);
        $this->assign('roles',$role_info['data']);
        return $this->fetch('admin_user/detail/detail');
    }

    /**
     * 添加管理员用户界面
     * @return mixed
     */
    public function add()
    {
        $role_info = $this->service->enableRoleLists();
        $this->assign('roles',$role_info['data']);
        return $this->fetch('admin_user/detail/add');
    }

    /**
     * 添加用户
     * @param Request $request
     * @return Json
     */
    public function addUser(Request $request) : Json
    {
        $data = $request->except(['create_time','update_time'],'post');
        $rule = [
            'username|账户名'=>'require|alphaDash|unique:admin_users',
            'phone|电话' => 'require|mobile',
            'email|电子邮箱' => 'require|email',
            'role_id|角色ID' => 'require|integer'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $data['password'] = password_hash('123456',PASSWORD_DEFAULT);
        $res = $this->service->userAdd($data);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    /**
     * 更新管理员
     * @param Request $request
     * @return Json
     */
    public function update(Request $request) : Json
    {
        $data = $request->except(['create_time','update_time'],'post');
        $rule = [
            'username|账户名'=>'require|chsDash|unique:admin_users',
            'phone|电话' => 'require|mobile',
            'email|电子邮箱' => 'require|email',
            'role_id|角色ID' => 'require|integer'
        ];

        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $res = $this->service->userUpdate($data,$data['id']);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    /**
     * 删除用户
     * @param Request $request
     * @return Json
     */
    public function del(Request $request) : Json
    {
        $uid = $request->get('uid', 0);
        if ($uid < 1) {
            return json(ApiResult::failResult('请选择要查看的用户'));
        }
        $res = $this->service->userDel($uid);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    /**
     * 启用/禁用 用户
     * @param Request $request
     * @return Json
     */
    public function isUse(Request $request) : Json
    {
        $uid = $request->get('uid', 0);
        if ($uid < 1) {
            return json(ApiResult::failResult('请选择要查看的用户'));
        }
        $res = $this->service->isUse($uid);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

}
