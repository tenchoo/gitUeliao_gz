<?php
class PostExpressInfo extends CAction {
	
	public function run() {
		$com =Yii::app()->request->getQuery('name'); // 快递公司
		$nu =Yii::app()->request->getQuery('number'); // 快递单号
		
		$param['id'] = '106617';
		$param['secret'] = '83d07350a8ebd29c9a16af46a844bccf';		//该参数为新增加，老用户可以使用申请时填写的邮箱和接收到的KEY值登录
		$param['com'] = $com;					//要查询的快递公司代码
		$param['nu'] = $nu;					//要查询的快递单号
		$param['lang'] = '';					//en返回英文结果，目前仅支持部分快递（EMS、顺丰、DHL）
		$param['type'] = 'json';						//返回结果类型，值分别为 html | json（默认） | text | xml
		$param['encode'] = 'utf8';						//gbk（默认）| utf8
		$param['order'] = 'desc';				//排序： desc：按时间由新到旧排列， asc：按时间由旧到新排列。 不填默认返回倒序（大小写不敏感）
		
		$url="http://api.ickd.cn/?id={$param['id']}&secret={$param['secret']}&com={$param['com']}&nu={$param['nu']}&type={$param['type']}&encode={$param['encode']}&ord={$param['order']}&ver=2";
		
		
		//建立缓存
		$expcache = 'exp'.$com.$nu;
		if($json = Yii::app()->cache->get($expcache)){
			$exp =  $json;
		}else{
			$str=file_get_contents($url);
			$exp = json_decode($str,true);
			//设置缓存
			Yii::app()->cache->set($expcache,$exp,3600*24);
		}
		
		if($exp['status']==0){
			$json = new AjaxData( false );
			$json ->setMessage( 'glo', $value='Query interface error! Please try again later' );
		}else{
			$json = new AjaxData( true, null, $exp['data'] );
		}
		echo $json->toJson();
		Yii::app()->end();
	}
}