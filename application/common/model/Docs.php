<?php
namespace app\commmon\model;

use think\Model;
use app\common\model\File;
use app\common\model\Option;
use app\common\model\Dochistroy;

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
            return [false, '权限不足'];
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
            return [true, $res->did];
        }
    }

    static function updateDoc($did, $data)
    {
        if (!isLogin()) {
            return [false, '权限不足'];
        }
        $doc = self::get($did);
        if (empty($dic)) {
            return [false, '文档不存在'];
        }

        if (Option::getValue('autoHistory') == 1) {
            $res = File::writeDoc($doc->sign, $data, 0);
            $update_hid = Dochistory::where('did', $did)->max('hid');
            $hisIn = [
                'did' => $did,
                'update_hid' => $update_hid,
                'update_uid' => session('uid'),
                'sign' => $res[1],
            ];
            Dochistory::update($hisIn);
        } else {
            $res = File::writeDoc($doc->sign, $data, 1);
        }
        return [true, '更新成功'];
    }

    static function readDoc($did = null, $sign = null, $type = 0)
    {
        if (empty($did) && empty($sign)) {
            return [false, '标识不能为空'];
        }
        if (!empty($did)) {
            $res = self::get($did);
            $data = File::readDoc($res->sign, $type);
            return [true, $data];
        } else {
            $data = File::readDoc($sign, $type);
            return [true, $data];
        }
    }

    static function delDoc($sign)
    {
        $doc = self::where('sign',$sign)->find();
        if (empty($doc)) {
            return [false,'文档不存在'];
        }
        if (Option::getValue('delHistory') != 1) {
            File::delFile($sign,1);
            self::delete($doc->did);
            return [true,'删除完成'];
        }
        $res = File::delFile($sign);
        return $res;
    }
}
