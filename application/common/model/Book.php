<?php
namespace app\commmon\model;

use think\Model;
use app\common\model\File;
use app\common\model\Option;
use app\common\model\Docs;

class Book extends Model
{
    protected $pk = 'bid';

    static function createBook($bookName)
    {
        if (!isLogin()) {
            return [false, '权限不足'];
        }
        $data = [
            'uid'=>session('uid'),
        ];
        self::create($data);
        return [true,'创建成功'];
    }

    

    static function delDoc($sign)
    {
        $doc = self::where('sign', $sign)->find();
        if (empty($doc)) {
            return [false, '文档不存在'];
        }
        if (Option::getValue('delHistory') != 1) {
            File::delFile($sign, 1);
            self::delete($doc->did);
            return [true, '删除完成'];
        }
        $res = File::delFile($sign);
        return $res;
    }
}
