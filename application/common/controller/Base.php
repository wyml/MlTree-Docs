<?php
namespace app\common\controller;

use think\Controller;
use app\common\model\Option;

class Base extends Controller
{
    protected function initialize()
    {
        $siteOption = Option::getValues();
        $this->assign('siteOption',$siteOption);
    }
    static function mdView($tplName)
    {
        $tpl = Option::getValue('Theme');
        return view($tpl.$tplName);
    }
}
