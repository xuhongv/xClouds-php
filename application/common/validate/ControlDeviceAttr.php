<?php


namespace app\common\validate;


use think\Validate;

class ControlDeviceAttr extends Validate
{

    protected $ruleFor = [
        'deviceKey' => 'require|max:60',
        'mac' => 'require|max:20',
        'type' => 'require|max:2',
    ];

}