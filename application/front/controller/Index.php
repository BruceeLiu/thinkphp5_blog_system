<?php

namespace app\front\controller;

use data\model\ArticleManages;
use data\model\ArticleTags;
use data\model\Links;
use data\util\ApiResult;
use data\model\ArticleTimes;
use think\Collection;
use think\Request;
use think\response\Json;

class Index extends Base
{

    use ApiResult;


    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 首页
     * @param Request $request
     * @return mixed|Json
     */
    public function index(Request $request)
    {
        $id = $request->get('id',0);

        //异步查询剩余文章条件
        if ((int)$id >= 1){
            $where[] = [
                ['id','<',(int)$id],
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
                'id' => $id,
                'article_all_info'=>$article_all_info,
                'last_article_id' => $last_article_id
            ]));
        }

        //获取已上架的文件
        $where = [
            ['status','=',2]
        ];
        $article_info = ArticleManages::where($where)->withCount('articleTimes')->withCount('articleComment')->order('sort_no','desc')->order('create_time','desc')->limit(10)->select();
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
        $this->assign('recommended_lists',$this->recommendedLists());
        $this->assign('click_lists',$this->clickLists());
        $this->assign('article_tags_infos',$this->articleTagsInfo());
        $this->assign('friendship_links',$this->linksUrl());
        return $this->fetch();
    }


    public function detail(int $id)
    {
        if ((int)$id < 1){
            return json(self::failResult('请选择要查看的文章'));
        }
        $ip = request()->ip();
        $article_time_info = $this->getLastArticleTimesInfo((int)$id,['ip']);
        //判断当前是否有人点击该文章
        if ($article_time_info['code'] === 0){
            $res =$this->insertArticleTimes($id,$ip,date('Y-m-d H:i:s',time()));
            if ($res['code'] === 0){
                return json(self::failResult($res['message']));
            }
        }
        //判断当前Ip是否相同
        if ($ip !== $article_time_info['data']['ip']){
            $res =$this->insertArticleTimes($id,$ip,date('Y-m-d H:i:s',time()));
            if ($res['code'] === 0){
                return json(self::failResult($res['message']));
            }
        }
        $article_info = ArticleManages::where([
            ['status','=',2],
            ['id','=',$id]
        ])->withCount('articleTimes')->find();
        $article_info['author_name'] = '一条有理想的咸鱼';
        if (isset($article_info['keywords']) && !empty($article_info['keywords'])){
            $article_info['keywords'] = explode(',',$article_info['keywords']);
        }
        $per_article = [];
        $next_article = [];
        //下一篇
        if ($id <= 1){
            $per_article = [
                'id'=> 0,
                'url'=>'#',
                'title'=>'没有了'
            ];
            $next_article_info = ArticleManages::where([
                ['status','=',2],
                ['id','>',$id]
            ])->field(['id','title'])->find();
            $next_article = [
                'id'=>$next_article_info['id'],
                'url' =>'front/index/detail',
                'title'=>$next_article_info['title']
            ];
        }
        //上一篇
        if ($id > 1){
            $per_article_info = ArticleManages::where([
                ['status','=',2],
                ['id','<',$id]
            ])->field(['id','title'])->find();
            $per_article = [
                'id'=>$per_article_info['id'],
                'url' =>'front/index/detail',
                'title'=>$per_article_info['title']
            ];

            $next_article_info = ArticleManages::where([
                ['status','=',2],
                ['id','>',$id]
            ])->field(['id','title'])->find();
            if ($next_article_info ===null){
                $next_article = [
                    'id'=>0,
                    'url' =>'#',
                    'title'=>'没有了'
                ];
            }else{
                $next_article = [
                    'id'=>$next_article_info['id'],
                    'url' =>'front/index/detail',
                    'title'=>$next_article_info['title']
                ];
            }

        }
        $this->assign('detail',$article_info);
        $this->assign('recommended_lists',$this->recommendedLists());
        $this->assign('per_article_info',$per_article);
        $this->assign('next_article_info',$next_article);
        return $this->fetch('index/detail/detail');
    }

    /**
     * 文章热点推荐排行榜
     * @return array
     */
    public function recommendedLists() : array
    {
        $article_info = ArticleManages::where([
            ['status','=',2]
        ])->withCount('articleTimes')->withCount('articleComment')->order('sort_no','desc')->order('create_time','desc')->select();
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

    /**
     * 点击率排行榜
     * @return array
     */
    public function clickLists() : array
    {
        $article_info = ArticleManages::where([
            ['status','=',2]
        ])->withCount('articleTimes')->withCount('articleComment')->order('sort_no','desc')->order('create_time','desc')->select();
        $article_all_info = [];
        foreach ($article_info as $k=>$v){
            $article_all_info[] = [
                'id'=>$v['id'],
                'title' => $v['title'],
                'click_count' => $v['article_times_count'] ?: 0,
            ];
        }
        $click_lists = [];
        if (count($article_all_info) >= 1){
            $click_lists =  sortLists($article_all_info,'click_count');
        }
        return $click_lists;
    }

    /**
     * 文章标签
     * @return ArticleTags[]
     */
    public function articleTagsInfo()
    {
        $article_info = ArticleManages::where([
            ['status','=',2]
        ])->withCount('articleTimes')->withCount('articleComment')->order('sort_no','desc')->order('create_time','desc')->select();
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
     * 友情链接
     * @return array|array[]|\array[][]|Links[]|Collection
     */
    public function linksUrl()
    {
        $lists = Links::order('create_time','desc')->field(['name','url'])->select();
        return (is_array($lists) ? $lists : $lists->toArray());
    }

    /**
     * 获取最新的文章点击率信息
     * @param int $article_id
     * @param array $fields
     * @return array
     */
    protected function getLastArticleTimesInfo(int $article_id,array $fields=['*'])
    {

        $field = [];
        foreach ($fields as $v){
            if ($v !== '*'){
                $field[] = $v;
            }
        }
        $info = ArticleTimes::where('article_manage_id','=',$article_id);
        if (!empty($field)){
            $info = $info->field($field);
        }
        $info = $info->order('id','desc')->find();
        if ($info === null){
            return self::failResult('请选择要查看的文章');
        }
        $info = is_array($info) ? $info : $info->toArray();
        return self::successResult('查询成功',$info);
    }

    /**
     * 写入文章点击记录表
     * @param int $article_manage_id
     * @param string $ip
     * @param string $click_time
     * @return array
     */
    protected function insertArticleTimes(int $article_manage_id,string $ip,string $click_time)
    {
        $fields = ['article_manage_id','ip','click_time'];
        $data =['article_manage_id'=>$article_manage_id,'ip'=>$ip,'click_time' => $click_time];
        $res = ArticleTimes::create($data,$fields);
        if (!$res){
            return self::failResult('写入失败!');
        }
        return self::successResult('写入成功!');
    }

}
