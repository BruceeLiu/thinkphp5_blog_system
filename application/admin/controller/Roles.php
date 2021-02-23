<?php

namespace app\admin\controller;

use data\model\Auths;
use data\service\AuthService;
use data\service\RolesService;
use data\util\ApiResult;
use think\Request;

class Roles extends Base
{

    /**
     * @var RolesService
     */
    protected $service;

    public function initialize()
    {
        parent::initialize();
        $this->service = new RolesService();
    }

    public function index(Request $request)
    {
        $keywords = $request->get('keywords', '');
        $list = $this->service->roleLists($keywords);
        $page = $list->render();
        $this->assign('roles', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function detail(Request $request)
    {
        $role_id= $request->get('role_id', 0);
        if ($role_id < 1) {
            return json(ApiResult::failResult('请选择要查看的角色'));
        }
        $detail = $this->service->roleDetail($role_id);
        $detail['auth_id'] = explode(',',$detail['auth_id']);
        $auth_id_arr = Auths::column('id');
        $auth_service = new AuthService();
        $auth_tree = $auth_service->getAuthTree($auth_id_arr);
        $this->assign('detail', $detail);
        $this->assign('auth_trees', $auth_tree);
        return $this->fetch('roles/detail/detail');
    }

    public function add(Request $request)
    {
        $auth_id_arr = Auths::column('id');
        $auth_service = new AuthService();
        $auth_tree = $auth_service->getAuthTree($auth_id_arr);
        $this->assign('auth_trees', $auth_tree);
        return $this->fetch('roles/detail/add');
    }

    public function addRoles(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');
        $rule = [
            'name|权限名'=>'require|unique:auths',
            'desc|权限描述' => 'max:255',
            'auth_id|权限id' => 'require|array'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $data['auth_id'] = implode(',',$data['auth_id']);
        $res = $this->service->roleAdd($data);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function update(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');
        $rule = [
            'name|权限名'=>'require|unique:auths',
            'desc|权限描述' => 'max:255',
            'auth_id|权限id' => 'require|array'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $data['auth_id'] = implode(',',$data['auth_id']);
        $res = $this->service->roleUpdate($data,$data['id']);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function del(Request $request)
    {
        $roleId = $request->get('role_id', 0);
        if ($roleId < 1) {
            return json(ApiResult::failResult('请选择要删除的角色'));
        }
        $res = $this->service->roleDelete($roleId);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function isUse(Request $request)
    {
        $roleId = $request->get('role_id', 0);
        if ($roleId < 1) {
            return json(ApiResult::failResult('请选择要【启用/禁用】的角色'));
        }
        $res = $this->service->isUse($roleId);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

}
