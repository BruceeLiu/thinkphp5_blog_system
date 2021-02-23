<?php


namespace data\service;


use data\model\Links;
use data\util\ApiResult;
use think\Paginator;

class LinkService
{

    use ApiResult;

    /**
     * 友情链接列表
     * @param string $keywords
     * @param int $per_page
     * @return Links[]|Paginator
     */
    public function linksLists(string $keywords = '', int $per_page=10){
        $where = [];
        if (!empty($keywords)){
            $where[] = [
                'name','like','%'.$keywords.'%'
            ];
        }
        return Links::where($where)->paginate($per_page,false,config('paginate.paginate'));
    }

    /**
     * 单条友情链接详情
     * @param int $linksId
     * @return Links
     */
    public function linksDetail(int $linksId)
    {
        $where = [
            ['id','=',$linksId]
        ];
        return Links::where($where)->find();
    }

    /**
     * 友情链接新增
     * @param array $data
     * @return array
     */
    public function addLink(array $data)
    {
        $filed = [
            'name','url','create_time','update_time'
        ];
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = Links::create($data,$filed);
        $id = $res->id ?? 0;
        if ($id < 1){
            return self::failResult('友情链接信息写入失败');
        }
        return self::successResult('友情链接信息写入成功');
    }

    /**
     * 友情链接修改
     * @param array $data
     * @param int $linksId
     * @return array
     */
    public function linksUpdate(array $data,int $linksId)
    {

        $filed = [
            'name','url','update_time'
        ];
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = Links::update($data,[['id','=',$linksId]],$filed)->getNumRows();
        if ($res < 1){
            return self::failResult('友情链接信息更新失败');
        }
        return self::successResult('友情链接信息更新成功');

    }

    /**
     * 友情链接删除
     * @param int $linksId
     * @return array
     */
    public function linkDelete(int $linksId)
    {
        $where = [
            ['id','=',$linksId]
        ];
        $detail = Links::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到友情链接信息');
        }

        $res = Links::destroy($linksId);
        if (!$res){
            return self::failResult('删除失败,请稍后重试');
        }
        return self::successResult('删除成功');
    }

}