<?php

// +----------------------------------------------------------------------
// | WeChatDeveloper
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/WeChatDeveloper
// +----------------------------------------------------------------------

return [
    'token'          => 'test',
    'appid'          => 'wx9155b85421946427',
    'appsecret'      => '891b09d903592f2e05c7c9bb3e7a154b',
    'encodingaeskey' => '2kjEkCoPvogEu4zPF9jchd0NrmrMFTdVWWObB74YqyH',
    // 配置商户支付参数
    'mch_id'         => "1332187001",
    'mch_key'        => 'A82DC5BD1F3359081049C568D8502BC5',
    // 配置商户支付双向证书目录 （p12 | key,cert 二选一，两者都配置时p12优先）
    'ssl_p12'        => __DIR__ . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . '1332187001_20181030_cert.p12',
    // 'ssl_key'        => __DIR__ . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . '1332187001_20181030_key.pem',
    // 'ssl_cer'        => __DIR__ . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . '1332187001_20181030_cert.pem',
    // 配置缓存目录，需要拥有写权限
    'cache_path'     => '',
];