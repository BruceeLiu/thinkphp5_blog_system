<?php


namespace app\admin\controller;


use data\service\NetworkLineService;
use data\util\ApiResult;
use think\Request;

class NetworkLine extends Base
{
    use ApiResult;

    /**
     * @var NetworkLineService
     */
    protected $service;

    public function initialize()
    {
        parent::initialize();
        $this->service = new NetworkLineService();
    }


    public function index(Request $request)
    {
        $keywords = $request->get('keywords', '');
        $list = $this->service->networkLineLists($keywords);
        $page = $list->render();
        $this->assign('lines', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }
    
    public function detail(Request $request)
    {
        $line_id= $request->get('line_id', 0);
        if ($line_id < 1) {
            return json(ApiResult::failResult('请选择要查看友情链接'));
        }
        $detail = $this->service->networkLineDetail($line_id);
        $detail['time_point'] = date('Y-m-d',strtotime($detail['time_point']));
        $this->assign('detail', $detail);
        return $this->fetch('network_line/detail/detail');
    }

    public function add()
    {
        return $this->fetch('network_line/detail/add');
    }

    public function addLine(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');

        $rule = [
            'name|网站节点名称'=>'require|unique:network_lines|max:255',
            'desc|描述' => 'require|max:255',
            'time_point|时间点' => 'require|date'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $res = $this->service->networkLineAdd($data);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function update(Request $request)
    {
        $data = $request->except(['create_time','update_time'],'post');

        $rule = [
            'name|网站节点名称'=>'require|max:255',
            'desc|描述' => 'require|max:255',
            'time_point|时间点' => 'require|date'
        ];
        if (!$this->app->validate->check($data,$rule)){
            return json(ApiResult::failResult((string)$this->app->validate->getError()));
        }
        $res = $this->service->networkLineUpdate($data,$data['id']);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

    public function del(Request $request)
    {
        $line_id = $request->post('line_id', 0);
        if ($line_id < 1) {
            return json(ApiResult::failResult('请选择要删除的标签'));
        }
        $res = $this->service->networkLineDelete($line_id);
        if ($res['code'] === 0){
            return json(ApiResult::failResult($res['message']));
        }
        return json(ApiResult::successResult($res['message']));
    }

}