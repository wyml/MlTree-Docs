<?php
namespace app\commmon\model;

use think\Model;

class Dochistory extends Model
{
    protected $pk = 'uid';

    static function isLogin($userKey='')
    {
        if (empty($userKey)) {
            $userKey = \cookie('userKey');
        }
        if ($userKey === session('userKey') && !empty(session('uid')) && !empty(session('userKey'))) {
            return true;
        }
        return false;
    }
}
