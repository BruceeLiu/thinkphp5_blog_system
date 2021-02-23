<?php


namespace data\model;


use think\model\relation\HasMany;

class SystemGroup extends Base
{

    /**
     * @return HasMany
     */
    public function configInfos() : HasMany
    {
        return $this->hasMany(SystemConfig::class,'group_id','id');
    }

}