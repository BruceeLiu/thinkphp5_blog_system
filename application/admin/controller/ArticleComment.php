<?php

namespace app\admin\controller;

use data\service\article\CommentService;
use data\util\ApiResult;
use think\Request;
use think\response\Json;

class ArticleComment extends Base
{

    /**
     * @var CommentService
     */
    protected $service;


    public function initialize()
    {
        parent::initialize();
        $this->service = new CommentService();
    }

    /**
     * 评论列表
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $keywords = $request->get('keywords','');
        $lists = $this->service->commentLists($keywords);
        $page = $lists->render();
        $this->assign('comments',$lists);
        $this->assign('page',$page);
        return $this->fetch();
    }

    /**
     * 删除评论
     * @param Request $request
     * @return Json
     */
    public function del(Request $request) : Json
    {
        $commentId= $request->get('comment_id', 0);
        if ($commentId < 1) {
            return json(ApiResult::failResult('请选择要删除的标签'));
        }
        $res = $this->service->delete($commentId);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

}
