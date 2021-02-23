<?php

namespace app\admin\controller;

use data\service\article\TypeService;
use data\util\ApiResult;
use think\Request;

class ArticleTypes extends Base
{

    use ApiResult;

    /**
     * @var TypeService
     */
    protected $service;

    public function initialize()
    {
        parent::initialize();
        $this->service = new TypeService();
    }

    public function index(Request $request)
    {
        $keywords = $request->get('keywords', '');
        $list = $this->service->typeLists($keywords);
        $page = $list->render();
        $this->assign('types', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function detail(Request $request)
    {
        $type_id= $request->get('type_id', 0);
        if ($type_id < 1) {
            return json(ApiResult::failResult('请选择要查看的类型'));
        }
        $detail = $this->service->typeDetail($type_id);
        $this->assign('detail', $detail);
        return $this->fetch('article_types/detail/detail');
    }

    public function add(Request $request)
    {
        $pid = $request->get('pid',0);
        $this->assign('pid',$pid);
        return $this->fetch('article_types/detail/add');
    }

    public function addTypes(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');

        $rule = [
            'name|权限名'=>'require|unique:article_tags',
            'desc|权限描述' => 'max:255'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $data['pid'] = $data['pid'] ?? 0;
        if ($data['pid'] >= 1){
            $info = \data\model\ArticleTypes::where('id','=',$data['pid'])->field(['id','name'])->find();
            if ($info === null){
                return json(ApiResult::failResult('当前权限不存在,请重新选择'));
            }
        }
        $data['status'] = 1;

        $res = $this->service->typeAdd($data);
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

        $data['pid'] = $data['pid'] ?? 0;
        if ($data['pid'] >= 1){
            $info = \data\model\ArticleTypes::where('id','=',$data['pid'])->field(['id','name'])->find();
            if ($info === null){
                return json(ApiResult::failResult('当前权限不存在,请重新选择'));
            }
        }
        $data['status'] = 1;

        $res = $this->service->typeUpdate($data,$data['id']);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function del(Request $request)
    {
        $typeId = $request->get('type_id', 0);
        if ($typeId < 1) {
            return json(ApiResult::failResult('请选择要删除的分类'));
        }

        $detail = \data\model\ArticleTypes::where('pid',$typeId)->select();
        if ($detail !== null){
            return json(ApiResult::failResult('请先删除下级分类'));
        }


        $res = $this->service->typeDelete($typeId);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function isUse(Request $request)
    {
        $tagId = $request->get('type_id', 0);

        if ($tagId < 1) {
            return json(ApiResult::failResult('请选择要【启用/禁用】的分类'));
        }
        $res = $this->service->isUse($tagId);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

}
