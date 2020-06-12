<?php


namespace app\api\controller\test;


use app\api\controller\oauth\BaseController;
use app\common\device\DeviceCenter;
use think\Controller;
use think\Validate;

class Test extends BaseController
{


    public function index()


    {


        $deviceAttr = config('devicesAttr.esp32s2');
        var_dump(array_key_exists('htmlControl', $deviceAttr));


        //var_dump(DeviceCenter::isSupportThisClouds("esp32s2", config('devicesAttr.AligenieName')));
    }


}