<?php


namespace data\model;




use think\model\relation\HasOne;

class SystemLog extends Base
{


    /**
     * 用户信息
     * @return HasOne
     */
    public function userInfo() : HasOne
    {
        return $this->hasOne(AdminUsers::class,'id','uid');
    }

}