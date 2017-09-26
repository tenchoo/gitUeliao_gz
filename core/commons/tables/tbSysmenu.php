<?php
/**
 * 系统菜单管理
 * @author yagas
 * @datetime 2016/3/15 17:44
 */

class tbSysmenu extends CActiveRecord {

    public $id;
    public $type;
    public $title;
    public $fatherId;
    public $sortNum;
    public $url;
    public $route;
    public $hidden;

    private $_childrens = array();

    const TYPE_NAVIGATE = 'navigate';
    const TYPE_GROUP    = 'group';
    const TYPE_MENU     = 'menu';
    const TYPE_ACTION   = 'action';

    public function getAttributes($names = true){
        return parent::getAttributes($names);
    }

    public function tableName() {
        return '{{sysmenu}}';
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function primaryKey() {
        return 'id';
    }

	/**
	 * @return array 模型验证规则.
	 */
	public function rules()
	{
		return array(
			array('title,hidden,sortNum', 'required'),
			array('hidden','in','range'=>array('0','1')),
			array('sortNum', 'numerical', 'integerOnly'=>true),
			array('title,route,url','safe'),
            array('route','unique'),
		);
	}

	/**
	* Declares attribute labels.
	* @return array 定制字段的显示标签 (name=>label)
	*/
	public function attributeLabels(){
		return array(
			'id' => '菜单ID',
			'type' => '菜单类型',
			'title' => '菜单名称',
			'url' => '链接地址',
			'route' => '路由',
			'hidden' => '是否隐藏',
		);
	}

    public function setChildrens($childrens) {
        $this->_childrens = $childrens;
    }

    public function getChildrens() {
        return $this->_childrens;
    }

    /**
     * 递归的获取下级菜单内容
     * @param integer $id 父级菜单ID
     * @param string $type 菜单类型
     * @return array
     */
    public function findAllChildrens($id, $type, $permission=true) {
    	$criteria = new CDbCriteria();
    	$criteria->condition = "type=:t and fatherId=:id and hidden=0";
    	$criteria->order = "sortNum ASC, id ASC";
    	$criteria->params = array(':t'=>$type, ':id'=>$id);
    	$childrens = $this->findAll($criteria);

    	switch($type) {
    		case self::TYPE_NAVIGATE:
    			$type = self::TYPE_GROUP;
    			break;

    		case self::TYPE_GROUP:
    			$type = self::TYPE_MENU;
    			break;

    		case self::TYPE_MENU:
    			$type = self::TYPE_ACTION;
    			break;
    	}

    	$dataList = array();
    	foreach($childrens as $item) {
    		/** 是否需要进行权限检查 */
    		if($permission) {
    			/** 非超级管理员，且没有访问权限，直接略过 */
	    		if(Yii::app()->user->getState('isAdmin')==0 && !$item->hasAssign()){
	    			continue;
	    		}
    		}

    		$children = $this->findAllChildrens($item->id, $type);
    		if($children) {
    			$item->childrens = $children;
    		}
    		array_push($dataList, $item);
    	}
    	return $dataList;
    }

    public function findAllByNavigate($id) {
        $criteria = new CDbCriteria();
        $criteria->condition = "type=:t and fatherId=:id";
        $criteria->order = "sortNum ASC, id ASC";
        $criteria->params = array(':t'=>self::TYPE_GROUP, ':id'=>$id);
        $groups = $this->findAll($criteria);

        if(Yii::app()->user->getState('isAdmin')==0) {
        	foreach($groups as $index => $item) {
        		if(!$item->hasAssign()) {
        			unset($groups[$index]);
        		}
        	}
        }

        foreach($groups as & $item) {
            $item->childrens = $this->findAllByGroup($item->id);
        }
        return $groups;
    }

    public function findAllByGroup($id) {
        $criteria = new CDbCriteria();
        $criteria->condition = "type=:t and fatherId=:id";
        $criteria->order = "sortNum ASC, id ASC";
        $criteria->params = array(':t'=>self::TYPE_MENU, ':id'=>$id);
        $menus = $this->findAll($criteria);

        if(Yii::app()->user->getState('isAdmin')==0) {
        	foreach($menus as $index => $item) {
        		if(!$item->hasAssign()) {
        			unset($menus[$index]);
        		}
        	}
        }

        foreach($menus as & $item) {
            $item->childrens = $this->findAllByMenu($item->id);
        }
        return $menus;
    }

    /**
     * 获取菜单项的子项
     * @param unknown $id
     */
    public function findAllByMenu($id) {
        $criteria = new CDbCriteria();
        $criteria->condition = "type=:t and fatherId=:id";
        $criteria->order = "sortNum ASC, id ASC";
        $criteria->params = array(':t'=>self::TYPE_ACTION, ':id'=>$id);
        $result = $this->findAll($criteria);

        if(Yii::app()->user->getState('isAdmin')==0) {
        	foreach($result as $index => $item) {
        		if(!$item->hasAssign()) {
        			unset($result[$index]);
        		}
        	}
        }
        return $result;
    }

    /**
     * 判断角色ID是否有访问权限
     * @param integer $roleId
     * @return boolean
     */
    public function isAssign($roleId) {
        $condition = array('roleId'=>$roleId, 'menuId'=>$this->id);
        $permission = tbPermission::model()->findByAttributes($condition);
        return !is_null($permission)? true : false;
    }

    /**
     * 检验菜单是否有访问权限
     * @return boolean
     */
    public function hasAssign() {
    	$roles = Yii::app()->user->getState('roles');
    	if(!$roles) {
    		return false;
    	}

    	$criteria = new CDbCriteria(['condition'=>'menuId='.$this->id]);
    	$criteria->addInCondition('roleId', $roles);

    	$menu = tbPermission::model()->findAll($criteria);
    	if($menu) {
    		return true;
    	}
    	return false;
    }

    /**
     * 获取我有权限访问的导航栏菜单ID
     * @return array
     */
    public function fetchMyNavigate() {
        $tMenu = tbSysmenu::model()->tableName();
        $tPermission = tbPermission::model()->tableName();
        $sql = "SELECT id FROM {$tMenu} t WHERE `type`='navigate' AND EXISTS(SELECT 1 FROM {$tPermission} WHERE t.`id`=menuId AND roleId IN (:roles))";
        $cmd = Yii::app()->getDb()->createCommand($sql);
        $roles = implode(',', Yii::app()->user->getState('roles'));
        $cmd->bindValue(':roles', $roles, PDO::PARAM_STR);
        return $cmd->queryColumn();
    }

	/**
	 * 保存前的操作
	 */
	protected function beforeSave(){
		if($this->isNewRecord){
			$this->id = $this->createId();
		}

		return true;
	}

	/**
	* 生成菜单ID
	*
	*/
	private function createId(){
        $result = $this->find( array(
				'select'=>'id',
				'condition'=>"type=:t and fatherId=:id",
				'params'=>array(':t'=>$this->type, ':id'=>$this->fatherId),
				'order'=>'id DESC',
			));

		if( $result ){
			$menuInfo = str_split( $result->id, 3 );
		}else{
			$menuInfo = str_split( $this->fatherId, 3 );
		}

		$types = array('navigate','group','menu','action');
		$key = array_search($this->type,$types );
		$menuInfo[$key] = str_pad($menuInfo[$key]+1, 3, '0', STR_PAD_LEFT);
		return implode('',$menuInfo);
	}
}