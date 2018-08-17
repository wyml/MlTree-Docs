<?php
namespace app\index\controller;

use app\index\controller\Base;
use app\common\model\File;
use app\commom\model\Docs;
use app\common\model\Option;

class Index extends Base
{
    public function index()
    {
        $this->assign('site',Option::getValues('base'));
        return self::mdView('index/index');
    }

    public function doc($uid = 0, $sign = null)
    {
        return self::mdView('index/doc');
    }

    public function create()
    {
        return self::mdView('index/newBook');
    }

    public function newDoc()
    {
        if (request()->isPost()) {
            $res = $this->validate(input('post.'), 'app\index\validate\Doc');
            if ($res !== true) {
                return \json(['code' => -1, 'message' => $res, 'time' => time()]);
            }
            if (File::fileExists(input('post.sign'))) {
                $res = Docs::updateDoc(input('post.sign'), input('post.content'));
                if ($res[0]) {
                    return json(['code' => 0, 'message' => $res[1], 'time' => time()]);
                } else {
                    return json(['code' => -1, 'message' => $res[1], 'time' => time()]);
                }
            }else{
                $res = Docs::createDoc();
                
            }

        }
        return self::mdView('index/newDoc');
    }
}
