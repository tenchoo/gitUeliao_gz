<?php
/**
 * 与用户关联的设备信息
 * @author yagas
 * @version 0.1
 *
 * @property integer id        主键
 * @property integer msgtype   消息类型
 * @property integer memberId  会员编号
 * @property string  cid       设备个推编号
 * @property string  userType  会员类型
 * @property integer loginTime 设备最后登陆时间
 * @property string  os        设备系统ios或android
 */
class tbMemberDevice extends CActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{member_device}}';
	}

	public function primaryKey() {
		return 'id';
	}

	public function rules() {
		return[
			['memberId,cid,userType,loginTime,os','required','message'=>'need fill {attribute} value'],
			['memberId,loginTime,msgtype','numerical']
		];
	}

	/**
	 * 记录会员登陆时的设备个推CID信息
	 * @param integer $memberId 会员编号
	 * @param string  $cid      设备CID
	 * @return boolean
	 */
	public static function log($memberId, $cid, $userType,$os) {
		$info = tbMemberDevice::model()->find("memberId=:id and cid=:device and userType=:type and os=:os", [':id'=>$memberId, ':device'=>$cid, ':type'=>$userType, ':os'=>$os]);
		if(is_null($info)) {
			$info = new tbMemberDevice();
			$info->memberId = $memberId;
			$info->cid = $cid;
			$info->loginTime = time();
			$info->userType = $userType;
			$info->os = $os;
		}
		else {
			$info->loginTime = time();
		}
		return $info->save();
	}

	/**
	 * 获取会员设备个推CID
	 * @param integer $memberId 会员编号
	 * @return null|array
	 */
	public function getMemberCids($memberId) {
		$ids = null;
		$result = $this->findAll("memberId=:id", [':id'=>$memberId]);
		if( !is_null($result) ) {
			$ids = array_map(function($row){
				return $row->cid;
			}, $result);
		}

		return $ids;
	}
}