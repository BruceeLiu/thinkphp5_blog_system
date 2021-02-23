<?php

namespace app\admin\controller;

use data\service\article\TagService;
use data\util\ApiResult;
use think\Request;

class ArticleTags extends Base
{

    use ApiResult;

    /**
     * @var TagService
     */
    protected $service;

    public function initialize()
    {
        parent::initialize();
        $this->service = new TagService();
    }

    public function index(Request $request)
    {
        $keywords = $request->get('keywords', '');
        $list = $this->service->tagLists($keywords);
        $page = $list->render();
        $this->assign('tags', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function detail(Request $request)
    {
        $tag_id= $request->get('tag_id', 0);
        if ($tag_id < 1) {
            return json(ApiResult::failResult('请选择要标签的角色'));
        }
        $detail = $this->service->tagDetail($tag_id);
        $this->assign('detail', $detail);
        return $this->fetch('article_tags/detail/detail');
    }

    public function add(Request $request)
    {

        return $this->fetch('article_tags/detail/add');
    }

    public function addTags(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');

        $rule = [
            'name|权限名'=>'require|unique:article_tags',
            'desc|权限描述' => 'max:255'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $res = $this->service->tagAdd($data);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function update(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');

        $rule = [
            'name|权限名'=>'require',
            'desc|权限描述' => 'max:255'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $res = $this->service->tagUpdate($data,$data['id']);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function del(Request $request)
    {
        $tagId = $request->get('tag_id', 0);
        if ($tagId < 1) {
            return json(ApiResult::failResult('请选择要删除的标签'));
        }
        $res = $this->service->tagDelete($tagId);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function isUse(Request $request)
    {
        $tagId = $request->get('tag_id', 0);
        if ($tagId < 1) {
            return json(ApiResult::failResult('请选择要【启用/禁用】的标签'));
        }
        $res = $this->service->isUse($tagId);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

}
