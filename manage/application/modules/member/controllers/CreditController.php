<?php
/**
 * 客户信用额度模块和还款管理
 * @access 客户信用额度管理
 * @author liang
 * @package Controller
 */
class CreditController extends Controller {


	/**
	 * 显示月结客户列表
	 * @access 月结客户列表
	 */
	public function actionIndex() {
		$keyword = Yii::app()->request->getQuery('keyword');
		$data = Member::creditMemberList( $keyword );
		$data['keyword'] = $keyword;
		$this->render( 'index',$data );
	}

	/**
	 * 客户还款信息上传
	 * @access 还款
	 */
	public function actionRepayment() {
		$memberId = Yii::app()->request->getQuery('memberId');
		if( !is_numeric( $memberId ) || !$model = tbMemberCredit::model()->findByPk ( $memberId ) ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		if( Yii::app()->request->isPostRequest ){
			$amount = Yii::app()->request->getPost('amount');

			$creditDetail = new tbMemberCreditDetail();
			$creditDetail->memberId =  $memberId;
			$creditDetail->repayment( $amount );

			if( $creditDetail->save() ) {
				$from = Yii::app()->request->getQuery('from');
				if( $from ){
					$url = urldecode($from);
				}else{
					$url = $this->createUrl( 'index' );
				}
				$this->dealSuccess( $url );
			} else {
				$errors = $creditDetail->getErrors();
				$this->dealError( $errors );
			}
		}else{
			$amount = '';
		}

		$usedCredit = tbMemberCreditDetail::usedCredit( $memberId ,'');
		$validCredit = ($model->state == '0')? bcsub ( $model->credit, $usedCredit ):'0';

		$companyName = $this->getCompanyname( $memberId );
		$this->render( 'repayment',array('amount'=>$amount,'model'=>$model,'usedCredit'=>$usedCredit,'validCredit'=>$validCredit,'companyName'=>$companyName) );
	}

	private function getCompanyname( $memberId ){
		$pd = tbProfileDetail::model()->findByPk( $memberId );
		return ( $pd )?$pd->companyname:'';
	}


	/**
	 * 显示月结客户账单
	 * @access 月结客户账单
	 */
	public function actionBill() {
		$memberId = Yii::app()->request->getQuery('memberId');
		$y = Yii::app()->request->getQuery('y');
		$m = Yii::app()->request->getQuery('m');

		if( !is_numeric( $memberId ) || !$model = tbMemberCredit::model()->findByPk ( $memberId ) ){
			throw new CHttpException(404,"the require obj has not exists.");
		}

		//客户加入月结年份 月份
		$endYear = date('Y');
		$beginYear = date('Y',strtotime($model->createTime));

		$endMonth = date('m');
		$beginMonth = date('m',strtotime($model->createTime));
		$count = 0;

		$bill = null;
		$detail = array();
		if( $y && $m ){
			if( $y<$beginYear || $y >$endYear || $m<1 || $m>12 || ( $y == $beginYear && $m<$beginMonth ) || ( $y == $endYear && $m>$endMonth ) ){
				goto end;
			}

			$t = date('Y-m-d H:i:s', mktime(0,0,0,$m,1,$y));
			$t1 = date('Y-m-d H:i:s', mktime(0,0,0,$m+1,1,$y));

			$bill = tbMemberBill::model()->find ( array(
					'condition'=>'t.memberId = :memberId and t.createTime>:t and t.createTime< :t1',
					'params'=>array(':memberId'=>$memberId,':t'=>$t,':t1'=>$t1),
					'order'=>'t.createTime ASC',
				) );
			if( $bill  ){
				$detail = $bill->detail;
			}
		}else{
			//未生成账单记录
			$detail = tbMemberCreditDetail::model()->findAll ( array(
					'condition'=>'t.memberId = :memberId and t.state=:t',
					'params'=>array(':memberId'=>$memberId,':t'=>'0'),
					'order'=>'t.createTime ASC',
				) );
		}

		$detail = array_map( function ( $i ){
						$i->amount = - $i->amount;
						if($i->amount>0){
							$i->amount = '+'.$i->amount;
						}
						return $i->attributes;
					},$detail );
		$count = array_sum ( array_map( function ( $i ){ return $i['amount'];}, $detail) );

		end:
		$member = $model->attributes;
		$member['companyName'] = $this->getCompanyname( $memberId );

		$this->render( 'bill',array( 'bill'=>$bill,'endYear'=>$endYear,'beginYear'=>$beginYear ,'member'=>$member,'y'=>$y,'m'=>$m ,'detail'=>$detail,'count'=>$count) );
	}

}