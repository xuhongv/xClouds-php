<?php
use think\Route;

/*********** 授权相关 *************/
Route::rule('oauth/token', 'api/oauth.AliGenieOauth/token');
/*************** 天猫精灵 相关   ***************/
Route::rule('oauth/aligenie', 'api/oauth.AliGenieOauth/authorize');
Route::rule('oauth/AliGenieGateWay', 'api/oauth.AliGenieGateWay/index');
/*************** 小爱 相关   ***************/
Route::rule('oauth/miot', 'api/oauth.MiotOauth/authorize');
Route::rule('oauth/MiotGateWay', 'api/oauth.MiotGateWay/index');



Route::rule('api/test', 'api/test.Test/index');


//微信服务器签名校正
Route::rule('wechat/checkSign','wechat/BaseWeChat/checkClouds');
//微信设备列表
//Route::rule('wechat/GuideDeviceTypeList','wechat/GuideDeviceTypeListActivity/index');
//微信创建菜单
Route::rule('wechat/creatMenu','wechat/BaseWeChat/creatMenu');
//微信个人中心
Route::rule('wechat/user','wechat/BindPhoneActivity/index');
//emq
Route::rule('emq/callback','api/emq.Emq/index');
