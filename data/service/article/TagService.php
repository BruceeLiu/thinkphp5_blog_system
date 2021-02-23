<?php


namespace data\service\article;


use data\model\ArticleTags;
use data\util\ApiResult;
use think\exception\DbException;
use think\Paginator;

class TagService
{

    use ApiResult;

    /**
     * 标签列表
     * @param string $keywords
     * @param int $per_page
     * @return ArticleTags[]|Paginator
     * @throws DbException
     */
    public function tagLists(string $keywords = '', int $per_page=10){
        $where = [];
        if (!empty($keywords)){
            $where[] = [
                'name','like','%'.$keywords.'%'
            ];
        }
        return ArticleTags::where($where)->paginate($per_page,false,config('paginate.paginate'));
    }

    /**
     * 标签详情
     * @param int $tagId
     * @return ArticleTags
     */
    public function tagDetail(int $tagId)
    {
        $where = [
            ['id','=',$tagId]
        ];
        return ArticleTags::where($where)->find();
    }

    /**
     * 标签新增
     * @param array $data
     * @return array
     */
    public function tagAdd(array $data)
    {
        $filed = [
            'name','desc','create_time','update_time'
        ];
        $data['status'] = 1;
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = ArticleTags::create($data,$filed);
        $id = $res->id ?? 0;
        if ($id < 1){
            return self::failResult('标签信息写入失败');
        }
        return self::successResult('标签信息写入成功');
    }

    /**
     * 标签修改
     * @param array $data
     * @param int $tagId
     * @return array
     */
    public function tagUpdate(array $data,int $tagId)
    {

        $filed = [
            'name','desc','update_time'
        ];
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = ArticleTags::update($data,[['id','=',$tagId]],$filed)->getNumRows();
        if ($res < 1){
            return self::failResult('标签信息更新失败');
        }
        return self::successResult('标签信息更新成功');

    }

    /**
     * 标签删除
     * @param int $tagId
     * @return array
     */
    public function tagDelete(int $tagId)
    {
        $where = [
            ['id','=',$tagId]
        ];
        $detail = ArticleTags::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到标签信息');
        }

        $res = ArticleTags::destroy($tagId);
        if (!$res){
            return self::failResult('删除失败,请稍后重试');
        }
        return self::successResult('删除成功');
    }

    /**
     * 标签启用/禁用
     * @param int $tagId
     * @return array
     */
    public function isUse(int $tagId) : array
    {

        $where = [
            ['id','=',$tagId]
        ];
        $detail = ArticleTags::where($where)->find();
        if ($detail === null){
            return self::failResult('未找到角色信息');
        }
        $detail->status = ((int)$detail->status === 1) ? 0 : 1;
        $res = $detail->save();
        if (!$res){
            return self::failResult('启用禁用失败,请稍后重试');
        }
        $status_desc = ((int)$detail->status === 1) ? '启用' : '禁用';
        return self::successResult('当前标签'.$status_desc.'成功');
    }

}