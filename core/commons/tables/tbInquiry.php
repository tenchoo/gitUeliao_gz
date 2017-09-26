<?php
/**
 * Created by PhpStorm.
 * User: yagas
 * Date: 2016/2/27
 * Time: 14:08
 */
class tbInquiry extends CActiveRecord {

	public $id;
    public $inquiryId; //询盘标识
    public $memberId; //客户ID
    public $productId; //产品ID
    public $hasNew;
    public $lastTime;
	public $title;
	public $serial;
	public $mainPic;


    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{inquiry}}';
    }

    public function primaryKey() {
        return 'id';
    }

    public function rules() {
        return array(
            ['inquiryId,memberId,productId','required'],
            ['hasNew,lastTime,title,serial,mainPic','safe']
        );
    }
    
    public function relations() {
    	return[
    		'member' => array(self::BELONGS_TO, 'tbMember', 'memberId'),
    		'product' => array(self::BELONGS_TO, 'tbProduct', 'productId')
    	];
    }
    
    public function lastMessage($html=true) {
    	if($this->isNewRecord) {
    		throw new CHttpException(500, Yii::t('base','new record not found this method'));
    	}
    	
    	$criteria = new CDbCriteria();
    	$criteria->order = "createTime DESC";
    	$criteria->condition = "inquiryId=:id";
    	$criteria->params = array(':id'=>$this->inquiryId);
    	
    	$message = tbInquiryContent::model()->find($criteria);

		if(!$message) {
			return new tbInquiryContent();
		}

		if(!$html) {
			if($message->mime !== 'message') {
				$message->content = $message->mime.'::'.$message->content;
			}

			return $message;
		}

    	if($message) {
    		switch ($message->mime) {
    			case 'image':
    				$message->content = $this->showImage($message);
    				break;
    				
    			case 'voice':
					$message->content = $this->showVoice($message);
    				break;
				default:
					$message->content = urldecode( $message->content );
					break;
    		}
    		return $message;
    	}
    	return new tbInquiryContent();
    }

	public function showVoice(tbInquiryContent $msg) {
		$url = Yii::app()->urlManager->createUrl('/inquiry/default/source', array('id'=>$msg->mime.'::'.$msg->content));
		list($type,$key)=explode('::',$msg->content);
		$content = "声音文件：". $key;
		return $content . CHtml::link("点击播放",$url,['target'=>'_blank','data-type'=>'audio']);
	}

	public function showImage(tbInquiryContent $msg) {
		$url = Yii::app()->getController()->createUrl('/inquiry/default/source', array('id'=>$msg->mime.'::'.$msg->content));
		list($type,$key)=explode('::',$msg->content);
		$content = "图片文件：". $key;
		return $content . CHtml::link("点击查看",$url,['target'=>'_blank','data-type'=>'image']);
	}
}