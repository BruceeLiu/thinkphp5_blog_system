<?php

namespace data\model;

use think\model\concern\SoftDelete;
use think\model\relation\HasOne;

class AdminUsers extends Base
{
    use SoftDelete;

    protected $autoWriteTimestamp = 'datetime';

    protected $deleteTime = 'delete_time';
    //
    protected $append = ['status_text'];

    protected $status_text = [0=>'禁用',1=>'启用'];

    /**
     * 获取状态码
     * @param $data
     * @param $value
     * @return string
     */
    protected function getStatusTextAttr($data,$value) : string
    {
       return $this->status_text[$data['status'] ?? 0] ?? '未知状态';
    }

    /**
     * 角色信息
     * @return HasOne
     */
    public function rolesInfo() : HasOne
    {
        return $this->hasOne(Roles::class,'id','role_id');
    }

    /**
     * 生成access_token
     * @param int $uid - 用户ID
     * @return array
     */
    public static function tokenRefresh(int $uid) : array
    {
        $update_field = [
            'access_token' => md5(uniqid(microtime(true),true)),
            //access_token有效期(单位:秒)
            'access_token_expire_in' => 7200,
            'access_token_create_at' => time()
        ];

        $update = self::where('id','=',$uid)->update($update_field);

        if (!$update){
            return [];
        }

        return $update_field;
    }


}
