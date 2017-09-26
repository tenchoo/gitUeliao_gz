<?php
/**
 * 数据表视图，自动合并相同数据行
 */
class MagicTableRow {
	private $_fileds     = array();
	private $_filedCount = 0;
	private $_rowCount   = 0;
	private $_rowspan    = true;

	public function __construct() {
		$this->_filedCount = func_num_args();
		if( $this->_filedCount == 0 ) {
			throw new CHttpException( 500, 'filed is nothing' );
		}

		foreach( func_get_args() as $single ) {
			array_push( $this->_fileds, array('title'=>$single,'rows'=>array(), 'nomerge'=>false) );
		}
	}

	public function rowspan( $bool ) {
		$this->_rowspan = $bool;
	}
	
	/**
	 * 设置不进行合并的列
	 * @param string $title
	 */
	public function filterMerge($title) {
		foreach($this->_fileds as & $item) {
			if($item['title'] == $title) {
				$item['nomerge'] = true;
				break;
			}
		}
	}

	public function appendRow() {
		if( func_num_args() !== $this->_filedCount ) {
			throw new CHttpException( 500, 'The number of fields do not match' );
		}

		foreach( func_get_args() as $index=>$item ) {
			array_push( $this->_fileds[$index]['rows'], array('value'=>$item,'rowspan'=>0) );
		}
		++$this->_rowCount;
	}

	protected function checkRows() {
		foreach( $this->_fileds as $fi=>&$field ) {
			if($field['nomerge']) {
				continue;
			}
			
			$base = array('value'=>null);
			$header = 0;
			
			foreach( $field['rows'] as $ri=>& $row ) {
				if( empty($row['value']) || strcmp($row['value'],$base['value']) !== 0 ) {
					$base = $row;
					$header = $ri;
					continue;
				}
				else {
					$row['value'] = null;
					$field['rows'][$header]['rowspan'] += 1;
				}
			}
		}
	}

	public function show() {
		if( $this->_rowspan ) {
			$this->checkRows();
		}

		for( $i=0; $i<$this->_rowCount; $i++ ) {
			echo '<tr>';
			for( $f=0; $f<$this->_filedCount; $f++ ) {

				extract($this->_fileds[$f]['rows'][$i]);
				if( is_null($value) ) {
					continue;
				}

				$optional = array();
				if( $rowspan>0 ) {
					$optional['rowspan'] = ++$rowspan;
					$optional['style'] = "vertical-align:middle";
				}
				echo CHtml::tag('td',$optional,$value);
			}
			echo '</tr>';
		}
	}
}