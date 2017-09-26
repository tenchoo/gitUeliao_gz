<?php

/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2016/5/4
 * Time: 15:32
 */
class AppPush {

    protected $conf = array();
    protected $igt;

    public function __construct() {
        $this->conf = (array)json_decode( tbConfig::model()->get('getui_config') );
        $this->igt = new IGeTui($this->conf['host'], $this->conf['appKey'], $this->conf['masterSecret']);
    }

    public function pushMessageToSingle($cid, $msg){
        $template = $this->messageTemplate($msg);
        $message  = $this->createMessage($template, 'single');
        $target   = $this->createTraget($cid);

        $rep = $this->igt->pushMessageToSingle($message, $target);
        if($rep['result']==='ok') {
            return true;
        }
        return false;
    }

    public function pushMessageToList($cids, $msg, $template=null) {
        $targetList = array();

        if(is_null($template)) {
            $template  = $this->messageTemplate($msg);
        }
        $message   = $this->createMessage($template,'list');
        $contentId = $this->igt->getContentId($message);

        foreach ($cids as $cid) {
            $target   = $this->createTraget($cid);
            array_push($targetList, $target);
        }

        $rep = $this->igt->pushMessageToList($contentId, $targetList);
        if($rep['result']==='ok') {
            return true;
        }
        return false;
    }

    public function pushMessageToApp($cids, $msg) {
        $notice = $this->messageTemplate($msg);
        // $result = $this->pushMessageToList($cids['all'], $msg, $notice);
        $result = $this->pushMessageToList($cids['all'], $msg);

        if($cids['android']) {
            $notice = $this->notifyTemplate($msg);
            $result = $this->pushMessageToList($cids['android'], $msg, $notice);
        }
    }

    /**
     * 创建推送消息模板推送
     * @param $msg
     * @return IGtTransmissionTemplate
     */
    private function messageTemplate($msg) {
        $template =  new IGtTransmissionTemplate();
        $template->set_appId($this->conf['appID']);//应用appid
        $template->set_appkey($this->conf['appKey']);//应用appkey
        $template->set_transmissionType(2);//透传消息类型
        $template->set_transmissionContent($msg);//透传内容
        //iOS推送需要设置的pushInfo字段

        $apn = new IGtAPNPayload();
        $alertmsg = new SimpleAlertMsg();
        $alertmsg->alertMsg = "你有新的消息";
        $apn->alertMsg = $alertmsg;
        $apn->badge=1;
        $apn->category="ACTIONABLE";
        $apn->contentAvailable=1;
        $template->set_apnInfo($apn);
        return $template;
    }

    private function notifyTemplate($msg,$title='优易料通知',$content='你有新的消息',$logo='app_icon.png',$isRing=true,$isVibrate=true) {
        $template =  new IGtNotificationTemplate();
        $template->set_appId($this->conf['appID']);//应用appid
        $template->set_appkey($this->conf['appKey']);//应用appkey
        $template->set_transmissionType(2);//透传消息类型
        $template->set_transmissionContent($msg);//透传内容
        $template->set_title($title);//通知栏标题
        $template->set_text($content);//通知栏内容
        $template->set_logo($logo);//通知栏logo
        $template->set_isRing($isRing);//是否响铃
        $template->set_isVibrate($isVibrate);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除

        return $template;
    }

    /**
     * 创建个推消息类型
     * @param        $template
     * @param string $messageType
     * @return IGtListMessage|IGtSingleMessage
     */
    private function createMessage($template, $messageType='single') {
        if($messageType==='single') {
            $instance = new IGtSingleMessage();
        }
        else {
            $instance = new IGtListMessage();
        }

        $instance->set_isOffline(true);
        $instance->set_offlineExpireTime(3600*12*7);
        $instance->set_data($template);
        $instance->set_PushNetWorkType(0);
        return $instance;
    }

    /**
     * 创建推送目标
     * @param $cid
     * @return IGtTarget
     */
    private function createTraget($cid) {
        $target = new IGtTarget();
        $target->set_appId($this->conf['appID']);
        $target->set_clientId($cid);
        return $target;
    }
}
