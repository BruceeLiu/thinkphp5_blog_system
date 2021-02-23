<?php

namespace app\admin\controller;

use think\Request;

class SystemLog extends Base
{

    public function index(Request $request)
    {
        $where = [];
        $keywords = $request->get('keywords','');
        $start_time = $request->get('start_time','');
        $end_time = $request->get('end_time','');

        if (!empty($keywords)){
            $where[] = [
                'url','like','%'.$keywords.'%'
            ];
        }

        if (isset($start_time,$end_time) && !empty($start_time) && !empty($end_time)){
            $start_time .= ' 00:00:00';
            $end_time .= ' 23:59:59';
            $where[] = [
                'create_time','between',[$start_time,$end_time]
            ];
        }

        $lists = \data\model\SystemLog::where($where)->order('create_time','desc')->paginate(20,false,config('paginate.paginate'));
        $page = $lists->render();
        $this->assign('page',$page);
        $this->assign('logs',$lists);
        return $this->fetch();
    }

}
