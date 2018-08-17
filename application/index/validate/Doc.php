<?php
namespace app\index\validate;

use think\Validate;

class Doc extends Validate
{
    protected $rule = [
        'content' => 'require',
        'name' => 'require',
        'sign' => 'require',
        'type' => 'require|in:0,1',
    ];

}