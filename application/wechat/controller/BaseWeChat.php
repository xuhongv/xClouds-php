<?php
/**
 * Created by PhpStorm.
 * User: xuhongv
 * Date: 2019-02-01
 * Time: 00:38
 */

namespace app\wechat\controller;


use think\Controller;
use think\Session;

class BaseWeChat extends Controller
{
    
    protected $mWeiChatOptions;
    protected $localUser;


    protected $CODE_INVALID_TOKEN = -1;
    protected $CODE_FAIL = 0;
    protected $CODE_SUCCESS = 1;



    //模拟数据
    protected $devicesList = [
        [
            'name' => '灯具',
            'icon' => 'http://placeholder.qiniudn.com/100x100',
            'des' => '首款无暇光、无频闪的智能灯',
            'actionNet' => '快速开关五次，直到灯具出现呼吸闪烁，表示进入配网模式！',
            'actionNetPic' => 'https://www.xuhongv.com/icon/lightNet.jpg',
        ],
    ];
    
    
    public function _initialize()
    {
        $this->mWeiChatOptions = config('wechatConfig');
        $this->localUser = $this->isLogin();
    }

//这几天弄微信，token验证总是不过。
//经过多方查找，终于找到了问题。
//是因为写代码时打开了页面输出和调试模式。
//经过总结如下：
//注意：关闭debug模式。关闭页面trace信息输出，启用sae引擎
//做到这三步，配置成功没有问题
//config.php文件：
//'SHOW_PAGE_TRACE' =>false, // 显示页面Trace信息
    
    public function checkClouds()
    {
        
        if (!request()->get()) {
            return '';
        }
        $weiChatMsg = ((input("get.")));
        
        //获得参数 signature nonce token timestamp echostr
        $nonce = $weiChatMsg['nonce'];
        $timestamp = $weiChatMsg['timestamp'];
        $token = $this->mWeiChatOptions['token'];
        $signature = $weiChatMsg['signature'];
        //将$nonce, $timestamp, $token这3个字符串按字典序排序后拼接成字符串（放入数组只是为了排序）
        $arr = [$nonce, $timestamp, $token];
        sort($arr, SORT_STRING);
        $str = implode($arr);
        //sha1加密后与signature比对，相同则来自微信，否则是坏人发来的
        $sha1str = sha1($str);
        if ($sha1str != $signature) {
            return '';
        } else {
            return $weiChatMsg['echostr'];
            
        }
    }
    
    
    public function creatMenu()
    {
        
        $data = [
            'button' => [
                [
                    "type" => "view",
                    "name" => "个人中心",
                    "url" => $this->mWeiChatOptions['weichatDomain'] . "/wechat/user" //点击跳转的界面链接
                ],
            ]
        ];
        
        try {
            // 实例接口
            $menu = new \WeChat\Menu($this->mWeiChatOptions);
            // 执行创建菜单
            $result = $menu->create($data);
            $result['my']=$data;
            
        } catch (Exception $e) {
            // 异常处理
            echo $e->getMessage();
        }

        return json($result);
    }





    /**
     * @param string $message 响应提示
     * @param bool $isSuccess 是否成功响应，默认是成功
     * @param array $extraArry 额外数据数组
     * @param int $CODE_FAIL 错误码
     * @return \think\response\Json 返回json
     */
    protected function utlisResponse($message = '', $isSuccess = true, $extraArry = [], $CODE_FAIL = 0)
    {
        $code = $this->CODE_SUCCESS;
        if (!$isSuccess) {
            $code = $this->CODE_FAIL;
            if (0 != $CODE_FAIL)
                $code = $this->CODE_INVALID_TOKEN;
            $msg = '操作失败！' . $message;
        } else {
            $msg = '操作成功！' . $message;
        }

        $data = [
            'code' => $code,
            'msg' => $msg,
            'data' => $extraArry,
        ];
        return json($data);
    }



    /**
     * 检查本地浏览器是否已经登录，有缓存数据？
     * @return bool|mixed
     */
    protected function isLogin()
    {

        $value = Session::get(config('session.wechat_page_name_key'), config('session.wechat_page_name_scope'));
        if ($value)
            return $value;
        else
            return false;
    }

    /**
     * 本地浏览器退出登录，清除缓存数据？
     */
    protected function LoginOut()
    {
        Session::set(config('session.wechat_page_name_key'), null, config('session.wechat_page_name_scope'));
    }

    /**
     * 本地浏览器登录，增加缓存数据
     *
     * @param $saveData 缓存数据
     */
    protected function LoginIn($saveData)
    {

        //session 控制会话: 第1个是参数名字session的key 第二个是session的值  第二个是作用域
        Session::set(config('session.wechat_page_name_key'), $saveData, config('session.wechat_page_name_scope'));
    }

}