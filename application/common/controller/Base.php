<?php
namespace app\common\controller;

use think\Controller;
use app\common\model\Option;

class Base extends Controller
{
    static function mdView($tplName)
    {
        $tpl = Option::getValue('tpl');
        return view($tpl.$tplName);
    }
}
