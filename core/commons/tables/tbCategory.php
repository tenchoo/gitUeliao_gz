<?php
class tbCategory extends CActiveRecord {

	public $title;
	public $categoryId;
	public $parentId;
	public $lft;
	public $rft;
	public $seoTitle;
	public $seoKeywords;
	public $seoDesc;

	public function rules() {
		return array(
			array("title,parentId","required"),
			array('title','length','min'=>'2','max'=>'10'),
			array("parentId","numerical"),
			array('seoTitle,seoKeywords,seoDesc','safe'),
			array('seoTitle','length','max'=>'50'),
			array('seoKeywords','length','max'=>'100'),
			array('seoDesc','length','max'=>'200'),
		);
	}

	public function tableName() {
		return "{{category}}";
	}

	public function primaryKey() {
		return "categoryId";
	}

	public static function model($className=__CLASS__) {
		return parent::model( $className );
	}

	/**
	 * 获取当前分类下的子类目列表
	 * @param integer $categoryId 类目编号
	 * @return array
	 */
	public function getChildrens( $categoryId, $all=false ) {
		$cmd = $this->getDbConnection()->createCommand("select categoryId,title,lft,rft from {$this->tableName()} where parentId=:id order by lft ASC");
		$cmd->bindParam( ':id', $categoryId, PDO::PARAM_INT );
		$result = $cmd->queryAll();
		foreach( $result as & $item ) {
			if( $item['rft']-$item['lft'] > 1 ) {
				if( !$all ) {
					$item['childrens'] = array('hasChildren');
				}
				else {
					$item['childrens'] = $this->getChildrens( $item['categoryId'], $all );
				}
			}
			else {
				$item['childrens'] = array();
			}

			if( $all ){
				unset($item['rft'],$item['lft']);
			}
		}
		return $result;
	}

	public function getTrees( $categoryId, $all=false ) {

		if( $categoryId == 0 ) {
			return $this->getChildrens(0, $all);
		}

		$category = $this->findByPk( $categoryId );
		if( is_null($category) ) {
			return array();
		}

		$cmd = $this->getDbConnection()->createCommand( "select categoryId,title from {$this->tableName()} where lft<:lft and rft>:rft order by lft ASC" );
		$cmd->bindParam( ':lft', $category->lft );
		$cmd->bindParam( ':rft', $category->rft );
		$trees = $cmd->queryAll();

		if( $trees ) {
			$brothers = $this->getChildrens( $category->parentId, $all );

			$layout = array_pop( $trees );
			if( $brothers ) {
				$layout['childrens'] = $brothers;
			}
			while( $item=array_pop( $trees ) ) {
				$item['childrens'] = $layout;
				$layout = $item;
			}
			$root = $this->getChildrens(0);
			foreach( $root as & $item ) {
				if( $item['categoryId']==$layout['categoryId'] ) {
					$item['childrens'] = $layout['childrens'];
				}
			}
			return $root;
		}
		return $this->getChildrens( 0, $all );

	}

	public function rebuildTree() {
		$current = 1;
		$this->getChildren(0, $current);
	}

	protected function getChildren($parentId,& $current) {
		$criteria            = new CDbCriteria();
		$criteria->order     = "listOrder ASC";
		$criteria->condition = "parentId=:pid";
		$criteria->params    = array( ":pid" => $parentId );

		$result = $this->findAll( $criteria );
		$index = 0;
		foreach( $result as & $item ) {
			$item->lft = ++$current;
			$this->getChildren( $item->categoryId, $current );
			$item->rft = ++$current;
			$item->listOrder = $index++;
			$item->save();
		}
	}

	/**
	 * 获取新类目的左值与右值
	 * @param integer $parentId
	 * @return array
	 */
	public function getPosition( $parentId ) {
		if( $parentId === 0 ) {
			$sql = "select max(rft) as `rft` from ".$this->tableName();
		}
		else {
			$sql = "select `rft` from ".$this->tableName()." where categoryId=:id";
		}
		$cmd = $this->dbConnection->createCommand( $sql );
		if( $parentId > 0 ) {
			$cmd->bindParam(":id", $parentId);
		}
		$result = (int)$cmd->queryScalar();
		if( $parentId === 0 ) {
			$result++;
		}
		return array(
			'lft' => $result,
			'rft' => $result+1
		);
	}

	/**
	 * 获取listOrder排序最大值
	 * @param integer $parentId
	 */
	public function getMaxOrder( $parentId ) {
		$cmd = $this->getDbConnection()->createCommand("select max(listOrder) as listOrder from ".$this->tableName()." where parentId=:pid");
		$cmd->bindParam(':pid', $parentId );
		$max = $cmd->queryScalar();
		return ++$max;
	}

	/**
	 * 重构数据插入方法
	 * 更新类目左右值
	 * @see CActiveRecord::insert()
	 */
	public function insert($attributes=null) {
		$position        = $this->getPosition( (int)$this->parentId );
		$this->lft       = $position['lft'];
		$this->rft       = $position['rft'];
		$this->listOrder = $this->getMaxOrder( (int)$this->parentId );

		$trans           = $this->getDbConnection()->beginTransaction();

		//修改左值
		$cmd = $this->getDbConnection()->createCommand("update ".$this->tableName()." set `lft`=`lft`+2 where `lft`>:lft");
		$cmd->bindParam(':lft', $this->lft, PDO::PARAM_INT);
		$cmd->execute();

		//修改右值
		$cmd = $this->getDbConnection()->createCommand("update ".$this->tableName()." set `rft`=`rft`+2 where `rft`>=:lft");
		$cmd->bindParam(':lft', $this->lft, PDO::PARAM_INT);
		$cmd->execute();

		$result = parent::insert( $attributes );
		if( $result ) {
			$trans->commit();
			return true;
		}

		$trans->rollback();
		return false;
	}

	/**
	 * 变更类目排序值
	 * @param integer $categoryId 类目ID
	 * @param string $to value: down|up
	 */
	public function changePosition( $categoryId, $to ) {
		$first = $this->findByPk($categoryId);
		if( is_null($first) ) {
			$this->addError('categoryId', 'Not found category');
			return false;
		}

		$second = $this->findByDirection( $first, $to );
		if( is_null($second) ) {
			$msg = $to=="up"? "Is first" : "Is last";
			$this->addError( 'categoryId', $msg );
			return false;
		}

		if( $to == "up" ) {
			$tmp    = $first;
			$first  = $second;
			$second = $tmp;
		}

		$offset = $second->rft+1;
		$space  = $first->rft - $first->lft + 1;

		$trans = $this->getDbConnection()->beginTransaction();
		$cmd   = $this->getDbConnection()->createCommand("update {$this->tableName()} set lft=lft+{$space},rft=rft+{$space} where lft>=:lft");
		$cmd->bindParam(':lft', $offset);
		if( !$cmd->execute() ) {
			$trans->rollback();
			return false;
		}

		$add = $offset - $first->lft;
		$cmd   = $this->getDbConnection()->createCommand("update {$this->tableName()} set lft=lft+{$add},rft=rft+{$add} where lft>=:lft and rft<=:rft");
		$cmd->bindParam(':lft', $first->lft);
		$cmd->bindParam(':rft', $first->rft);
		if( !$cmd->execute() ) {
			$trans->rollback();
			return false;
		}

		$cmd   = $this->getDbConnection()->createCommand("update {$this->tableName()} set lft=lft-{$space},rft=rft-{$space} where lft>:lft");
		$cmd->bindParam(':lft', $first->lft);
		if( !$cmd->execute() ) {
			$trans->rollback();
			return false;
		}
		$trans->commit();
		return true;
	}


	public function findByDirection( $category, $direction ) {
		$cmd = $this->getDbConnection()->createCommand();
		$cmd->setSelect("categoryId,lft,rft");

		if( $direction === "up" ) {
			$sql = "select categoryId,title,lft,rft from {$this->tableName()} where lft<:lft and parentId=:pid order by lft DESC limit 0,1";
		}
		else {
			$sql = "select categoryId,title,lft,rft from {$this->tableName()} where lft>:lft and parentId=:pid order by lft ASC limit 0,1";
		}
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindParam(':lft', $category->lft);
		$cmd->bindParam(':pid', $category->parentId);
		$result = $cmd->queryRow();
		if( $category->categoryId == $result['categoryId'] ) {
			return null;
		}
		return tbCategory::model()->findByPk($result['categoryId']);
	}

	/**
	 * 获取类目及子子孙孙类目树
	 * @param integer $left
	 * @param integer $right
	 * @return array
	 */
	public function findByLeftRight( $left, $right ) {
		$sql = "select categoryId,lft,rft,title from {$this->tableName()} where lft>=:lft and rft<=:rft order by lft ASC";
		$cmd = $this->getDbConnection()->createCommand( $sql );
		$cmd->bindParam( ':lft', $left );
		$cmd->bindParam( ':rft', $right );
		return $cmd->queryAll();
	}

	/**
	 * 获取指定类目的所有子子孙孙的categoryId
	 * @param integer $categoryId
	 * @return array
	 * @user 规格属性继承时需要使用
	 */
	public function getAllLevelChildrens( $categoryId ){
		$category = $this->findByPk( $categoryId );
		$children = array();
		if( $category ) {
			$data = $this->findByLeftRight( $category->lft,$category->rft  );
			foreach ( $data as $val ){
				if( $val['categoryId'] != $categoryId ){
					$children[] = $val['categoryId'];
				}
			}
		}
		return 	$children ;
	}

	/**
	 * 删除类目及其子目录
	 * @param integer $categoryId 类目编号
	 * @return boolean
	 */
	public function remove( $categoryId ) {
		$category = $this->findByPk( $categoryId );
		if( is_null($category) ) {
			$this->addError('categoryId', 'Not found category');
			return false;
		}

		if( $this->beforeRemove( $category ) ) {
			$cmd = $this->getDbConnection()->createCommand("delete from {$this->tableName()} where lft>=:lft and rft<=:rft");
			$cmd->bindParam( ':lft', $category->lft );
			$cmd->bindParam( ':rft', $category->rft );
			$result = $cmd->execute();

			if( $result ) {
				$space = $category->rft - $category->lft + 1;
				$cmd = $this->getDbConnection()->createCommand( "update {$this->tableName()} set lft=lft-{$space},rft=rft-{$space} where rft>=:rft" );
				$cmd->bindParam(':rft', $category->rft);
				$cmd->execute();
			}
			return $result;
		}
	}

	/**
	 * 删除类目及子目录触发器
	 * @param CActiveRecord $category
	 */
	public function beforeRemove( $category ) {
		return true;
	}


	/**
	* 获取当前类目名称及其所有父分类名称
	* @param integer $categoryId 类目编号
	*/
	public function getParentNames( $categoryId ){
		$model = $this->find( array(
		  'select'=>'categoryId,title,lft,rft,parentId',
		  'condition' => 'categoryId = :categoryId',
		  'params' => array( ':categoryId'=>$categoryId ),
		));
		if ( !$model ){
			return ;
		}

		$result = array();
		$model2 = $this->findAll( array(
		  'select'=>'categoryId,title,lft,rft,parentId',
		  'condition' => 'lft < :lft and rft > :rft',
		  'params' => array( ':lft'=>$model->lft,':rft'=>$model->rft ),
		  'order' => 'lft ASC',
		));

		foreach ( $model2  as $val ){
			$result[$val->categoryId] = $val->title;
		}

		$result[$categoryId] = $model->title;
		return $result;
	}

	public function findParent( $categoryId ) {
		$category = $this->findByPk( $categoryId );
		if( $category instanceof CActiveRecord ) {
			$sql = "select categoryId,title from {$this->tableName()} where lft<=:lft and rft>=:rft order by lft asc";
			$cmd = $this->getDbConnection()->createCommand( $sql );
			$cmd->bindValue(':lft', $category->lft, PDO::PARAM_INT);
			$cmd->bindValue(':rft', $category->rft, PDO::PARAM_INT);
			return $cmd->queryAll();
		}
		return null;
	}
}
