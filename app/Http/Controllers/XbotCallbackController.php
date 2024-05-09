<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\WechatBot;
use App\Models\WechatClient;
use App\Models\WechatContact;
use App\Models\WechatContent;
use App\Models\WechatBotContact;
use App\Models\WechatMessage;
use App\Models\WechatMessageFile;
use App\Models\WechatMessageVoice;
use App\Models\XbotSubscription;
// use App\Chatwoot\Chatwoot;
// use Illuminate\Support\Facades\Http;
// use App\Services\Xbot;
// use App\Services\Icr;
// use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Str;
// use App\Jobs\SilkConvertQueue;
use App;

class XbotCallbackController extends Controller
{
    private $isProduction = false;
    private $msgType;

    /**
     * @param bool $isProduction
     */
    public function __construct()
    {
        $this->isProduction = App::environment('production');
    }


    public function __invoke(Request $request, string $token){
        // 事件类型处理
        $msgType = $request['type']??false;
        {
            if(!$msgType) {
                $msg = '参数错误: no type';
                Log::error(__CLASS__, [__LINE__, $token, $request->all(), $msg]);
                return $this->returnNull();
            }
            // 维护一个unique数组，记录所有的type：MT_USER_LOGIN etc.
            if (!$this->isProduction) {
                $xbotTypes = Cache::store('file')->get('xbot_types',[]);
                array_unshift($xbotTypes, $msgType);
                array_unique($xbotTypes);
                Cache::store('file')->set('xbot_types',$xbotTypes);
            }
            // 忽略的消息类型
            $ignoreHooks = [
                'MT_RECV_MINIAPP_MSG' => '小程序信息',
                "MT_WX_WND_CHANGE_MSG"=>'',
                "MT_DEBUG_LOG" =>'调试信息',
                "MT_UNREAD_MSG_COUNT_CHANGE_MSG" => '未读消息',
                "MT_DATA_WXID_MSG" => '从网络获取信息',
                "MT_TALKER_CHANGE_MSG" => '客户端点击头像',
                "MT_RECV_REVOKE_MSG" => 'xx 撤回了一条消息',
                "MT_DECRYPT_IMG_MSG_TIMEOUT" => '图片解密超时',
            ];
            if(in_array($msgType, array_keys($ignoreHooks))){
                Log::debug(__CLASS__, [__LINE__, "忽略的消息类型", $msgType]);
                return $this->returnNull();
            }
            $this->msgType = $msgType;
        }

		// 第x号微信客户端ID
        $clientId = $request['client_id']??false;
        if(!$clientId) {
            $errorInfo = '参数错误: no client_id';
            Log::error(__CLASS__, [__LINE__, $token, $request->all(), $errorInfo]);
            return $this->returnNull();
        }

        // Windows机器（根据token确定）
        $wechatClient = WechatClient::where('token', $token)->first();
        if(!$wechatClient) {
            $errorInfo = '找不到windows机器';
            Log::error(__CLASS__, [__LINE__, $clientId, $token, $request->all(), $errorInfo]);
            return $this->returnNull();
        }
        $wechatClientId = $wechatClient->id;
        $wechatClientName = $wechatClient->token;

        // Debug info
        if (!$this->isProduction) Log::debug(__CLASS__, [__LINE__, $type, $clientId, $wechatClientName, $request->all()]);

        // 客户端登陆的bot的wxid，部分消息没有wxid，
        $cliendWxid = $data['wxid']??null; //从raw-data中post过来的wxid,

        $rawData = $request['data'];
        // 忽略1小时以上的信息 60*60
        $timestamp = $rawData['timestamp']??false;
        if($timestamp &&  now()->timestamp - $rawData['timestamp'] > 1*60*60 ) {
            $info = '忽略1小时以上的信息';
            Log::info(__CLASS__, [__LINE__, $wechatClientName, $type, $info]);
            return $this->returnNull();
        }

        // 忽略公众号消息
        $fromWxid = $data['from_wxid']??false;
        if($fromWxid && Str::startsWith($fromWxid, 'gh_')) {
            $info = '接收到公众号消息: 已忽略';
            Log::info(__CLASS__, [__LINE__, $wechatClientName, $type, $info]);
            return $this->returnNull();
        }

        // 处理登陆、登出、扫码、绑定
        switch ($type) {
        	case 'MT_RECV_QRCODE_MSG':
        		$this->processQrCode();
        		break;
        	case 'MT_USER_LOGIN':
        		$this->processLogin();
        		break;
        	case 'MT_USER_LOGOUT':
        		$this->processLogout();
        		break;
        	case 'MT_CLIENT_DISCONTECTED':
        		$this->processLogout();
        		break;


        	default:
        		// code...
        		break;
        }

        // 处理消息
        $this->processMessage();


    }
    private function returnNull() {
        return response()->json(null);
    }

    private function processQrCode()
    {
        return $this->returnNull();
    }

    private function processLogin()
    {
        return $this->returnNull();
    }

    private function processLogout()
    {
        return $this->returnNull();
    }

    private function processMessage()
    {
        // 处理文本消息
            // 繁体 转 简体
            // ori: 可能繁体
            // zh-cn: 简体
            // numbers：
            // urls：
        // $this->processTextMessage();

        // 收到的消息，按照bot，每个bot一个队列！
            // 收到的消息，按照type分类统计！
        // 处理其他消息

        return $this->returnNull();
    }

}
