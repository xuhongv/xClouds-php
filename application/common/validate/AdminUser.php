<?php
/**
 * Created by PhpStorm.
 * User: XuHongYss
 * Date: 2018/11/23
 * Time: 15:12
 */

namespace app\common\validate;


use think\Validate;

class AdminUser extends Validate{

    protected $rule =[
        'username'=>'require|max:20',
        'password'=>'require|max:20',
    ];
}