<?php
/**
 * 碎片模板物件--取得碎片信息，并输出碎片内容。
 * @author liang
 * @version 0.1
 * @package CBasePager
 * @example
 *
 */
class PieceWidget extends CWidget {

	/**
	 * 碎片Id
	 * @var int
	 */
	public $id;

	/**
	 * 碎片标识
	 * @var string
	 */
	public $mark;

	public function run() {
		$piece = $this->getPiece();
		echo $piece;
	}

	/**
	* 取得碎片信息
	*/
	private function getPiece(){
		$model = null;
		if( !empty($this->id) && is_numeric($this->id) ){
			$model = tbPiece::model()->findByPk( $this->id ,'t.state=0 and t.parentId > 0');
		}else if( !empty($this->mark) && preg_match('/^[a-zA-Z_]+$/',$this->mark)) {
			$model = tbPiece::model()->findByAttributes( array('mark'=> $this->mark ) ,'t.state=0 and t.parentId > 0');
		}
		if( $model ){
			return $model->content;
		}
	}
}