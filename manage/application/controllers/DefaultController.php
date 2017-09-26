<?php

/**
 * 管理中心默认用户欢迎页面
 * @author yagas
 * @version 0.1
 * @package Controller
 */
class DefaultController extends Controller
{
    public $layout = '//layouts/framework';

    //欢迎页面无需检察权限
    public function beforeAction($action) {
        return true;
    }

    //框架主体
    public function actionIndex()
    {
        $this->layout = false;
        $this->render('framework');
    }

    /**
     * 框架页左侧菜单栏
     * 超级管理员菜单分组显示
     * 非超级管理员菜单合并在左边栏显示
     */
    public function actionLeftbar()
    {
        $id = Yii::app()->request->getQuery('id');
        $dataList = array();

        //超级管理员菜单处理流程
        if(Yii::app()->user->getState('isAdmin')==1) {
        	if(!$id) {
        		$navigate = tbSysmenu::model()->find('type=:t and hidden=:h', [':t'=>tbSysmenu::TYPE_NAVIGATE, ':h'=>0]);
        		$id = $navigate->id;
        	}
        	$dataList = tbSysmenu::model()->findAllChildrens($id,tbSysmenu::TYPE_GROUP);
        	goto render_view;
        }

        //非超级管理员菜单处理流程
        $ids = tbSysmenu::model()->fetchMyNavigate();
        $navigates = tbSysmenu::model()->findAllByPk($ids);
        foreach($navigates as $item) {
        	$childrens = $item->findAllChildrens($item->id, tbSysmenu::TYPE_GROUP);

        	foreach($childrens as $groups) {
            	array_push($dataList, $groups);
            }
        }

        //渲染菜单栏
        render_view:
        	$this->render('leftbar', ['groups' => $dataList]);
    }

    /**
     * 框架页顶部导航栏
     */
    public function actionHeadbar()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'hidden=0 and type=:t';
        $criteria->params = [':t'=>'navigate'];
        $criteria->order = 'sortNum ASC, id ASC';

        /**
         * 非管理员首先要获取有权限访问的导航栏菜单ID */
        if( (int)Yii::app()->user->getState('isAdmin') === 0 ) {
            $ids = tbSysmenu::model()->fetchMyNavigate();
            $criteria->addInCondition('id',$ids);
        }

        $navigate = tbSysmenu::model()->findAll($criteria);

        $this->render('headbar', ['navigate' => $navigate]);
    }

    /**
     * 框架页工作区
     * 用于展示列表及相关操作
     */
    public function actionRightbar() {
		$group = array('order'=>'订单管理','customer'=>'客户管理','purchase'=>'采购管理',
				'warehouse'=>'仓库管理','inquiry'=>'网站询盘','factory'=>'工厂管理后台');

		$isAdmin = Yii::app()->user->getState( "isAdmin" );
        $result = $menus = array();
        if( $isAdmin == '1' ){
			$sql = "SELECT s.* FROM {{sysmenu_count}} s order by id asc";
		}else{
			$userRoles = Yii::app()->user->getState( "roles" );
            if(!$userRoles) {
              goto showpage;
            } else {
                $roleid = implode(',', $userRoles);
				$sql = "SELECT s.* FROM {{sysmenu_count}} s WHERE EXISTS
					( SELECT NULL FROM {{permission}} p WHERE s.`id` = p.`menuId` and p.`roleId` in( $roleid ) )
					order by id asc";
            }

		}
        $cmd = Yii::app()->db->createCommand( $sql );
        $result = $cmd->queryAll();


		if( is_array($result) ){
			$workCount = new WorkCount();
			foreach ( $result as $val ){
				//如果isCount =1 ，从方法中实时算出值。若为0，直接读取数据中保存的值。
				if( $val['isCount'] == '1' && method_exists( $workCount,$val['funcName'] ) ){
					$val['totalNum'] = call_user_func(array('WorkCount', $val['funcName']));

				}
				if( $val['totalNum'] > '1' ){
					$menus[$val['groupName']][] = $val;
				}
			}
		}

		showpage:
        $this->render('welcome',array('menus'=>$menus,'group'=>$group));
    }

    /**
     * 仪表盘
     */
    public function actionDashboard() {
        $this->render('dashboard');
    }

    public function actionChangepassword() {
		if( Yii::app()->request->getIsPostRequest() ) {
			$model = new MemberHelper();
			$model->attributes = Yii::app()->request->getPost( 'form' );
			if( $model->changePassword() ) {
				$this->dealSuccess( $this->createUrl('changepassword') );
			}else {
				$this->dealError( $model->getErrors() );
			}
		}
    	$this->render('changepassword');
    }

    public function actionPrinter() {
		$profile = tbUser::model()->findByPk(Yii::app()->user->id);
		$printers    = tbPrinter::model()->getAll();

		if(Yii::app()->request->getIsPostRequest()) {
			$printerId = Yii::app()->request->getPost('printerId');
			if( array_key_exists( $printerId, $printers )  ){
				$profile->printerId = $printerId;
				if( $profile->save() ) {
					$this->dealSuccess( $this->createUrl('printer') );
				}else{
					$this->dealError( $profile->getErrors() );
				}
			}
		}

        $this->render('printer', array('profile'=>$profile, 'printers'=>$printers));
    }
}
