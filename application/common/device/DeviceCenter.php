<?php


namespace app\common\device;


class DeviceCenter
{

    /**
     * @param string $deviceType 设备类型
     * @param string 服务器类型 0表示天猫精灵；1表示小爱同学
     * @return array 获取该设备的所有属性（可控制以及仅查询）
     */
    public static function getTheDeviceAllSupportProperties($deviceType, $cloudsType)
    {
        return config('devicesAttr.' . $deviceType)[$cloudsType]['properties_control_action'];
    }

    /**
     *  本身设备类型转为指定音响平台的设备类型
     * @param $type 设备类型
     * @param $cloudsType 音响平台
     * @return mixed
     */
    public static function getDeviceTypeToCloudsType($type, $cloudsType)
    {
        return config('devicesAttr.' . $type)[$cloudsType]['type'];
    }

    /**
     *  根据设备类型返回云平台可控制的action数组
     * @param $deviceType 设备类型
     * @param $cloudsType 音响平台
     * @return mixed
     */
    public static function getThisDeviceAllControlActions($deviceType, $cloudsType)
    {
        return config('devicesAttr.' . $deviceType)[$cloudsType]['properties_control_action'];
    }

    /**
     * 返回设备订阅的主题
     * @param $type 设备类型
     * @param $mac  设备mac
     * @return mixed 返回设备订阅的主题
     */
    public static function getDeviceSubMqttTopic($type, $mac)
    {
        return '/' . $type . '/' . $mac . '/devSub';
    }

}