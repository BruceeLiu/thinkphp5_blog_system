<?php

namespace data\service;

use data\model\AdminUsers;
use data\model\Roles;
use data\util\ApiResult;
use think\exception\DbException;
use think\Paginator;

class AdminUserService
{

    use ApiResult;

    public const SUPER_USER_UID = 1;

    /**
     * 管理员列表
     * @param string $keywords
     * @param int $per_page
     * @return AdminUsers[]|Paginator
     * @throws DbException
     */
    public function userLists(string $keywords = '', int $per_page = 10)
    {
        $where = [];
        if (!empty($keywords)) {
            $where = [
                ['username|email|phone', 'like', '%' . $keywords . '%']
            ];
        }
        return AdminUsers::with('rolesInfo')->where($where)->paginate($per_page,false,config('paginate.paginate'));
    }

    /**
     * 用户详情
     * @param int $uid
     * @return AdminUsers
     */
    public function userDetail(int $uid) : AdminUsers
    {
        $where = [
            ['id','=',$uid]
        ];
        return AdminUsers::where($where)->find();
    }

    /**
     * 用户新增
     * @param array $data
     * @return array
     */
    public function userAdd(array $data) : array
    {
        $filed = [
            'username','password','phone','email','role_id','create_time','update_time'
        ];
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = AdminUsers::create($data,$filed);
        $id = $res->id ?? 0;
        if ($id < 1){
            return self::failResult('用户信息写入失败');
        }
        return self::successResult('用户信息写入成功');
    }

    /**
     * 用户更新
     * @param array $data
     * @param int $uid
     * @return array
     */
    public function userUpdate(array $data,int $uid)
    {

        if ($uid === self::SUPER_USER_UID){
            return self::failResult('当前用户为超级管理员,无需做修改操作');
        }

        $filed = [
            'username','password','phone','email','role_id','update_time'
        ];
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = AdminUsers::update($data,[['id','=',$uid]],$filed)->getNumRows();
        if ($res < 1){
            return self::failResult('用户信息更新失败');
        }
        return self::successResult('用户信息写入成功');
    }

    /**
     * 用户删除
     * @param int $uid
     * @param bool $isSoftDelete
     * @return array
     */
    public function userDel(int $uid, bool $isSoftDelete = true) : array
    {
        if ($uid === self::SUPER_USER_UID){
            return self::failResult('当前用户为超级管理员,不允许被删除');
        }
        $where = [
            ['id','=',$uid]
        ];
        $detail = AdminUsers::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到用户信息');
        }
        if ((int)$detail['status'] === 1){
            return self::failResult('请先禁用改用户');
        }

        $res = !$isSoftDelete ?AdminUsers::destroy($uid,true) :  AdminUsers::destroy($uid);
        if (!$res){
            return self::failResult('删除失败,请稍后重试');
        }
        return self::successResult('删除成功');
    }

    /**
     * 用户启用/禁用
     * @param int $uid
     * @return array
     */
    public function isUse(int $uid) : array
    {
        if ($uid === self::SUPER_USER_UID){
            return self::failResult('当前用户为超级管理员,不允许被禁用');
        }
        $where = [
            ['id','=',$uid]
        ];
        $detail = AdminUsers::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到用户信息');
        }
        $detail->status = ((int)$detail->status === 1) ? 0 : 1;
        $res = $detail->save();
        if (!$res){
            return self::failResult('启用禁用失败,请稍后重试');
        }
        $status_desc = ((int)$detail->status === 1) ? '启用' : '禁用';
        return self::successResult('当前账户'.$status_desc.'成功');
    }

    /**
     * 获取已启用的角色列表
     * @return array
     */
    public function enableRoleLists() : array
    {
        $roles = Roles::where('status','=',1)->field(['id','name'])->select();
        $role_info = [];
        foreach ($roles as $k=>$v){
            $role_info[] =[
                'id'=>$v['id'],
                'name' => $v['name']
            ];
        }
        return ApiResult::successResult('角色列表获取成功',$role_info);
    }

}