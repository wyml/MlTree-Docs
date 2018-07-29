<?php
namespace app\index\controller;

use app\index\controller\Base;
use app\common\model\File;

class Index extends Base
{
    public function index()
    {
        //dump(File::createFile('mltd','# Hellow Word!'));
        dump(File::readDoc('mltd'));
        return view('defaule/index/index');
    }
}
