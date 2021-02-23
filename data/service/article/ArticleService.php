<?php

namespace data\service\article;


use data\model\ArticleManages;
use data\model\ArticleTags;
use data\model\ArticleTypes;
use data\util\ApiResult;

class ArticleService
{
    use ApiResult;

    public function articleLists(array $defaultWhere, int $per_page=20)
    {
        $where = [];
        if (!empty($defaultWhere)){
            $where = $defaultWhere;
        }
        return ArticleManages::where($where)->withCount('articleTimes')->with('articleTypeInfo')->paginate($per_page,false,config('paginate.paginate'));
    }

    public function articleDetail(int $articleId)
    {
        return ArticleManages::where('id','=',$articleId)->find();
    }

    public function articleAdd(array $data)
    {
        $fields = [
            'title','sub_title','article_type','article_tag','sort_no','keywords','content','content_summary','create_user',
            'update_user','is_allow_comment','comment_start_time','comment_end_time'
        ];
        $data['create_time'] = date('Y-m-d H:i:s',time());
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = ArticleManages::create($data,$fields);
        if (!$res){
            return self::failResult('文章创建失败');
        }
        return self::successResult('文章创建成功');

    }

    public function articleUpdate(array $data,int $articleId)
    {
        $info = ArticleManages::where('id','=',$articleId)->find();
        if ($info === null){
            return self::failResult('未找到相关文章信息');
        }

        if ($info['status'] === 2){
            return self::failResult('当前文章已上架,不能被修改');
        }

        $fields = [
            'title','sub_title','article_type','article_tag','sort_no','keywords','content','content_summary',
            'update_user','is_allow_comment','comment_start_time','comment_end_time'
        ];
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $res = ArticleManages::update($data,[['id','=',$articleId]],$fields);
        if (!$res){
            return self::failResult('文章创建失败');
        }
        return self::successResult('文章创建成功');
    }

    public function articleDelete(int $articleId, bool $isSoftDelete=true)
    {
        $info = ArticleManages::where('id','=',$articleId)->find();
        if ($info === null){
            return self::failResult('未找到相关文章信息');
        }
        $res = $isSoftDelete ? ArticleManages::destroy($articleId) : ArticleManages::destroy($articleId,true);
        if (!$res){
            return self::failResult('文章删除失败');
        }
        return self::successResult('文章删除成功');
    }

    public function articleAudit(int $articleId,int $auditStatus,int $auditUserId)
    {
        $info = ArticleManages::where('id','=',$articleId)->find();
        if ($info === null){
            return self::failResult('未找到相关文章信息');
        }
        $info['status'] = $auditStatus;
        $info['update_time'] = date('Y-m-d H:i:s',time());
        $info['update_user'] = $auditUserId;
        $res = $info->save();
        if (!$res){
            return self::failResult('文章审核失败');
        }
        return self::successResult('文章审核成功');
    }


    public function enableTags()
    {
        return ArticleTags::where('status','=',1)->field(['id','name'])->select();
    }

    public function articleTypeTree()
    {
        $article_id_arr = ArticleTypes::where('status','=',1)->where('status','=',1)->column('id');
        return $this->articleTree($article_id_arr);
    }

    protected function articleTree(array $id_arr, string $pk = 'id', string $pid = 'pid', string $child = 'child',int $root = 0)
    {
        $article_info = ArticleTypes::where('status','=',1)
            ->whereIn('id',$id_arr)
            ->where('status','=',1)
            ->select();
        $article_tree = [];
        foreach ($article_info as $k=>$v){
            if ($v[$pid] === $root){
                unset($article_info[$k]);
                if (!empty($article_info)){
                    $children = $this->articleTree($id_arr,$pk,$pid,$child,$v[$pk]);
                    $v[$child] = $children;
                }
                $article_tree[] = $v;
            }
        }
        return $article_tree;
    }

}