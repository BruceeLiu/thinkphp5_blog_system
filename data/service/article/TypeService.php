<?php


namespace data\service\article;


use data\model\ArticleTypes;
use data\util\ApiResult;
use think\exception\DbException;
use think\Paginator;

class TypeService
{

    use ApiResult;

    /**
     * 标签列表
     * @param string $keywords
     * @param int $per_page
     * @return ArticleTypes[]|Paginator
     * @throws DbException
     */
    public function typeLists(string $keywords = '', int $per_page=10){
        $where = [];
        if (!empty($keywords)){
            $where[] = [
                'name','like','%'.$keywords.'%'
            ];
        }
        return ArticleTypes::where($where)->paginate($per_page,false,config('paginate.paginate'));
    }

    /**
     * 标签详情
     * @param int $typeId
     * @return ArticleTypes
     */
    public function typeDetail(int $typeId)
    {
        $where = [
            ['id','=',$typeId]
        ];
        return ArticleTypes::where($where)->find();
    }

    /**
     * 标签新增
     * @param array $data
     * @return array
     */
    public function typeAdd(array $data)
    {
        $filed = [
            'name','desc','pid','create_time','update_time'
        ];
        $data['status'] = 1;
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = ArticleTypes::create($data,$filed);
        $id = $res->id ?? 0;
        if ($id < 1){
            return self::failResult('分类信息写入失败');
        }
        return self::successResult('分类信息写入成功');
    }

    /**
     * 标签修改
     * @param array $data
     * @param int $typeId
     * @return array
     */
    public function typeUpdate(array $data,int $typeId)
    {

        $filed = [
            'name','desc','pid','update_time'
        ];
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = ArticleTypes::update($data,[['id','=',$typeId]],$filed)->getNumRows();
        if ($res < 1){
            return self::failResult('分类信息更新失败');
        }
        return self::successResult('分类信息更新成功');

    }

    /**
     * 标签删除
     * @param int $typeId
     * @return array
     */
    public function typeDelete(int $typeId)
    {
        $where = [
            ['id','=',$typeId]
        ];
        $detail = ArticleTypes::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到分类信息');
        }

        $res = ArticleTypes::destroy($typeId);
        if (!$res){
            return self::failResult('删除失败,请稍后重试');
        }
        return self::successResult('删除成功');
    }

    /**
     * 标签启用/禁用
     * @param int $typeId
     * @return array
     */
    public function isUse(int $typeId) : array
    {

        $where = [
            ['id','=',$typeId]
        ];
        $detail = ArticleTypes::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到分类信息');
        }
        $detail->status = ((int)$detail->status === 1) ? 0 : 1;
        $res = $detail->save();
        if (!$res){
            return self::failResult('启用禁用失败,请稍后重试');
        }
        $status_desc = ((int)$detail->status === 1) ? '启用' : '禁用';

        return self::successResult('当前分类'.$status_desc.'成功');
    }

}