<?php


namespace app\extra;


return [
    'userTokenExpire' => 1 * 24 * 3600,//用户token有效期 单位秒-->   3天 * 24（每天24小时）* 3600 （一个小时3600秒） 3 * 24 * 3600
    'wechatUnionTokenExpire' => 15 * 60,//微信公众号用户操作绑定设备token有效期 单位秒-->   15分钟
    'wechatUserExpire' => 15 * 60,//微信公众号用户获取授权码有效期 单位秒-->   15分钟
];