<?php
namespace app\index\controller;

use app\index\controller\Base;
use app\common\model\File;

class Index extends Base
{
    public function index()
    {

        return self::mdView('index/index');
    }

    public function doc($uid=0, $sign = null)
    {
        return self::mdView('index/doc');
    }

    public function create()
    {
        return self::mdView('index/newBook');
    }

    public function newDoc()
    {
        return self::mdView('index/newDoc');
    }
}
