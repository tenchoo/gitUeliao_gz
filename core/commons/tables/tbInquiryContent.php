<?php
/**
 * 询盘详情
 * User: yagas
 * Date: 2016/2/27
 * Time: 12:43
 */

require_once Yii::getPathOfAlias('system').'/../vendors/getui/IGt.Push.php';

class tbInquiryContent extends CActiveRecord {

    public $inquiryId; //询盘标识
    public $productId; //产品ID
    public $memberId;  //会员ID
    public $mark; //发内容标识
    public $userId; //内容发起者
    public $mime; //内容标识
    public $content; //内容
    public $createTime; //发送时间

    private $tbGroup; //询盘讨论组ORM数据库模型对象
    private $tbProduct;  //产品表ORM数据库模型对象

    /**
     * 初始化操作
     * 对询盘讨论组对象和产品模型对象进行初始化赋值
     *
     */
    public function init() {
        parent::init();
        $this->tbGroup   = tbInquiry::model();
        $this->tbProduct = tbProduct::model();

        if($this->getScenario()==="member") { //用户询盘消息推送给所属业务员
            $this->attachEventHandler('onAfterSave', array('EventPushToApp', 'sendSalesman'));
        }
        elseif($this->getScenario()==="salesman") { //业务员或客服回复的询盘消息推荐给用户
            $this->attachEventHandler('onAfterSave', array('EventPushToApp', 'sendMember'));
        }
        else { //后台客服回复内容同时推送给移动端
            $this->attachEventHandler('onAfterSave', array('EventPushToApp', 'sendSalesman'));
            $this->attachEventHandler('onAfterSave', array('EventPushToApp', 'sendMember'));
        }
    }

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{inquiry_content}}';
    }

    public function primaryKey() {
        return 'id';
    }

    public function rules() {
        return array(
            ['productId,memberId,mark,userId,mime,content','required'],
            ['createTime','numerical',"integerOnly"=>true]
        );
    }

    protected function beforeSave() {
        $this->createTime = time();

        if($this->getScenario()==='member') {
            $this->userId = $this->memberId;
            $result = $this->checkGroup($this->productId, $this->memberId);
            if(!$result) {
                return false;
            }
        }
        else {
            $this->inquiryId = md5(strval($this->productId).strval($this->userId));
        }

        return parent::beforeSave();
    }


    public function showVoice() {
    	$url = Yii::app()->urlManager->createUrl('/inquiry/default/source', array('id'=>$this->mime.'::'.$this->content));
        list($type,$key)=explode('::',$this->content);
        $content = "声音文件：". $key;
        return $content . CHtml::link("点击播放",$url,['target'=>'_blank','data-type'=>'audio']);
    }
    
    public function showImage() {
    	$url = Yii::app()->getController()->createUrl('/inquiry/default/source', array('id'=>$this->mime.'::'.$this->content));
        list($type,$key)=explode('::',$this->content);
    	$content = "图片文件：". $key;
    	return $content . CHtml::link("点击查看",$url,['target'=>'_blank','data-type'=>'image']);
    }

    public function userIcon() {
        $prefix = 'http://'.$_SERVER['SERVER_NAME'];
        if($this->isNewRecord) {
            return $prefix.'/static/images/service.jpg';
        }

        if($this->mark==='custom_service') {
            return $prefix.'/static/images/service.jpg';
        }

        $user = tbProfile::model()->findByPk($this->userId);
        if($user) {
            return Yii::app()->getController()->getImageUrl($user->icon, null);
        }

        return $prefix.'/static/images/service.jpg';
    }

    /**
     * 创建聊天讨论组
     * 1).用户发起的询盘首先对讨论组进行检测，当讨论组不存在的时才进行创建，否则略过此步骤
     * 2).讨论组的ID由咨询的产品ID+发起咨询用户ID组成，使用md5进行序列化
     * 3).创建讨论组时，进行询盘的产品不存在时，创建组创建失败
     * 4).询盘创建失败时，错误信息记录到log日志中
     *
     * @param string $groupId 讨论组ID
     */
    protected function checkGroup($productId, $memberId) {
        $this->inquiryId = md5(strval($productId).strval($memberId));
        if($this->getScenario() === 'member') {
            $group = $this->tbGroup->find(
                "inquiryId=:id",
                array( ':id' => $this->inquiryId )
            );

            if(is_null($group)) { //讨论组不存在时进行创建
                $product = $this->tbProduct->findByPk($productId);
                if(is_null($product)) { //无法找到匹配的产品
                    $this->addError('productId', Yii::t('base','Not found Record'));
                    return false;
                }

                //创建讨论组
                $GroupObject = get_class($this->tbGroup);
                $group = new $GroupObject;
                $group->setAttributes(array(
                    'inquiryId' => $this->inquiryId,
                    'productId' => $productId,
                    'memberId' => $memberId,
                    'title' => $product->title,
                    'serial' => $product->serialNumber,
                    'mainPic' => $product->mainPic,
                    'lastTime' => $this->createTime,
                    'hasNew' => 1
                ));

                if(!$group->save()) { //创建讨论组失败
                    $errors = $group->getErrors();
                    $error = array_shift($errors);

                    //写log日志
                    Yii::log($error[0], CLogger::LEVEL_ERROR, __CLASS__.'::createGroup');
                    $this->addError('inquiryId', $error[0]);
                    return false;
                }
            }
            else { //更新后台询盘列表状态
                $group->hasNew = 1;
                $group->lastTime = $this->createTime;
                $group->save();
            }
            return true;
        }
    }
}

class EventPushToApp {
    /**
     * 用户询盘消息推送给所属业务员
     * @param CEvent $event
     */
    public static function sendSalesman(CEvent $event) {
        $model = $event->sender;

        /** 推送消息内容：产品ID##发消息用户ID##消息类型##消息内容 */
        $message = sprintf('%s##%s##%s##%s', $model->productId, $model->memberId, $model->mime, $model->content);

        $user = tbMember::model()->findByPk($model->userId);
        if($user instanceof tbMember) {
            $salesman = tbMemberDevice::model()->findAll("memberId=:uid and loginTime>:expire", array(':uid'=>$user->userId, ':expire'=>2592000));

            if($salesman) {
                $android = [];
                $all = [];

                foreach($salesman as $item) {
                    array_push($all, $item->cid);
                    if(strtolower($item->os) === "android") {
                        array_push($android, $item->cid);
                    }
                }
                
                $push = new AppPush();
                $push->pushMessageToApp(['android'=>$android, 'all'=>$all], $message);
            }
        }
    }

    /**
     * 业务员或客服回复的询盘消息推荐给用户
     * @param CEvent $event
     */
    public static function sendmember(CEvent $event) {
        $model = $event->sender;

        /** 推送消息内容：产品ID##接收消息用户ID##消息类型##消息内容 */
        $message = sprintf('%s##%s##%s##%s', $model->productId, $model->userId, $model->mime, $model->content);

        $salesman = tbMemberDevice::model()->findAll("memberId=:uid and loginTime>:expire", array(':uid'=>$model->userId, ':expire'=>2592000));

        if($salesman) {
            $android = [];
            $all     = [];

            foreach($salesman as $item) {
                array_push($all, $item->cid);
                if(strtolower($item->os) === "android") {
                    array_push($android, $item->cid);
                }
            }

            $push = new AppPush();
            $push->pushMessageToApp(['android'=>$android, 'all'=>$all], $message);
        }

    }
}