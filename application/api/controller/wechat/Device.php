<?php


namespace app\api\controller\wechat;


use app\common\encrypt\Aes;
use think\Validate;

class Device extends BaseWeChatController
{


    /**
     * 微信用户绑定设备或者管理人员创建设备
     * @return \think\response\Json
     */
    public function bindDevice($postData = [])
    {

        //第一步 校验数据是否合法
        $rule = new Validate(['mac' => 'require', 'type' => 'require', 'token' => 'require']);
        //判断是否匹配规则
        if (!$rule->check($postData)) {
            //提示错误信息
            return $this->utlisResponse($rule->getError(), false);
        }


        //第二步：获取各个数据数值
        $getMac = $postData['mac'];
        $getType = $postData['type'];
        $getToken = $postData['token'];

        //return $this->utlisResponse('token校验！'.config('encryptKey.AES_WECHAT_UNIONID_TOKEN'), false);

        //第三步：判断token是否有效
        //start 解密，判断是否是aes加密后数据
        $token = Aes::opensslDecrypt($getToken, config('encryptKey.AES_WECHAT_UNIONID_TOKEN'));
        $token = json_decode($token);
        //return $this->utlisResponse('token校验！'.json_encode($token), false);

        if ($token) {

            //判断有效期 是否处于有效期内？否则需要重新获取
            if (time() > $token->deadline) {
                return $this->utlisResponse('token失效！', false);
            }

            if (!isset($token->name)) {
                return $this->utlisResponse('非微信用户！', false);
            }

            //判断用户已经注册？
            try {
                $user = model('User')->get(['openId' => $token->name]);
            } catch (\Exception $exception) {
                return $this->utlisResponse('' . $exception->getMessage(), false);
            }

            if (!$user) {
                return $this->utlisResponse('该用户不存在！', false);
            }
        } else {
            return $this->utlisResponse('非法用户！', false);
        }

        //第四步：判断设备是否已经注册以及该产品是否注册
        try {
            $Device = model('Device')->get(['mac' => $getMac]);
        } catch (\Exception $exception) {
            return $this->utlisResponse('' . $exception->getMessage(), false);
        }


        //设备未注册在数据库！进行下面操作！
        if (!$Device) {
            //当前用户身份？获取身份等级，大于0是管理员身份，存数据库操作
            if ($user->rank) {
                switch ($user->rank) {
                    //普通用户，不允许注册设备
//                    case 1:
//                        return $this->utlisResponse('非法设备或设备不存在，请购买我方设备！', false);
//                        break;
                    //内部测试人员，仅仅可以绑定5个设备
                    case 1:
                    case 2:
                        //获取当前已经绑定的设备数量
                        try {
                            //根据userID从数据库查询此用户已经绑定的设备列表个数
                            $number = model('RelDeviceUser')->countWhere(['user_id' => $user->id]);
                        } catch (\Exception $exception) {
                            return $this->utlisResponse('' . $exception->getMessage(), false);
                        }

                        if ($number > 5) {
                            return $this->utlisResponse('内部测试人员最多绑定5个设备！请主动删除！', false);
                        }
                        break;
                    //管理员
                    case 3:
                        //超级管理员
                    case 4:
                        break;
                }

                $deviceType = config('devicesAttr.' . $getType)['ch'];
                if (!$deviceType) {
                    return $this->utlisResponse('未知的设备类型！', false);
                }

                //通过身份校验，注册设备
                $registerDevice = [
                    'name' => config('devicesAttr.' . $getType)['ch'] . $getMac . substr(0, 4),
                    'mac' => $getMac,
                    'online' => 1,
                    'type' => $getType,
                    'admin_id' => $user->id, //此设备目前的管理员身份id
                    'isBind' => 1,
                    'create_time' => time(),
                ];


                try {
                    model('Device')->save($registerDevice);
                } catch (\Exception $exception) {
                    return $this->utlisResponse('设备注册失败，请联系管理员！' . $exception->getMessage(), false);
                }


                try {
                    javascript:;
                    $newDevice = model('Device')->get(['mac' => $getMac]);
                } catch (\Exception $exception) {
                    return $this->utlisResponse('' . $exception->getMessage(), false);
                }


                //开始绑定关系存数据库
                $relDeviceUser = [
                    'alias' => config('devicesAttr.' . $getType)['ch'] . $getMac . substr(0, 4),
                    'type' => $getType,
                    'uuid' => $getMac,
                    'online' => 1,
                    'device_id' => $newDevice->id,
                    'user_id' => $user->id,
                    'img' => 'https://mqtt.myshown.com/icon/light.png',
                ];
                try {
                    $RelDeviceOther = model('RelDeviceUser')->save($relDeviceUser);
                } catch (\Exception $exception) {
                    return $this->utlisResponse('设备注册失败，请联系管理员！' . $exception->getMessage(), false);
                }

                if ($RelDeviceOther) {
                    return $this->utlisResponse('您是该设备管理员！绑定设备成功！', true);
                }


            } else {
                return $this->utlisResponse('非法设备，请联系测试管理员！', false);
            }
        }


        //已经注册！判断是否已经被他人绑定了？
        if ($Device->isBind) //已经被绑定了
        {
            //已经绑定过了！
            if ($Device->admin_id == $user->id) {
                return $this->utlisResponse('配网并绑定成功！', true);
            } else {
                //判断该管理员用户已经注册？
                try {
                    $userAdmin = model('User')->get(['id' => $Device->admin_id]);
                } catch (\Exception $exception) {
                    return $this->utlisResponse('操作失败！联系管理员！' . $exception->getMessage(), false);
                }
            }

            if ($userAdmin)
                return $this->utlisResponse('此设备已被【' . userName_cut($userAdmin['name']) . '】绑定！', false);
            else
                return $this->utlisResponse('此设备管理员已注销无法绑定，请联系客户！', false);

        } else //未绑定 , 修改状态，并且把绑定的管理员id修改
        {
            //更新数据库的字段，最后登录时间以及ip地址
            $updata = [
                'isBind' => 1,
                'admin_id' => $user->id,
            ];

            //修改设备注册信息
            try {
                model('Device')->save($updata, ['id' => $Device->id]);
            } catch (\Exception $exception) {
                return $this->utlisResponse('未知错误！请联系管理员！' . $exception->getMessage(), false);
            }

            //开始绑定关系存数据库
            $relDeviceUser = [
                'alias' => config('devicesAttr.' . $getType)['ch'] . $getMac . substr(0, 4),
                'type' => $getType,
                'uuid' => $getMac,
                'device_id' => $Device->id,
                'user_id' => $user->id,
                'img' => '',
            ];


            try {
                $RelDeviceOther = model('RelDeviceUser')->save($relDeviceUser);
            } catch (\Exception $exception) {
                return $this->utlisResponse('设备绑定失败，请联系管理员！' . $exception->getMessage(), false);
            }

            if ($RelDeviceOther)
                return $this->utlisResponse('您是该设备管理员！配网并绑定成功！', true);
            else
                return $this->utlisResponse('绑定失败！未知原因！', true);
        }
        return $this->utlisResponse('未知错误！请联系管理员！错误码：' . $Device->admin_type, false);
    }


    public function getUserBindDevices($postData = [])
    {

        //第一步 校验数据是否合法
        $rule = new Validate(['token' => 'require']);
        //判断是否匹配规则
        if (!$rule->check($postData)) {
            //提示错误信息
            return $this->utlisResponse($rule->getError(), false);
        }
        //第二步：获取各个数据数值
        $getToken = $postData['token'];

        //第三步：判断token是否有效
        //start 解密，判断是否是aes加密后数据
        $token = Aes::opensslDecrypt($getToken, config('encryptKey.AES_WECHAT_UNIONID_TOKEN'));
        $token = json_decode($token);

        if ($token) {

            //判断有效期 是否处于有效期内？否则需要重新获取
            if (time() > $token->deadline) {
                return $this->utlisResponse('token失效！', false);
            }

            if (!isset($token->name)) {
                return $this->utlisResponse('非微信用户！', false);
            }

            //判断用户已经注册？
            try {
                $user = model('User')->get(['openId' => $token->name]);
            } catch (\Exception $exception) {
                return $this->utlisResponse('' . $exception->getMessage(), false);
            }

            if (!$user) {
                return $this->utlisResponse('该用户不存在！', false);
            }
        } else {
            return $this->utlisResponse('非法用户！', false);
        }

        //第四步：判断设备是否已经注册以及该产品是否注册
        try {
            $listDevice = model('RelDeviceUser')->getThisUserDevicesList(['user_id' => $user->id]);
        } catch (\Exception $exception) {
            return $this->utlisResponse('' . $exception->getMessage(), false);
        }

        return collection($listDevice)->toArray(); //对象转为数组
    }

}