<?php

namespace app\front\controller;


use data\model\ArticleManages;
use data\model\ArticleTags;
use data\model\ArticleTypes;
use data\util\ApiResult;
use think\Request;

class Article extends Base
{

    use ApiResult;

    public function initialize()
    {
        parent::initialize();
    }

    public function index(Request $request)
    {
        $articleTypeInfo = $this->articleTypeInfo();
        $article_type = $request->article_type ?? $articleTypeInfo['first_type']['id'];
        $article_id = $request->id ?? 0;
        //异步查询剩余文章条件
        if ((int)$article_id >= 1){
            $where[] = [
                ['id','<',(int)$article_id],
                ['article_type','=',(int)$article_type],
                ['status','=',2]
            ];
            $article_info = ArticleManages::where($where)
                ->withCount('articleTimes')
                ->withCount('articleComment')
                ->order('sort_no','desc')
                ->order('create_time','desc')
                ->limit(10)->select();

            $article_all_info = [];
            foreach ($article_info as $k=>$v){
                $article_all_info[] = [
                    'id'=>$v['id'],
                    'title' => $v['title'],
                    'author_name' => '一条有理想的咸鱼',
                    'publish_time' => calDays(strtotime($v['create_time']),time()),
                    'content_summary' => $v['content_summary'],
                    'comment_count' => $v['article_comment_count'] ?: 0,
                    'click_count' => $v['article_times_count'] ?: 0,
                    'article_cover_url' => ''
                ];
            }
            $last_article_id = 0;
            if (count($article_all_info) >= 1){
                $last_article_id = $article_all_info[count($article_all_info) - 1]['id'];
            }
            return json(self::successResult('获取成功!',[
                'id' => $article_id,
                'article_all_info'=>$article_all_info,
                'last_article_id' => $last_article_id,
                'first_article_type' => $article_type
            ]));
        }

        //获取已上架的文件
        $where = [
            ['status','=',2],
            ['article_type','=',(int)$article_type]
        ];
        $article_info = ArticleManages::where($where)->withCount('articleTimes')
            ->withCount('articleComment')
            ->order('sort_no','desc')
            ->order('create_time','desc')
            ->limit(10)
            ->select();
        $article_all_info = [];
        foreach ($article_info as $k=>$v){
            $article_all_info[] = [
                'id'=>$v['id'],
                'title' => $v['title'],
                'author_name' => '一条有理想的咸鱼',
                'publish_time' => calDays(strtotime($v['create_time']),time()),
                'content_summary' => $v['content_summary'],
                'comment_count' => $v['article_comment_count'] ?: 0,
                'click_count' => $v['article_times_count'] ?: 0,
                'article_cover_url' => ''
            ];
        }
        $last_article_id = 0;
        if (count($article_all_info) >= 1){
            $last_article_id = $article_all_info[count($article_all_info) - 1]['id'];
        }

        $this->assign('article_all_info',$article_all_info);
        $this->assign('last_article_id',$last_article_id);
        $this->assign('first_article_type',$article_type);
        $this->assign('first_article_type_name',$articleTypeInfo['first_type']['name']);
        $this->assign('all_article_type',$articleTypeInfo['all_types']);
        $this->assign('recommended_lists',$this->recommendedLists());
        $this->assign('article_tags_infos',$this->articleTagsInfo());
        return $this->fetch();
    }

    public function accordArticleType(Request $request){
        if (!isset($request->article_type) || empty($request->article_type)){
            return json(self::failResult('请选择要查看的分类'));
        }

        $articleTypeInfo = $this->articleTypeInfo((int)$request->article_type);
        $allArticleTypeInfo = $this->articleTypeInfo();

        $article_type = $request->article_type;

        $where[] = [
            ['article_type','=',(int)$article_type],
            ['status','=',2]
        ];
        $article_info = ArticleManages::where($where)
            ->withCount('articleTimes')
            ->withCount('articleComment')
            ->order('sort_no','desc')
            ->order('create_time','desc')
            ->limit(10)->select();

        $article_all_info = [];
        foreach ($article_info as $k=>$v){
            $article_all_info[] = [
                'id'=>$v['id'],
                'title' => $v['title'],
                'author_name' => '一条有理想的咸鱼',
                'publish_time' => calDays(strtotime($v['create_time']),time()),
                'content_summary' => $v['content_summary'],
                'comment_count' => $v['article_comment_count'] ?: 0,
                'click_count' => $v['article_times_count'] ?: 0,
                'article_cover_url' => ''
            ];
        }
        $last_article_id = 0;
        if (count($article_all_info) >= 1){
            $last_article_id = $article_all_info[count($article_all_info) - 1]['id'];
        }
        return json(self::successResult('获取成功!',[
            'article_all_info'=>$article_all_info,
            'last_article_id' => $last_article_id,
            'first_article_type' => (int)$article_type,
            'all_article_type' => $allArticleTypeInfo['all_types'],
            'first_article_type_name'=>$articleTypeInfo['first_type']['name']
        ]));
    }

    /**
     * 全部分类
     * @param int $id
     * @return array
     */
    public function articleTypeInfo(int $id = 0)
    {
        $where = [];
        $where[] = ['status','=',1];
        if ($id >= 1){
            $where[] = ['id','=',$id];
        }
        $infos = ArticleTypes::where($where)
            ->order('create_time','desc')
            ->select();
        return ['first_type' => $infos[0],'all_types' => $infos];
    }

    /**
     * 文章标签
     * @return ArticleTags[]
     */
    public function articleTagsInfo()
    {
        $article_info = ArticleManages::where([
            ['status','=',2]
        ])->withCount('articleTimes')->withCount('articleComment')
            ->order('sort_no','desc')
            ->order('create_time','desc')
            ->select();
        $article_tags_id = '';
        foreach ($article_info as $k=>$v){
            if (($k + 1) === count($article_info)){
                $article_tags_id .= $v['article_tag'];
            }else{
                $article_tags_id .= $v['article_tag'] .',';
            }
        }
        $article_tags_id_arr = array_unique(explode(',',$article_tags_id));
        return ArticleTags::where([
            ['id','in',$article_tags_id_arr],
            ['status','=',1]
        ])->field(['id','name'])->select();
    }


    /**
     * 文章热点推荐排行榜
     * @return array
     */
    public function recommendedLists() : array
    {
        $article_info = ArticleManages::where([
            ['status','=',2]
        ])->withCount('articleTimes')
            ->withCount('articleComment')
            ->order('sort_no','desc')
            ->order('create_time','desc')
            ->select();
        $article_all_info = [];
        foreach ($article_info as $k=>$v){
            $article_all_info[] = [
                'id'=>$v['id'],
                'title' => $v['title'],
                'comment_count' => $v['article_comment_count'] ?: 0,
            ];
        }
        $recommended_lists = [];
        if (count($article_all_info) >= 1){
            $recommended_lists = sortLists($article_all_info,'comment_count');
        }
        return $recommended_lists;
    }

}
