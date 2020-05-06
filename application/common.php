<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 *
 *  统一保存日志数据，异步打印
 * @param string $data 要保存的数据
 * @param int $level 等级： 1-> error 、2 -> warn、 3 ->debug、 4-> info
 */
function utilsSaveLogs($data = '', $level = 4)
{

    $items = debug_backtrace();
    $errfile = $items[0]["file"];
    $errline = $items[0]["line"];

    if ($level == 1) {
        error_log("[Error]" . $errfile . ', Line : ' . $errline);
        error_log("[Error]" . $data);
    } elseif ($level == 2) {
        error_log("[Warn]" . $errfile . ', Line : ' . $errline);
        error_log("[Warn]" . $data);
    } elseif ($level == 3) {
        error_log("[Debug]" . $errfile . ', Line : ' . $errline);
        error_log("[Debug]" . $data);
    } elseif ($level == 4) {
        error_log("[Info]" . $errfile . ', Line : ' . $errline);
        error_log("[Info]" . $data);
    }
}


/**
 *   封装统一格式回调给客户端
 *
 * @param int $statusCode 业务状态码
 * @param string $msg 信息提示
 * @param array $data 数据
 * @param int $httpCode http状态码
 * @return string json数据
 */
function utilsResponse($statusCode = 0, $msg = '', $data = [], $httpCode = 200)
{
    $Arry = [
        'status' => $statusCode,
        'msg' => $msg,
        'data' => $data,
    ];
    return json($Arry, $httpCode);
}


/**
 *  微信公众号内部浏览器的 token； 截止时间: 当前时间戳 + 有效期
 * @param int $time 时间戳
 * @return int|mixed
 */
function getWeChatUnionTokenDeadline($time = 0)
{
    return $time + config('oauth.wechatUnionTokenExpire');
}


/**
 *  微信公众号内部浏览器的 token 是否有效?
 * @param int $time 时间戳
 * @return boolean 是否有效
 */
function judgeWeChatUnionTokenInDeadline($time = 0)
{
    if ($time > time())
        return true;
    else
        return false;
}

/**
 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
 * @param string $user_name 姓名
 * @return string 格式化后的姓名
 */
function userName_cut($user_name)
{
    $strlen = mb_strlen($user_name, 'utf-8');
    $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
    $lastStr = mb_substr($user_name, -1, 1, 'utf-8');
    return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}


/**
 * 获取设备的订阅主题
 * @param string $type 设备type
 * @param string $uuid 设备uuid
 * @return string
 */
function getDeviceTopicSub($type, $uuid)
{

    return '/' . $type . '/' . $uuid . '/devSub';
}


/**
 * 获取设备的发布主题
 * @param string $type 设备type
 * @param string $uuid 设备uuid
 * @return string
 */
function getDeviceTopicPub($type, $uuid)
{
    return '/' . $type . '/' . $uuid . '/devPub';

}


/**
 * POST 发送给mqtt服务器的接口
 * @param string $url
 * @param array $param
 * @return string content
 */
function post2Emq($url, $param = [])
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_USERNAME, '1d1ce2bee35fa');
    curl_setopt($ch, CURLOPT_PASSWORD, 'MjkyODQ3MDEyMDYyODY2NTA3NTA2MDAwNzQ0NzQ2MTg4ODA');
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //设置头部信息
    $headers = array('Content-Type:application/json; charset=utf-8', 'Content-Length: ' . strlen(json_encode($param)));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //执行请求
    $output = curl_exec($ch);

    return $output;

}
