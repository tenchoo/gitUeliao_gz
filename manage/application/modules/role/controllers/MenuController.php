<?php
/**
 * 菜单管理
 * User: yagas
 * Date: 2016/3/16
 * Time: 11:36
 */

class MenuController extends Controller {

    public function actionIndex() {
		$id = Yii::app()->request->getQuery('id',0);
		$perSize = tbConfig::model()->get( 'page_size' );
		$criteria = new CDbCriteria;
		$criteria->compare('t.fatherId',$id );
		$criteria->order = "sortNum ASC, id ASC";

		$model = new tbSysmenu();
		$data = $model->findAll( $criteria );
		$list = array_map( function($i){ return $i->attributes;},$data);

		$title = $fatherId = null;

		if( $id>0 ){
			$menu = $model->findByPk( $id );
			if( $menu ){
				$fatherId = $menu->fatherId;

				if( $menu->type == 'action' ){
				throw new CHttpException( '404', 'Not found record' );
				}
			}
			$title = $this->getParentsTitle( $id );
		}
        $this->render('index',array('list'=>$list,'title'=>$title,'fatherId'=>$fatherId,'id'=>$id));
    }

	/**
	 * 编辑菜单
	 * @access 编辑菜单
	 * @throws CHttpException
	 */
	public function actionEdit() {
		$id = Yii::app()->request->getQuery('id');
		if( is_null($id) || !is_numeric($id) || ($model = tbSysmenu::model()->findByPk( $id ))==null ) {
			throw new CHttpException( '404', 'Not found record' );
		}
		$this->saveData( $model );
	}

	/**
	* 新增菜单
	* @access 新增菜单
	* @throws CHttpException
	*/
	public function actionAdd(){
		$model = new tbSysmenu();
		$model->hidden = 0;
		$model->sortNum = 0;

		//所属父级
		$fatherId = Yii::app()->request->getQuery('fatherId');
		if( !empty( $fatherId ) && $fatherId >0 ){
			$parentmenu = tbSysmenu::model()->findByPk( $fatherId );
			if( !$parentmenu ){
				throw new CHttpException( '404', 'Not found record' );
			}

			if( $parentmenu->type == 'action' ){
				throw new CHttpException( '403', 'Not allow add submenu for action ' );
			}

			$model->fatherId = $fatherId;

			if( $parentmenu->type == tbSysmenu::TYPE_NAVIGATE ){
				$model->type = tbSysmenu::TYPE_GROUP;
			}else if( $parentmenu->type == tbSysmenu::TYPE_GROUP ){
				$model->type = tbSysmenu::TYPE_MENU;
			}else{
				$model->type = tbSysmenu::TYPE_ACTION;
			}
		}else{
			$model->type = tbSysmenu::TYPE_NAVIGATE;
			$model->fatherId = '000000000000';
		}

		$this->saveData( $model );
	}

	/**
	 * 删除菜单
	 * @access 删除菜单
	 */
	public function actionDel() {
		$id = Yii::app()->request->getQuery('id');
		if( is_numeric($id) && $id >1 ){
			//先判断是否包含子菜单，若包含子菜单，不给删除
			$model  = new tbSysmenu();
			$exists = $model->exists('fatherId = :fatherId',array(':fatherId'=>$id ));
			if( $exists  ){
				$json=new AjaxData(false,'有子菜单，不允许删除！');
				echo $json->toJson();
				exit;
			}else{
				$a = tbSysmenu::model()->deleteByPk( $id );
				//关联删除persission中的关联ID
				$a = tbPermission::model()->deleteAllByAttributes( array('menuId'=>$id ) );
			}
		}
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}

	private function getParentsTitle( $id ){
		$menuInfo = str_split($id, 3);
        $ids = [];
        $base = '';
        foreach($menuInfo as $item) {
            $base .= array_shift($menuInfo);
            $id = str_pad($base, 12, '0');
            if(!array_search($id, $ids)) {
                array_push($ids, $id);
            }
        }

        $menus = tbSysmenu::model()->findAllByPk($ids);
		$title = array();
        foreach($menus as $menu) {
           $title[] = $menu->title;
        }
        return $title;
	}

	/**
	* 保存菜单数据
	*/
	private function saveData( $model ){
		$title = $this->getParentsTitle( $model->fatherId );
		if( Yii::app()->request->isPostRequest ) {
			$data = Yii::app()->request->getPost('form');
			$model->attributes = $data;
			$model->route = trim( $model->route );
			if( $model->save() ) {
				$this->dealSuccess( $this->createUrl('index',array( 'id'=>$model->fatherId ) ));
			}else{
				$this->dealError( $model->getErrors() );
			}
		}
		$this->render( 'edit', array( 'data'=>$model->attributes ,'title'=>$title ) );
	}
}