<?php


namespace data\service\article;


use data\model\ArticleComment;
use data\util\ApiResult;

class CommentService
{

    use ApiResult;

    public function commentLists(string $keywords='',int $per_page=20)
    {
        $where = [];
        if (!empty($keywords)){
            $where[] = [
                ['nickname|email|content','like','%'.$keywords.'%']
            ];
        }
        return ArticleComment::where($where)->with('articleInfo')->paginate($per_page,false,config('paginate.paginate'));
    }

    public function add(array $data)
    {
        $field = [
            'nickname','email','content','article_manage_id','create_time','update_time'
        ];
        $res = ArticleComment::create($data,$field);
        if (!$res){
            return self::failResult('评论失败,请及时联系管理员');
        }
        return self::successResult('评论成功!');

    }

    public function delete(int $commentId)
    {
        $info = ArticleComment::where('id','=',$commentId)->find();
        if ($info === null){
            return self::failResult('未找到相关评论信息');
        }
        $res =  ArticleComment::destroy($commentId);
        if (!$res){
            return self::failResult('评论删除失败');
        }
        return self::successResult('评论删除成功');
    }

}