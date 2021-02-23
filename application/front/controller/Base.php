<?php


namespace app\front\controller;


use data\model\SystemConfig;
use think\Controller;

class Base extends Controller
{

    public function initialize()
    {
        $this->assign('network_base_info', $this->getNetworkBaseInfo());
        $this->assign('menu_info', $this->getMenuInfo());
        $this->assign('uper_info', $this->getUperInfo());
    }

    /**
     * @return string[]
     */
    protected function getNetworkBaseInfo()
    {
        //网站基本信息
        $networkBaseInfo = SystemConfig::where('group_id', '=', 1)->where('status', '=', 1)
            ->field(['key', 'value'])
            ->select();
        $network_logo = '';
        $network_title = '';
        $network_beian_url = '';
        $network_beian_num = '';
        $network_copyright = '';
        $network_index_url = '';
        foreach ($networkBaseInfo as $k => $v) {
            if ($v['key'] === '网站logo' && !empty($v['value'])) {
                $network_logo = $v['value'];
            }

            if ($v['key'] === '网站名称' && !empty($v['value'])) {
                $network_title = $v['value'];
            }

            if ($v['key'] === '网站备案链接' && !empty($v['value'])) {
                $network_beian_url = $v['value'];
            }

            if ($v['key'] === '网站备案号' && !empty($v['value'])) {
                $network_beian_num = $v['value'];
            }

            if ($v['key'] === '版权信息' && !empty($v['value'])) {
                $network_copyright = $v['value'];
            }

            if ($v['key'] === '网站首页链接' && !empty($v['value'])) {
                $network_index_url = $v['value'];
            }
        }

        return [
            'network_logo' => $network_logo,
            'network_title' => $network_title,
            'network_beian_url' => $network_beian_url,
            'network_beian_num' => $network_beian_num,
            'network_copyright' => $network_copyright,
            'network_index_url' => $network_index_url
        ];
    }

    /**
     * 栏目信息
     * @return array
     */
    protected function getMenuInfo(): array
    {
        //栏目信息
        $menuInfo = SystemConfig::where('group_id', '=', 2)
            ->where('status', '=', 1)
            ->field(['key', 'value'])
            ->select();
        $menu = [];
        foreach ($menuInfo as $k => $v) {
            if (!empty($v['value'])) {
                $menu[] = $v;
            }
        }
        return $menu;
    }

    /**
     * 博主信息
     * @return string[]
     */
    protected function getUperInfo()
    {
        //博主信息
        $uperInfo = SystemConfig::where('group_id', '=', 3)
            ->where('status', '=', 1)
            ->field(['key', 'value'])
            ->select();
        $uper_name = '';
        $uper_work = '';
        $uper_company = '';
        $uper_email = '';
        $uper_sex = '';
        $uper_location = '';
        $uper_hobby = '';
        $uper_qq = '';
        $uper_wei_xin = '';
        $uper_wei_xin_common = '';
        $uper_desc = '';
        $uper_ding_wei = '';
        foreach ($uperInfo as $k => $v) {
            if ($v['key'] === '博主姓名' && !empty($v['value'])) {
                $uper_name = $v['value'];
            }
            if ($v['key'] === '职业' && !empty($v['value'])) {
                $uper_work = $v['value'];
            }
            if ($v['key'] === '所任职的公司' && !empty($v['value'])) {
                $uper_company = $v['value'];
            }
            if ($v['key'] === '邮箱' && !empty($v['value'])) {
                $uper_email = $v['value'];
            }
            if ($v['key'] === '性别' && !empty($v['value'])) {
                $uper_sex = $v['value'];
            }
            if ($v['key'] === '定位' && !empty($v['value'])) {
                $uper_location = $v['value'];
            }
            if ($v['key'] === '爱好' && !empty($v['value'])) {
                $uper_hobby = $v['value'];
            }
            if ($v['key'] === 'qq' && !empty($v['value'])) {
                $uper_qq = $v['value'];
            }
            if ($v['key'] === '微信号' && !empty($v['value'])) {
                $uper_wei_xin = $v['value'];
            }
            if ($v['key'] === '微信公众号' && !empty($v['value'])) {
                $uper_wei_xin_common = $v['value'];
            }
            if ($v['key'] === '个人描述' && !empty($v['value'])) {
                $uper_desc = $v['value'];
            }
            if ($v['key'] === '网站定位' && !empty($v['value'])) {
                $uper_ding_wei = $v['value'];
            }
        }

        return [
            'uper_name' => $uper_name,
            'uper_work' => $uper_work,
            'uper_company' => $uper_company,
            'uper_email' => $uper_email,
            'uper_sex' => $uper_sex,
            'uper_location' => $uper_location,
            'uper_hobby' => $uper_hobby,
            'uper_qq' => $uper_qq,
            'uper_wei_xin' => $uper_wei_xin,
            'uper_wei_xin_common' => $uper_wei_xin_common,
            'uper_desc' => $uper_desc,
            'uper_ding_wei' => $uper_ding_wei
        ];
    }


}