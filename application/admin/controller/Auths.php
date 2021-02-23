<?php

namespace app\admin\controller;

use data\service\AuthService;
use data\util\ApiResult;
use think\Request;

class Auths extends Base
{

    /**
     * @var AuthService
     */
    protected $service;

    public function initialize()
    {
        parent::initialize();
        $this->service = new AuthService();
    }

    public function index(Request $request)
    {
        $keywords = $request->get('keywords', '');
        $list = $this->service->authLists($keywords);
        $page = $list->render();
        $this->assign('auths', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function detail(Request $request)
    {
        $auth_id = $request->get('auth_id', 0);
        if ($auth_id < 1) {
            return json(ApiResult::failResult('请选择要查看的权限'));
        }
        $detail = $this->service->authDetail($auth_id);
        $this->assign('detail', $detail);
        //$this->assign('roles',$role_info['data']);
        return $this->fetch('auths/detail/detail');
    }

    public function add(Request $request)
    {
        $pid = $request->get('pid',0);
        $this->assign('pid',$pid);
        return $this->fetch('auths/detail/add');
    }

    public function addAuth(Request $request)
    {

        $data = $request->except(['create_time','update_time'],'post');
        $rule = [
            'name|权限名'=>'require|unique:auths',
            'auth_rule|权限路径' => 'require',
            'desc|权限描述' => 'max:255',
            'auth_type|权限类型' => 'require|integer|in:1,2'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $data['pid'] = $data['pid'] ?? 0;
        if ($data['pid'] >= 1){
            $info = \data\model\Auths::where('id','=',$data['pid'])->field(['id','name'])->find();
            if ($info === null){
                return json(ApiResult::failResult('当前权限不存在,请重新选择'));
            }
        }
        $data['status'] = 1;
        $res = $this->service->authAdd($data);
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
            'auth_rule|权限路径' => 'require',
            'desc|权限描述' => 'max:255',
            'auth_type|权限类型' => 'require|integer|in:1,2'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $data['pid'] = $data['pid'] ?? 0;
        if ($data['pid'] >= 1){
            $info = \data\model\Auths::where('id','=',$data['pid'])->field(['id','name'])->find();
            if ($info === null){
                return json(ApiResult::failResult('当前权限不存在,请重新选择'));
            }
        }
        $res = $this->service->authUpdate($data,$data['id']);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }



}
