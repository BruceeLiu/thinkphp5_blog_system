<?php


namespace app\admin\controller;


use data\service\LinkService;
use data\util\ApiResult;
use think\Request;

class Link extends Base
{

    use ApiResult;

    /**
     * @var LinkService
     */
    protected $service;

    public function initialize()
    {
        parent::initialize();
        $this->service = new LinkService();
    }


    public function index(Request $request)
    {
        $keywords = $request->get('keywords', '');
        $list = $this->service->linksLists($keywords);
        $page = $list->render();
        $this->assign('links', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function detail(Request $request)
    {
        $link_id= $request->get('link_id', 0);
        if ($link_id < 1) {
            return json(ApiResult::failResult('请选择要查看友情链接'));
        }
        $detail = $this->service->linksDetail($link_id);
        $this->assign('detail', $detail);
        return $this->fetch('link/detail/detail');
    }

    public function add()
    {
        return $this->fetch('link/detail/add');
    }

    public function addLink(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');

        $rule = [
            'name|友情链接名称'=>'require|unique:links|max:255',
            'url|友情链接地址' => 'require|max:255'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $res = $this->service->addLink($data);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function update(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');

        $rule = [
            'name|友情链接名称'=>'require',
            'url|友情链接地址' => 'max:255'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $res = $this->service->linksUpdate($data,$data['id']);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function del(Request $request)
    {
        $link_id = $request->post('link_id', 0);
        if ($link_id < 1) {
            return json(ApiResult::failResult('请选择要删除的标签'));
        }
        $res = $this->service->linkDelete($link_id);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

}