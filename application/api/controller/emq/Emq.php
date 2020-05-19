<?php


namespace app\api\controller\Emq;


use think\Controller;

class Emq extends Controller
{

    /**
     *  把设备设置为在线状态
     * @param $clientId
     * @return string
     */
    public function setDeviceOnline($clientId)
    {
        //更新数据库的字段
        $updata = [
            'update_time' => time(),
            'online' => 1, //离线
        ];
        try {
            $Device = model('Device')->save($updata, ['mac' => $clientId]);
        } catch (\Exception $exception) {
            utilsSaveLogs("save online device fail :" . $exception->getMessage());
            return 'fail';
        }
        if (!$Device)
            utilsSaveLogs("save online device  fail !!");

        return "";
    }

    /**
     *  把设备设置为离线状态
     * @param $clientId
     * @return string
     */
    public function setDeviceOffline($clientId)
    {

        //更新数据库的字段
        $updata = [
            'update_time' => time(),
            'online' => 0, //离线
        ];

        try {
            $Device = model('Device')->save($updata, ['mac' => $clientId]);
        } catch (\Exception $exception) {
            utilsSaveLogs("failset office :" . $exception->getMessage());
            return 'fail';
        }
        if (!$Device)
            utilsSaveLogs("save offline device  fail !!");

//        try {
//            $Device = model('Device')->get(['mac' => $clientId]);
//        } catch (\Exception $exception) {
//            return 'fail';
//        }
// var_dump($Device->mac);
//
//        //把action设置为离线
//        $objAttr->header->action = 'Device.Offline';
//        //构造数据发送到手机，该设备离线
//        $data = [
//            "topic" => DeviceCenter::getMqttTopicHeaderType($device['type']) . '/' . $device['deviceKey'] . '/devPub',
//            "qos" => 1,
//            "retain" => false,
//            "client_id" => "xuhongPhpClient",
//            'username' => "xuhongPhpClient",
//            "payload" => "",
//        ];
        // $result = json_decode(post2Emq(config('url_mqtt'), $data));
        //utilsSaveLogs('send Mqtt result :  ' . json_encode($result), 1);
        //utilsSaveLogs('send Mqtt :  ' . json_encode($objAttr), 1);


        //主动推送离线状态到客户端
        return "";
    }


    /**
     *  更新设备的状态并存储在数据库
     * @param $getPostDataString 更新的属性
     * @return string
     * @throws \think\exception\DbException
     */
    private function updataDeviceAttrbute($getPostDataString)
    {
        //json字符串转为数组arry
        $getPostDataArry = json_decode($getPostDataString, true);

        if (isset($getPostDataArry['header']))
            $checkData = $getPostDataArry['header'];
        else
            return '';

        //获取规则
        $validate = validate('DeviceUpdata');

        //判断是否匹配规则
        if (!$validate->check($checkData)) {
            //提示错误信息
            utilsSaveLogs($validate->getError(), 1);
            return '';
        }

        //根据mac从数据库查询此设备
        try {
            $device = model('Device')->get(['mac' =>($getPostDataArry['header']['mac'])]);
        } catch (DbException $e) {
            utilsSaveLogs('$getPostDataArry ID ' . ($getPostDataArry['header']['deviceId']) . 'database check fail ' . $e->getMessage(), 1);
        }
        if (!$device) {
            utilsSaveLogs('database have not  this device' . ($getPostDataArry['header']['mac']), 1);
            return '';
        }
        $var = json_encode($getPostDataArry['attr']);
        //打印存储的状态数据
        //utilsSaveLogs('var:' . $var, 2);
        //更新数据库的字段
        $updata = [
            'update_time' => time(),
            'attrbute' => $var,
            'online' => 1, //在线
        ];

        try {
            model('Device')->save($updata, ['id' => $device->id]);
        } catch (\Exception $exception) {
            utilsSaveLogs('updata fail! ' . $exception->getMessage(), 3);
        }
    }

    public function index()
    {
        if (!$this->request->isPost()) {
            return '';
        }
        //格式化json数据
        $deviceMsg = json_decode(file_get_contents("php://input"), TRUE);

        //打印所有
        //utilsSaveLogs(file_get_contents("php://input"), 4);
        //utilsSaveLogs($deviceMsg['action'], 4);

        if (empty($deviceMsg['action'])) {
            return '';
        }

        //判断收到消息类型
        switch ($deviceMsg['action']) {
            //状态在线改变
            case 'client_connected':
                //连接名判断是否设备上报 admin
                if (isset($deviceMsg['client_id']))
                    $this->setDeviceOnline($deviceMsg['client_id']);
                break;
            //设备推送消息
            case 'message_publish':
                //判断是设备发送过来的？还是api请求过来的？api请求的是empty
                if (empty($deviceMsg['from_client_id']) || empty($deviceMsg['from_username'])) {
                    //utilsSaveLogs('from php request...,return', 4);
                    return '';
                }
                if (strlen($deviceMsg['topic']) < 19)
                    return '';
                //把topic字符串分割为数组
                $arrTopic = explode('/', $deviceMsg['topic']);
                utilsSaveLogs('from php request $arrTopic :'.json_encode($arrTopic), 4);
                //判断 clientId和主题前部分是否一致？
                if ($deviceMsg['from_client_id'] != $arrTopic[2]) {
                    return '';
                }
                //判断主题的后半部分是否 devPub
                if ($arrTopic[3] == 'devPub') {
                    //utilsSaveLogs('断主题的后半部分 是 devPub', 1);
                    //判断 payLoad 是否为json数据
                    if (json_last_error() == 0) {
                        $this->updataDeviceAttrbute($deviceMsg['payload']);
                    } else {
                        utilsSaveLogs('payLoad 不是json数据', 1);
                    }
                } else {
                    utilsSaveLogs('断主题的后半部分 否 devPub', 1);
                }
                break;
            //设备离线
            case 'client_disconnected':
                //连接名判断是否设备上报 admin
                if (isset($deviceMsg['client_id']))
                    $this->setDeviceOffline($deviceMsg['client_id']);
                break;
            default:
                break;
        }
        return '';
    }
}