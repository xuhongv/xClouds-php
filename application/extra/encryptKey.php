<?php
/**
 * Created by PhpStorm.
 * User: xuhongv
 * Date: 2019-02-04
 * Time: 15:37
 */

namespace app\extra;


return [
    'AES_TOKEN_KEY' => '_Xuhong_Yss_Key_', // 生成密钥 必须16位 24位
    'USER_PASSWORD_ADD' => 'XUHONG20190204',//生成用户密码时自动在用户密码的后面加上此，再32位MD5加密存数据
    'AES_WECHAT_UNIONID_TOKEN' => 'XUHONG20190317',//微信公众号生成包含 unionid的token的密钥
];