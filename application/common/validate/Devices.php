<?php
/**
 * Created by PhpStorm.
 * User: XuHongYss
 * Date: 2018/12/18
 * Time: 21:24
 */

namespace app\common\validate;


use think\Validate;

class Devices extends Validate
{
    protected $rule = [
        'deviceKey' => 'require|max:60',
        'mac' => 'require|max:20',
        'type' => 'require|max:2',
    ];
}