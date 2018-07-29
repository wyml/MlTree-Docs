<?php
namespace app\commmon\model;

use think\Model;
use app\common\model\File;
use app\common\model\Option;

class Docs extends Model
{
    protected $pk = 'did';

    public function setCreateIpAttr($val)
    {
        return \request()->ip();
    }

    public function setUpdateIpAttr($val)
    {
        return \request()->ip();
    }

    public function getStatusTextAttr($val, $data)
    {
        $status = [0 => '关闭', 1 => '正常'];
        return $status[$data['status']];
    }

    public function getTypeTextAttr($val, $data)
    {
        $type = [0 => '私有', 1 => '公有'];
        return $type[$data['type']];
    }

    static function createDoc($docName, $sign, $data = null, $type = 1)
    {
        if (!isLogin()) {
            return [false,'权限不足'];
        }
        if (Option::getValue('initDoc') == 1) {
            if (empty($data)) {
                $data = '#### ' . $docName . PHP_EOL . '*****';
            }
        }

        if (File::createFile($sign, $data) > 0) {
            $fileIni = File::getFileInfor($sign);
            $data = [
                'uid' => session('uid'),
                'sign' => $fileIni[1]['md5'],
                'type' => $type
            ];
            $res = self::create($data);
            return [true,$res->did];
        }
    }

    static function updateDoc($sign,$data)
    {
        
    }
}
