<?php


namespace data\service;


use data\model\Roles;
use data\util\ApiResult;
use think\exception\DbException;
use think\Paginator;

class RolesService
{
    use ApiResult;

    public const SUPER_AUTH_ID = 1;

    /**
     *角色列表
     * @param string $keywords
     * @param int $per_page
     * @return Roles[]|Paginator
     * @throws DbException
     */
    public function roleLists(string $keywords = '', int $per_page=10){
        $where = [];
        if (!empty($keywords)){
            $where[] = [
                'name','like','%'.$keywords.'%'
            ];
        }
        return Roles::where($where)->paginate($per_page,false,config('paginate.paginate'));
    }

    /**
     * 角色详情
     * @param int $roleId
     * @return Roles
     */
    public function roleDetail(int $roleId)
    {
        $where = [
            ['id','=',$roleId]
        ];
        return Roles::where($where)->find();
    }

    /**
     * 角色新增
     * @param array $data
     * @return array
     */
    public function roleAdd(array $data)
    {
        $filed = [
            'name','desc','auth_id','create_time','update_time'
        ];
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = Roles::create($data,$filed);
        $id = $res->id ?? 0;
        if ($id < 1){
            return self::failResult('角色信息写入失败');
        }
        return self::successResult('角色信息写入成功');
    }

    /**
     * 角色修改
     * @param array $data
     * @param int $roleId
     * @return array
     */
    public function roleUpdate(array $data,int $roleId)
    {
        if ($roleId === self::SUPER_AUTH_ID){
            return self::failResult('当前角色为超级管理员,无需进行修改');
        }
        $filed = [
            'name','desc','auth_id','update_time'
        ];
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = Roles::update($data,[['id','=',$roleId]],$filed)->getNumRows();
        if ($res < 1){
            return self::failResult('角色信息更新失败');
        }
        return self::successResult('角色信息更新成功');

    }

    /**
     * 角色删除
     * @param int $roleId
     * @param bool $isSoftDelete
     * @return array
     */
    public function roleDelete(int $roleId, bool $isSoftDelete = true)
    {
        if ($roleId === self::SUPER_AUTH_ID){
            return self::failResult('当前角色为超级管理员,不能被删除');
        }
        $where = [
            ['id','=',$roleId]
        ];
        $detail = Roles::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到用户信息');
        }

        $res = !$isSoftDelete ? Roles::destroy($roleId,true) : Roles::destroy($roleId);
        if (!$res){
            return self::failResult('删除失败,请稍后重试');
        }
        return self::successResult('删除成功');
    }

    /**
     * 用户启用/禁用
     * @param int $roleId
     * @return array
     */
    public function isUse(int $roleId) : array
    {
        if ($roleId === self::SUPER_AUTH_ID){
            return self::failResult('当前角色为超级管理员,不能被禁用');
        }
        $where = [
            ['id','=',$roleId]
        ];
        $detail = Roles::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到角色信息');
        }
        $detail->status = ((int)$detail->status === 1) ? 0 : 1;
        $res = $detail->save();
        if (!$res){
            return self::failResult('启用禁用失败,请稍后重试');
        }
        $status_desc = ((int)$detail->status === 1) ? '启用' : '禁用';
        return self::successResult('当前角色'.$status_desc.'成功');
    }

}