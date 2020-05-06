<?php


namespace app\api\controller\wechat;


use app\common\encrypt\Aes;
use think\Controller;
use think\Validate;

class BaseWeChatController extends Controller
{


    protected $CODE_INVALID_TOKEN = -1;
    protected $CODE_FAIL = 0;
    protected $CODE_SUCCESS = 1;


    //微信小程序配置
    protected $mWeiChatOptions;
    protected $mWeiChatMiniOptions;


    public function _initialize()
    {
        $this->mWeiChatOptions = config('wechatConfig');
        $this->mWeiChatMiniOptions = config('wechatMiniConfig');
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
     * 校验token是否有效,如果有效则返回个人信息
     */
    protected function checkTokenInvalidFromQuest($questData)
    {

        $postData = [
            'token' => $questData
        ];

        $rule = new Validate(['token' => ['require', 'min' => 20]], ['token.require' => '缺少请求参数!', 'token.min' => '参数太短!',]);
        //判断是否匹配规则
        if (!$rule->check($postData)) {
            //提示错误信息
            return $this->utlisResponse($rule->getError(), false);
        }

        //start 解密，判断是否是aes加密后数据
        $token = Aes::opensslDecrypt($postData['token']);
        if (!$token) {
            return $this->utlisResponse('非法token！', false);
        }
        //解密后数据是否符合要求
        $decstryData = json_decode($token, true);

        $rule = new Validate(['name' => ['require',], 'deadline' => ['require', 'number'],]
            , ['name.require' => '缺少请求参数一!', 'deadline.require' => '缺少请求参数二!',
                'deadline.number' => '请求参数1类型错误!',]);
        //判断是否匹配规则
        if (!$rule->check($decstryData)) {
            //提示错误信息
            return $this->utlisResponse($rule->getError(), false);
        }
        //end 解密和判断数据，得到有效期

        //echo $decstryData['deadline'];

        //判断是否在有效期内
        if (!judgeUserTokenInDeadline($decstryData['deadline'])) {
            return $this->utlisResponse('token已失效！', false, [], $this->CODE_INVALID_TOKEN);
        }


        //判断是否和数据库的token一样？
        $user = model('User')->get(['name' => $decstryData['name']]);
        if (!$user) {
            return $this->utlisResponse('用户不存在！', false);
        }

        if (!($user->token === $postData['token'])) {
            return $this->utlisResponse('您已经在其他地方登陆！请重新登陆！', false);
        }

        return ($user);
    }
}