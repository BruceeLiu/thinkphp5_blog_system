<?php


namespace data\model;

use think\model\concern\SoftDelete;

class Roles extends Base
{
    use SoftDelete;

    protected $autoWriteTimestamp = 'datetime';

    protected $deleteTime = 'delete_time';

    protected $append = ['status_text'];

    protected $status_text = [0=>'禁用',1=>'启用'];

    /**
     * 获取状态码
     * @param $data
     * @param $value
     * @return string
     */
    protected function getStatusTextAttr($data,$value)
    {
        return $this->status_text[$data['status'] ?? 0] ?? '未知状态';
    }

}