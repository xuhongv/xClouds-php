<?php
/**
 * Created by PhpStorm.
 * User: XuHongYss
 * Date: 2018/12/19
 * Time: 16:42
 */

namespace app\common\validate;


use think\Validate;

class DeviceUpdata extends Validate
{
    protected $rule = [
        "type" => 'require|max:20',
        "fw" => 'require|max:10',
        "mac" => 'require|max:20',
    ];


}