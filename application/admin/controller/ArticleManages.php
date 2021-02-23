<?php

namespace app\admin\controller;

use app\admin\validate\ArticleManage;
use data\service\article\ArticleService;
use data\util\ApiResult;
use think\Request;

class ArticleManages extends Base
{

    use ApiResult;

    /**
     * @var ArticleService
     */
    protected $service;

    /**
     * @var ArticleManage;
     */
    protected $validate;

    public function initialize()
    {
        parent::initialize();
        $this->service = new ArticleService();
        $this->validate = new ArticleManage();
    }

    /**
     * 文章管理列表页
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $keywords = $request->get('keywords','');
        $per_page = $request->get('per_page',20);
        $type = $request->get('type',0);
        $start_datetime = $request->get('start_datetime','');
        $end_datetime = $request->get('end_datetime','');
        $where = [];
        if (!empty($keywords)){
            $where = [
                ['title|sub_title|keywords','like','%'.$keywords.'%']
            ];
        }
        if ((int)$type >= 1){
            $where[] = [
                ['article_type','=',(int)$type]
            ];
        }
        if (!empty($start_datetime) && !empty($end_datetime)){
            $where[] = [
                ['create_time','between',[$start_datetime.' 00:00:00',$end_datetime.' 23:59:59']]
            ];
        }
        $lists = $this->service->articleLists($where,$per_page);
        $article_tree = $this->service->articleTypeTree();
        $page = $lists->render();
        $this->assign('articles',$lists);
        $this->assign('article_trees',$article_tree);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function detail(Request $request)
    {
        $data = $request->get();
        $article_editor = $data['article_editor'] ?? 0;
        if ((int)$data['article_id'] < 1) {
            return json(ApiResult::failResult('请选择要查看的用户'));
        }
        $detail = $this->service->articleDetail((int)$data['article_id']);
        $detail['article_tag'] = explode(',',$detail['article_tag']);
        $tags_lists = $this->service->enableTags();
        $article_tree = $this->service->articleTypeTree();
        $this->assign('tags',$tags_lists);
        $this->assign('article_trees',$article_tree);
        $this->assign('article_editor',(int)$article_editor);
        $this->assign('detail',$detail);
        return $this->fetch('article_manages/detail/detail');
    }

    public function add(Request $request)
    {
        $tags_lists = $this->service->enableTags();
        $article_tree = $this->service->articleTypeTree();
        $this->assign('tags',$tags_lists);
        $this->assign('article_trees',$article_tree);
        return $this->fetch('article_manages/detail/add');
    }

    public function addArticle(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');
        $data['content'] = $data['editorValue'] ?? '';
        $data['is_allow_comment'] = $data['is_allow_comment'] ?? 2;
        if (isset($data['comment_start_time'],$data['comment_end_time']) && strtotime($data['comment_end_time']) < strtotime($data['comment_start_time'])){
            return json(self::failResult('评论结束时间不能小于起始时间'));
        }
        $check = $this->validate->scene('create')->check($data);
        if (!$check){
            return json(self::failResult((string)$this->validate->getError()));
        }
        $data['article_tag'] = implode(',',$data['article_tag']);
        $data['create_user'] = $this->users_info['id'];
        $data['update_user'] = $this->users_info['id'];
        $res = $this->service->articleAdd($data);
        if ($res['code'] === 0){
            return json(self::failResult($res['message']));
        }
        return json(self::successResult($res['message']));
    }


    public function update(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');
        $data['content'] = $data['editorValue'] ?? '';
        $data['is_allow_comment'] = $data['is_allow_comment'] ?? 2;
        if (isset($data['comment_start_time'],$data['comment_end_time']) && strtotime($data['comment_end_time']) < strtotime($data['comment_start_time'])){
            return json(self::failResult('评论结束时间不能小于起始时间'));
        }
        $check = $this->validate->scene('update')->check($data);
        if (!$check){
            return json(self::failResult((string)$this->validate->getError()));
        }
        $data['article_tag'] = implode(',',$data['article_tag']);
        $data['update_user'] = $this->users_info['id'];
        $res = $this->service->articleUpdate($data,$data['id']);
        if ($res['code'] === 0){
            return json(self::failResult($res['message']));
        }
        return json(self::successResult($res['message']));
    }


    public function del(Request $request)
    {
        $article_id = $request->get('article_id',0);
        if ($article_id < 1){
            return json(self::failResult('请选择要删除的文章'));
        }
        $res = $this->service->articleDelete($article_id);
        if ($res['code'] === 0){
            return json(self::failResult($res['message']));
        }
        return json(self::successResult($res['message']));
    }


    public function audit(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');
        $data['update_user'] = $this->users_info['id'];
        $res = $this->service->articleAudit((int)$data['article_id'],(int)$data['audit_status'],$data['update_user']);
        if ($res['code'] === 0){
            return json(self::failResult($res['message']));
        }
        return json(self::successResult($res['message']));
    }
}
