<?php
namespace app\commmon\model;

use think\Model;

class Dochistory extends Model
{
    protected $pk = 'hid';

    public function getFileUrlAttr($val,$data)
    {
        $sign = $data['sign'];
        
    }
}
