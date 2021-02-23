<?php


namespace data\service;


use data\model\Auths;
use data\model\Roles;
use data\util\ApiResult;

class AuthService
{
    use ApiResult;

    public const AUTH_ALL = '__AUTH_ALL__';

    public static $role_enable_status = 1;

    public function authLists(string $keywords = '', int $per_page=10){
        $where = [];
        if (!empty($keywords)){
            $where[] = [
                'name','like','%'.$keywords.'%'
            ];
        }
        return Auths::where($where)->paginate($per_page,false,config('paginate.paginate'));

    }

    public function authDetail(int $authId)
    {
        $where = [
            ['id','=',$authId]
        ];
        return Auths::where($where)->find();
    }

    public function authAdd(array $data)
    {
        $filed = [
            'name','pid','auth_rule','desc','create_time','update_time','auth_type'
        ];
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = Auths::create($data,$filed);
        $id = $res->id ?? 0;
        if ($id < 1){
            return self::failResult('权限信息写入失败');
        }
        return self::successResult('权限信息写入成功');
    }

    public function authUpdate(array $data, int $authId)
    {
        $filed = [
            'name','pid','auth_rule','desc','create_time','update_time','auth_type'
        ];
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = Auths::update($data,[['id','=',$authId]],$filed);
        $id = $res->id ?? 0;
        if ($id < 1){
            return self::failResult('权限信息更新失败');
        }
        return self::successResult('权限信息更新成功');
    }

    /**
     * 权限检查
     * @param int $role_id - 角色ID
     * @param string $requestRule - 请求路径
     * @return array
     */
    public function authCheck(int $role_id, string $requestRule) : array
    {
        $role_field = ['auth_id','status'];
        $role_info = Roles::where('id',$role_id)->field($role_field)->find();

        if ($role_info === null){
            return ApiResult::failResult('未找到用户的角色信息,请联系管理员');
        }

        if ($role_info['status'] !== self::$role_enable_status){
            return ApiResult::failResult('当前用户角色已禁用,请联系管理员');
        }

        $auth_id_arr = explode(',',$role_info['auth_id']);

        if (empty($auth_id_arr)){
            return ApiResult::failResult('当前用户角色没有权限,请联系管理员');
        }

        if (in_array(self::AUTH_ALL,$auth_id_arr,true)){
            return ApiResult::successResult('当前用户权限验证通过');
        }

        $auth_rule_arr = Auths::where([
            ['status','=',self::$role_enable_status],
            ['id','in',$auth_id_arr]
        ])->column('auth_rule');


        if (!in_array(strtolower($requestRule),$auth_rule_arr,true)){
            return ApiResult::failResult('当前用户没有访问该功能的权限,请联系管理员');
        }

        return ApiResult::successResult('当前用户权限验证通过');
    }

    /**
     * 生成权限树
     * @param array $id_arr - 权限ID
     * @param string $pk - 主键ID字段名
     * @param string $pid - 上级ID字段名
     * @param string $child - 子节点字段名
     * @param int $root - 根节点
     * @return array
     */
    public function getAuthTree(array $id_arr, string $pk = 'id', string $pid = 'pid', string $child = 'child',int $root = 0) : array
    {
        $auth_info = Auths::where('status','=',self::$role_enable_status)
            ->whereIn('id',$id_arr)
            ->where('auth_type','=',1)
            ->select();
        $auth_tree = [];
        foreach ($auth_info as $k=>$v){
            if ($v[$pid] === $root){
                unset($auth_info[$k]);
                if (!empty($auth_info)){
                    $children = $this->getAuthTree($id_arr,$pk,$pid,$child,$v[$pk]);
                    $v[$child] = $children;
                }
                $auth_tree[] = $v;
            }
        }
        return $auth_tree;
    }

}