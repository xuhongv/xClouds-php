<?php
/**
 * Created by PhpStorm.
 * User: XuHongYss
 * Date: 2018/12/26
 * Time: 18:52
 */

namespace app\api\controller\oauth;


use app\common\device\DeviceCenter;
use think\Request;
use think\Validate;

class MiotGateWay extends BaseController
{


    private $server;
    private $cloudsType;
    private $cloudsMyType;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub

        $this->cloudsType = config('devicesAttr.MiotName');
        $this->cloudsMyType = config('devicesAttr.MineName');

        $dsn = 'mysql:dbname=' . config('database')['database'] . ';host=' . config('database')['hostname'];;
        $username = config('database')['username'];
        $password = config('database')['password'];

        \OAuth2\Autoloader::register();

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $storage = new \OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->server = new \OAuth2\Server($storage);

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
    }

    /**
     * @param string $userId
     * @param $body
     * @return mixed
     */
    private function subscribeDevices($userId = '', $body)
    {
//        {
//     "requestId": "xxxx",
//    "intent": "subscribe",
//    "devices": [
//        {
//            "did": "AAAA",
//        "subscriptionId": "abcdefg",
//        "status": 0
//      },
//      {
//          "did": "AAAB",
//        "subscriptionId": "abc123",
//        "status": -1, // 第三方校验时发现此设备不存在，DID是错的。
//        "description": "invalid device id"
//      }

        //先暂时不对 subscriptionId 做处理
        for ($i = 0; $i < count($body->devices); $i++) {
            $body->devices[$i]->status = 0;
        }

        return $body;
    }

    /**
     *  获取该用户下的绑定的设备列表
     * @param string $userId
     * @param string $messageId
     * @return array|string
     */
    private function getDevicesList($userId = '', $messageId = '')
    {
        //判断该用户是否存在
        try {
            $user = model('User')->get(['id' => $userId]);
        } catch (\Exception $exception) {
            return utilsResponse(-1, '用户查询报错:' . $exception->getMessage(), [], 400);
        }
        $devices = [
        ];

        //检测到存在
        if ($user) {
            //根据userID从数据库查询此用户已经绑定的设备列表列表
            $device = model('RelDeviceUser')->getThisUserDevicesList(['user_id' => $userId]);
            //根据userID从数据库查询此用户已经绑定的设备列表个数
            $devicesNums = model('RelDeviceUser')->countWhere(['user_id' => $userId]);
            //utilsSaveLogs('the user\'s nums:' . $devicesNums, 3);
            //此账户下面暂无设备
            if (0 == $devicesNums) {
                utilsSaveLogs('the MiIot user\'s nums == 0', 3);
                return '';
            }

            for ($i = 0; $i < $devicesNums; $i++) {
                $devices[$i] = [
                    'did' => $userId . '-' . (string)($device[$i]['device_id']),
                    'type' => 'urn:miot-spec-v2:device:light:0000A001:180-light:1:0000C801', //urn:miot-spec-v2:device:light:0000A001:180-light:1:0000C801
                    'name' => $device[$i]['alias'],
                ];
            }
        }
        //utilsSaveLogs(json_encode($returnData), 3);
        return [
            'requestId' => $messageId,
            'intent' => 'get-devices',
            'devices' => $devices,
        ];
    }

    /**
     * 控制单个设备
     * @param $deviceMsg
     * @return array
     */
    private function controlThisDevice($deviceMsg)
    {
        //默认设置设备不存在
        $status = [
            'did' => $deviceMsg->did,
            'siid' => $deviceMsg->siid,
            'piid' => $deviceMsg->piid,
            'status' => -1, //为 -1 表示失败控制
            'description' => 'the device is offline',
        ];
        //检查设备是否存在
        try {
            $device = model('Device')->get(['id' => (int)(explode('-', $deviceMsg->did)[1])]);
        } catch (\Exception $e) {
            //utilsSaveLogs('get device ID ' . $obj->did . ' database get fail ' . $e->getMessage(), 1);
            //设备不存在
            goto end_;
        }
        $controlValue = 0;
        //根据id从数据库查询此设备
        if ($device['online'] == 1) {
            //通过小爱发过来的siid和piid的数值判断是否有此属性的记录
            $deviceSupportAttr = config('devicesAttr.' . $device['type'])[$this->cloudsMyType][$this->cloudsType]['properties_query'];
            //查看此属性是否被支持
            if (array_key_exists('siid' . $deviceMsg->siid, $deviceSupportAttr) && array_key_exists('piid' . $deviceMsg->piid, $deviceSupportAttr['siid' . $deviceMsg->siid])) {
                // 小爱要查询的属性
                $queryAttr = $deviceSupportAttr['siid' . $deviceMsg->siid]['piid' . $deviceMsg->piid];
                // 小爱要控制的数值
                $controlValue = $deviceMsg->value;
                // 查看此属性下面的控制范围
                $rulers = config('devicesAttr.' . $device->type)[$this->cloudsMyType][$this->cloudsType]['properties_control_value_range'][$queryAttr];
                //对比下 , 判断是否匹配规则
                $enable = false;
                for ($index = 0; $index < count($rulers); $index++) {
                    //判断
                    $validate = new Validate(['value' => $rulers[$index]]);
                    //判断是否匹配规则
                    if ($validate->check(['value' => $controlValue])) {
                        $enable = true;
                        break;
                    }
                }
                if ($enable) {
                    $status['status'] = 0; //为 0 表示成功控制
                    unset($status['description']);
                } else {
                    $status['description'] = 'the ' . $queryAttr . '\'s value not in range'; //控制失败，属性数值不在范围内
                    goto end_;
                }
            }
        }
        $actionName = "";
        switch ($queryAttr) {
            //适配私有协议控制，电源控制时候的动作额外设置
            case  "powerstate":
                $actionName = $controlValue ? "TurnOn" : "TurnOff";
                break;
            //色温 对应的value 转化为 2700~6500
            case 'colorTemperature':
                $actionName = config('devicesAttr.' . $device->type)[$this->cloudsMine][$this->cloudsType]['properties_control']['siid' . $deviceMsg->siid]['piid' . $deviceMsg->piid];
                $controlValue = (int)((($controlValue - 1000) / 9000) * (6500 - 2700) + 2700);
                break;
            default:
                $actionName = config('devicesAttr.' . $device->type)[$this->cloudsMine][$this->cloudsType]['properties_control']['siid' . $deviceMsg->siid]['piid' . $deviceMsg->piid];
                break;
        }
        $this->reposeToDevice($this->cloudsType, $device, $actionName, $queryAttr, $deviceMsg->did, $controlValue, $payLoadVersion = 1);
        end_:
        return $status;

    }

    /**
     *  处理小爱发过来的设备控制请求
     * @param string $userId
     * @param $getAligenieObj
     * @return array
     */
    private function controlDevice($userId = '', $getAligenieObj)
    {

        $devicesArry = ($getAligenieObj->properties);
        if (!is_array($devicesArry))
            return [];

        $result = [
            'requestId' => ($getAligenieObj->requestId),
            'intent' => ($getAligenieObj->intent),
            'properties' => [],
        ];

        for ($i = 0; $i < count($devicesArry); $i++) {
            array_push($result['properties'], $this->controlThisDevice($devicesArry[$i]));
        }

        //应答给小米
        return $result;
    }


    /**
     * 读取某个设备的某个属性数值
     * @param $obj
     * @return array
     *
     */
    private function readOneDevice($obj)
    {
        $status = [];
        //默认设置设备不存在
        $status['did'] = $obj->did;
        $status['siid'] = $obj->siid;
        $status['piid'] = $obj->piid;
        $status['status'] = -1;
        $status['description'] = 'the device is not exit';

        //根据id从数据库查询此设备
        try {
            $device = model('Device')->get(['id' => (int)(explode('-', $obj->did)[1])]);
        } catch (\Exception $e) {
            //utilsSaveLogs('get device ID ' . $obj->did . ' database get fail ' . $e->getMessage(), 1);
            //设备不存在
            goto end_;
        }
        //存在设备
        if ($device) {
            //判断是否在线
            if ($device['online'] == 1) {
                //通过小爱发过来的siid和piid的数值判断是否有此属性的记录
                $deviceSupportAttr = config('devicesAttr.' . $device['type'])[$this->cloudsMyType][$this->cloudsType]['properties_query'];
                //查看此属性是否被支持
                if (array_key_exists('siid' . $obj->siid, $deviceSupportAttr)
                    && array_key_exists('piid' . $obj->piid, $deviceSupportAttr['siid' . $obj->siid])) {
                    $queryAttr = $deviceSupportAttr['siid' . $obj->siid]['piid' . $obj->piid];
                    //判断是否为基本属性获取
                    if ($obj->siid == 1) {
                        $status['status'] = 0;
                        unset($status['description']);
                        switch ($obj->piid) {
                            ////manufacturer
                            case 1:
                                $status['value'] = 'AiClouds';
                                break;
                            //model
                            case 2:
                                $status['value'] = 'AiClouds Dev';
                                break;
                            ///serial-number
                            case 3:
                                $status['value'] = $device['mac'];
                                break;
                            //firmware-revision
                            case 4:
                                $status['value'] = 'V2.3';
                                break;
                            default:
                                break;
                        }
                    } else {
                        $statusArry = json_decode($device['attrbute']);

                        $supportValue = config('devicesAttr.' . $device['type'])[$this->cloudsType]['properties_control_value_range'];
                        //var_dump($supportValue);
                        for ($i = 0; $i < count($statusArry); $i++) {
                            if ($statusArry[$i]->name == $queryAttr) {
                                //这里要转换为米家识别的属性
                                $status['status'] = 0;
                                switch ($queryAttr) {
                                    case 'powerstate':
                                        $status['value'] = $supportValue[$queryAttr][$statusArry[$i]->value];
                                        break;
                                }
                                unset($status['description']);
                            }
                        }
                    }
                }
            } else {
                //检查此设备激活状态
                if (!is_null($device['mac'])) {
                    //离线信息返回(一条信息)
                    $status['status'] = -18;
                    $status['description'] = 'device Offline';
                }
            }
        }
        end_:
        {
            return $status;
        }
    }

    /**
     * @param string $userId
     * @param $getAligenieObj
     * @return array
     */
    private function queryDevices($userId = '', $getAligenieObj)
    {
        $devicesArry = ($getAligenieObj->properties);

        if (!is_array($devicesArry))
            return [];

        $result = [
            'requestId' => ($getAligenieObj->requestId),
            'intent' => ($getAligenieObj->intent),
            'properties' => [],
        ];

        //查询逐个设备里面的最新状态
        for ($i = 0; $i < count($devicesArry); $i++) {
            //查询这个设备的每个属性的状态
            array_push($result['properties'], $this->readOneDevice($devicesArry[$i]));
        }

        return $result;
    }


    function index()
    {
        //捕获post过来的数据
        $poststr = file_get_contents("php://input");
        $info = Request::instance()->header('User-Token');

        //生成日志
        utilsSaveLogs('***********Miot to me*************', 3);
        utilsSaveLogs($poststr, 3);
        //utilsSaveLogs("token:".$info, 3);

        $_POST['access_token'] = $info;
        $_GET['access_token'] = $info;

        //解析成对象
        $getAligenieObj = json_decode($poststr);

        //是否对象
        if (!is_object($getAligenieObj)) {
            //utilsSaveLogs('fail pass' . gettype($getAligenieObj), 2);
            return utilsResponse(0, 'are you miot?', [], 400);
        }

        $intent = '';
        $requestId = '';

        try {
            //判断在 payload 是否有这个字段
            $intent = $getAligenieObj->intent;
            $requestId = $getAligenieObj->requestId;
        } catch (\Exception $exception) {
            //utilsSaveLogs('fail pass: ' . $exception->getMessage(), 2);
            return utilsResponse(0, 'are you Miot? ' . $exception->getMessage(), [], 400);
        }

        // 校验accessToken是否正确？
        if (!$this->server->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
            utilsSaveLogs('fail pass' . $this->server->getResponse(), 2);
            return utilsResponse(0, 'invalid_token check fail.' . $this->server->getResponse(), [], 400);
        }
        $token = $this->server->getAccessTokenData(\OAuth2\Request::createFromGlobals());

        utilsSaveLogs('success pass get Current userId 当前来请求设备的用户id是 ：' . $token['user_id'], 3);

        $repository = [];
        switch ($intent) {
            //请求刷新设备列表
            case 'get-devices':
                $repository = $this->getDevicesList($token['user_id'], $requestId);
                break;
            //请求控制单个设备
            case 'set-properties':
                $repository = $this->controlDevice($token['user_id'], $getAligenieObj);
                break;
            //查询设备的状态(包括多个)
            case 'get-properties':
                $repository = $this->queryDevices($token['user_id'], $getAligenieObj);
                break;
            //主动订阅设备的状态(包括多个)
            case 'subscribe':
                $repository = $this->subscribeDevices($token['user_id'], $getAligenieObj);
                break;
            default:
                break;
        }
        utilsSaveLogs('success send miot msg ：' . json_encode($repository), 3);
        return json($repository);
    }


}