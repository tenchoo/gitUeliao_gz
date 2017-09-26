<?php
/**
 * 产品属性内容编辑
 * @author liang
 * @version 0.1
 * @package CWidget
 */
class widgetProductAttr extends CWidget {
	
	public $categoryId;
	
	
	/**
	 * 渲染物件
	 * @see CWidget::run()
	 */
	public function run() {
		if( !$this->categoryId ){
			exit;
		} 
		$attrlist = tbAttribute::getAttributeLists(  $this->categoryId ) ;
		if ( empty ( $attrlist ) ) {
			exit ;
		}
		$code ='';
		foreach ( $attrlist as $attrval) {
			$name = 'product[productAttrs]['.$attrval['attributeId'].'][attributeValue]';
			$valuearr = explode( ',' , $attrval['attributeValue'] ) ;
			$code .= '<br/>'.$attrval['attributeName'].':';
			switch( $attrval['attributeType'] ) {
				case '1': //单选框
					foreach( $valuearr as $vvar){
						$code .= '<input type="radio" name="'.$name.'" value="'.$vvar.'"/>'.$vvar;
					}
					break;
				case '2': //复选框
					foreach( $valuearr as $vvar){
						$code .= '<input type="checkbox" name="'.$name.'[]" value="'.$vvar.'"/>'.$vvar;
					}
					break;
				case '3': //下拉框
					$code .= '<select name="'.$name.'">';
					foreach( $valuearr as $vvar){
						$code .= '<option value="'.$vvar.'"/>'.$vvar.'</option>';
					}
					$code .= '</select>';
					break;
				case '4':
					$code .= '<input type="text" name="'.$name.'" value=""/>';
					break;
				case '5':
					$code .= '<textarea name="'.$name.'"></textarea>';
					break;
			}
		}
		echo $code;
	}
}