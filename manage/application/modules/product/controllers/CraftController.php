<?php
/*
* @access 产品工艺
*/
class CraftController extends Controller {

	/**
	* @access 工艺列表
	*/
	public function actionIndex() {
		$craftCode = trim(Yii::app()->request->getQuery('craftCode'));

		$tbCraft = new tbCraft();
		$models = $list = array();
		if( $craftCode ){
			$tmodel = $tbCraft->find( 'craftCode = :craftCode',array( ':craftCode'=>$craftCode ) );

			if( !$tmodel ){
				goto end;
			}

			if ( $tmodel->parentCode == '' ){
				if( $tmodel->hasLevel ){
					$models = $tbCraft->findAll( array(
								'condition'=>'parentCode = :parentCode',
								'params'=>array( ':parentCode'=> $tmodel->craftCode ),
								'order'=>'craftCode ASC'
							));
				}
				array_unshift($models,$tmodel);
			}else{
				$pmodel = $tbCraft->find( 'craftCode = :craftCode',array( ':craftCode'=>$tmodel->parentCode ) );
				if( $pmodel ){
					array_push($models,$pmodel);
				}

				array_push($models,$tmodel);
			}
		}else{
			$models = $tbCraft->findAll( array(
						'order'=>'parentCode ASC,craftCode ASC'
					));
		}

		foreach ( $models as $val ){
			if ( !empty( $val->parentCode ) && isset( $list[$val->parentCode] ) ) {
				$list[$val->parentCode]['childs'][] = $val->attributes;
			} else {
				$list[$val->craftCode] = $val->attributes;
			}
		}

		end:

		$this->render( 'index',array( 'list'=>$list,'craftCode'=>$craftCode ) );
	}

	/**
	* @access 新增工艺
	*/
	public function actionAdd(){
		$model = new tbCraft();
		$parentCode = '';
		$pid = trim(Yii::app()->request->getQuery('pid'));
		if( $pid ){
			$pmodel = tbCraft::model()->findByPk( $pid );
			if ( !$pmodel ) {
				$this->redirect($this->createUrl( 'index' ));
			}

			$model->parentCode = $pmodel->craftCode;
			$model->hasLevel = 0;
			$parentCode = $pmodel->craftCode.'  '.$pmodel->title;
		}
		$this->addEdit( $model,$parentCode );
	}


	/**
	* @access 编辑工艺
	*/
	public function actionEdit( $id ){
		$model = tbCraft::model()->findByPk( $id );
		if ( !$model ) {
			throw new CHttpException(404,"the require Craft has not exists.");
		}
		$parentCode = '';
		if( $model->parentCode ){
			$pmodel = tbCraft::model()->find( 'craftCode = :craftCode',array( ':craftCode'=>$model->parentCode ) );
			if ( $pmodel ) {
				$parentCode = $pmodel->craftCode.'  '.$pmodel->title;
			}else{
				$model->parentCode = '';
			}
		}
		$this->addEdit( $model,$parentCode );
	}

	/**
	* @access 保存工艺信息
	* 编辑时：如果当前分类已有子分类，则是否有子分类的值不能修改为0
	* 已有子分类时，编号更改要同步更新子分类的上级编号
	*/
	private function addEdit( $model,$parentCode ){
		$hasLevel = false;

		if( $parentCode == '' && !$model->isNewRecord ){
			$hasLevel = $model->exists('parentCode = :p',array(':p'=>$model->craftCode));

			if( $hasLevel ){
				$model->hasLevel =  1;
			}
		}

		if( Yii::app()->request->isPostRequest ) {
			//原编号
			$_oldCode = $model->craftCode;

			$model->craftCode = strtoupper ( trim ( Yii::app()->request->getPost('craftCode') ) );;
			$model->title =  trim ( Yii::app()->request->getPost('title') );
			$model->icon =  trim ( Yii::app()->request->getPost('icon') );

			if( $parentCode == '' && !$hasLevel ){
				$model->hasLevel =  Yii::app()->request->getPost('hasLevel');
			}
			if( $model->save() ){
				// 已有子分类时，编号更改要同步更新子分类的上级编号
				if( $hasLevel && $_oldCode != $model->craftCode ){
					$model->updateAll( array('parentCode'=>$model->craftCode),'parentCode = :code',array( ':code'=>$_oldCode ) );
				}
				$url = $this->createUrl( 'index' );
				$this->dealSuccess($url);
			}else{
				$errors = $model->getErrors();
				$this->dealError( $errors );
			}
		}

		$this->render( 'add',array( 'data'=>$model->attributes,'parentCode'=>$parentCode,'hasLevel'=>$hasLevel ) );
	}


	/**
	* @access 删除工艺
	* 有子分类不允许删除
	*/
	public function actionDel( $id ){
		tbCraft::model()->del( $id );
		$this->dealSuccess( Yii::app()->request->urlReferrer );
	}
}