<?php
/**
 * 用户角色组关联关系
 * @author yagas
 *
 */
class tbUserrole extends CActiveRecord {
	
	public $id;
	public $userId;
	public $roleId;

	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	public function tableName() {
		return '{{userRole}}';
	}

	public function primaryKey() {
		return "id";
	}

	public function rules() {
		return array(
			array('roleId,userId','required'),
			array('roleId,userId', "numerical","integerOnly"=>true,'min'=>1),
		);
	}
	
	/**
	 * 更新会员角色信息
	 * 通过职位ID获取角色信息，同一职位可能包含有多个角色
	 * 清除原有的用户角色绑定关系
	 * 更新会员会员关系信息
	 * @param CEvent $event
	 */
	public static function ESaveUserRoleInfo(CEvent $event) {
		$userId = $event->sender->userId;
		$roles = tbRoleGroup::model()->findAllByAttributes(['deppositionId'=>$event->sender->depPositionId]);
		tbUserrole::model()->deleteAllByAttributes(['userId'=>$userId]);
		if($roles) {
			$tableName = tbUserrole::model()->tableName();
			$sql = "insert into {$tableName}(userId,roleId)values";
			$values = "";
			foreach($roles as $item) {
				$values .= sprintf(",('%s','%s')", $userId, $item->roleId);
			}
			if($values) {
				$sql .= substr($values,1);
				$cmd = Yii::app()->getDb()->createCommand($sql);
				$cmd->execute();
			}
		}
		
		
	}
}