<?php

/**
 * 页面碎片信息表模型
 *
 * @property integer	$pieceId
 * @property integer	$state			状态：0正常，1已删除
 * @property integer	$parentId		上级页面ID
 * @property timestamp	$updateTime
 * @property string		$title			名称
 * @property string		$mark			标识，标识具有唯一性
 * @property string		$content		碎片内容
 * @version 0.1
 * @package CActiveRecord
 */

class tbPiece extends CActiveRecord {

	/**
	 * 构造数据库表模型单例方法
	 * @param system $className
	 * @return static
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{piece}}';
	}

	public function init(){
		$this->mark = '';
		$this->content = '';
	}

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()	{
		return array(
			array('parentId,title','required'),
			array('title,mark','length','max'=>'15'),
			array('mark,content','required','on'=>'setcontent'),
			array('state','in','range'=>array(0,1)),
			array('parentId', 'numerical','integerOnly'=>true),
			array('mark','match','pattern'=>'/^[a-zA-Z_]+$/','message'=>Yii::t('base','Mark must be in English'),'on'=>'setcontent'),
			array('mark','unique','on'=>'setcontent'),
			array('title,mark,content','safe'),
		);
	}

	public function attributeLabels(){
		return array(
			'parentId'=>'所属页面ID',
			'title'=>'标题',
			'mark'=>'标识',
			'content'=>'碎片内容',
		);
	}

	/**
	* 删除碎片
	* @param array  $id 		 表PK
	* @param integer  $message	 提示信息
	*/
	public function del( $id,&$message ){
		if(!is_numeric($id) || $id<1 ) return false;

		//查找其是否有内容，有内容不允许删除。
		if( $this->count('state = 0 and parentId = '.$id)){
			$message = Yii::t('category','Under this classification and information, please delete the information');
			return false;
		}

		if( $this->updateByPk( $id,array('state'=>'1'), 'state=0') ){
			$message = null;
			return true;
		}

		return false;
	}

	/**
	* 根据上级ID取得碎片/页面列表
	* @param integer  $parentId
	*/
	public static function getList( $parentId ){
		if( !is_numeric($parentId) ) return ;


		$criteria = new CDbCriteria;
		$criteria->select = 'pieceId,title,parentId';
		$criteria->compare('t.parentId',$parentId);
		$criteria->compare('t.state','0');
		$data = self::model()->findAll($criteria);
		foreach($data as &$val){
			$c = 0;
			if( $parentId=='0' ){
				$c = $val->count('state = 0 and parentId = '.$val->pieceId);
			}

			$val = $val->getAttributes(array('pieceId','title','parentId'));
			$val['childrens'] = ($c)?true:false;
		}
		return $data;
	}




	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		$this->updateTime = new CDbExpression('NOW()');
		return true;
	}
}
?>