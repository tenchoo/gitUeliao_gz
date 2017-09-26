<?php
/**
 * 客户管理
 * @author liang
 * @package CFormModel
 */
class Member extends CFormModel {

	/**
	 * 查找客户列表
	 * @param  array $condition 查找条件
	 * @param  string $order	排序
	 * @param  integer $pageSize 每页显示条数
	 */
	public static function search( $condition = array() ,$pageSize=10 ) {
		$criteria=new CDbCriteria;
		if(!empty($condition)){
			foreach ($condition as $key=>$val){
				if( $val == '' ){
					continue ;
				}else if( $key == 'tel' ){
					$criteria->compare('t.phone',$val,true);
				}else if( $key == 'corp' ){
					$criteria->compare('pd.companyname',$val,true);
				}else{
					$criteria->compare('t.'.$key,$val);
				}
			}
		}
		$criteria -> select = 't.memberId, t.phone,t.isCheck,pd.companyname as nickName';//t.state,
		$criteria->compare('t.state',array('Normal','Disabled'));
		$criteria->join = "left join {{profile_detail}} pd on pd.memberId = t.memberId";

		$criteria->order = 't.register DESC'; //默认为时间倒序
		$model = new CActiveDataProvider('tbMember', array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>$pageSize),
		));

		$data = $model->getData();
		$result['list'] = array();
		$result['pages'] = $model->getPagination();
		$checkTitle = array('0'=>'待审核','1'=>'已审核','2'=>'审核不通过');
		if( $data ){
			foreach ( $data as $key => $val ){
				$result['list'][$key]['memberId'] = $val->memberId;
				$result['list'][$key]['phone'] = $val->phone;
				$result['list'][$key]['isCheck'] = $val->isCheck;
				$result['list'][$key]['companyname'] = $val->nickName;
				$result['list'][$key]['checkTitle'] = $checkTitle[$val->isCheck];
			}
		}
		return $result;
	}


}
