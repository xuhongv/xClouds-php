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

        $json = config('devicesAttr.rgbLight')['mine']['miot']['properties_query'];


        return json_encode($json);
    }


}