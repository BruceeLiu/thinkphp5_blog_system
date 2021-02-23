<?php

namespace app\front\controller;


use data\model\NetworkLines;

class NetworkTimeLine extends Base
{

    public function index()
    {
        $lists = NetworkLines::order('time_point','desc')->field(['id','name','desc','time_point'])->select();
        $this->assign('lists',$lists);
        return $this->fetch();
    }
}
