<?php


namespace data\service;


use data\model\NetworkLines;
use data\util\ApiResult;
use think\Paginator;

class NetworkLineService
{

    use ApiResult;

    /**
     * 时间线列表
     * @param string $keywords
     * @param int $per_page
     * @return NetworkLines[]|Paginator
     */
    public function networkLineLists(string $keywords = '', int $per_page=10){
        $where = [];
        if (!empty($keywords)){
            $where[] = [
                'name','like','%'.$keywords.'%'
            ];
        }
        return NetworkLines::where($where)->order('time_point','desc')->paginate($per_page,false,config('paginate.paginate'));
    }

    /**
     * 单条时间线详情
     * @param int $linesId
     * @return NetworkLines
     */
    public function networkLineDetail(int $linesId)
    {
        $where = [
            ['id','=',$linesId]
        ];
        return NetworkLines::where($where)->find();
    }

    /**
     * 时间线新增
     * @param array $data
     * @return array
     */
    public function networkLineAdd(array $data)
    {
        $filed = [
            'name','desc','time_point','create_time','update_time'
        ];
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = NetworkLines::create($data,$filed);
        $id = $res->id ?? 0;
        if ($id < 1){
            return self::failResult('时间线信息写入失败');
        }
        return self::successResult('时间线信息写入成功');
    }

    /**
     * 时间线修改
     * @param array $data
     * @param int $linesId
     * @return array
     */
    public function networkLineUpdate(array $data,int $linesId)
    {

        $filed = [
            'name','desc','time_point','update_time'
        ];
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = NetworkLines::update($data,[['id','=',$linesId]],$filed)->getNumRows();
        if ($res < 1){
            return self::failResult('时间线信息更新失败');
        }
        return self::successResult('时间线信息更新成功');

    }

    /**
     * 时间线删除
     * @param int $linesId
     * @return array
     */
    public function networkLineDelete(int $linesId)
    {
        $where = [
            ['id','=',$linesId]
        ];
        $detail = NetworkLines::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到时间线信息');
        }

        $res = NetworkLines::destroy($linesId);
        if (!$res){
            return self::failResult('删除失败,请稍后重试');
        }
        return self::successResult('删除成功');
    }

}