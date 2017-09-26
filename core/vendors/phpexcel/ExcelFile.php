<?php

/**
 * Simple excel generating from PHP5
 * 生成excel保存到指定目录
 *
 * @author liang
 * @version 1.0
 */


class ExcelFile {

	/**
	* 是否保存到服务器，否的话直接输出
	*/
	private $_isSave = false;

	/**
	* 文件名
	*/
	private $_fileName;
	/**
	* 保存文件夹的名称
	*/
	private $_saveDir;

	/**
	* 保存文件夹的全路径
	*/
	public $dirRoot;

	/**
	* 数据行数，已写到第几行，每次写完+1
	*/
	public $rows = 1;

	public $_objectPHPExcel;

	private $_sheetIndex = 0;


	public function __construct( $fileName,$saveDir='',$isSave = false ){
		$fileName = iconv("UTF-8","GB2312//IGNORE",$fileName);
		$this->_fileName = $fileName.'.xls';
		$this->_isSave = $isSave;
		$this->_saveDir = $saveDir;

		if( empty( $this->dirRoot ) ){
			$this->dirRoot = Yii::app()->getRuntimePath();
		}

		ob_end_clean();
		ob_start();
		$objectPHPExcel = new PHPExcel();
		$objectPHPExcel->setActiveSheetIndex(0);
		$this->_objectPHPExcel = $objectPHPExcel;
	}

	/**
	* 增加工作表
	* @param  string $sheetName 新增的工作表的名字
	* @param  array $data	 新增的工作表保存的数据
	*/
	public function addSheet( $sheetName = null ,array $data ){
		$this->_objectPHPExcel->createSheet();
		$this->_sheetIndex++;
		if( !empty( $sheetName )){
			$this->_objectPHPExcel->setActiveSheetIndex( $this->_sheetIndex )->setTitle( $sheetName,false );
		}

		$this->rows = 1;
		$this->setData( $data );
	}

	/**
	* 设置工作表名字，不设置时默认为Worksheet
	* @param string $sheetName
	*/
	public function setSheetName( $sheetName ){
		if( empty( $sheetName )) return ;
		$this->_objectPHPExcel->setActiveSheetIndex(0)->setTitle( $sheetName,false );
	}

	/**
	* 设置工作表单元格的宽度，默认读取当前active的sheet
	* @param string $col 要设置的列,用大写字母A B C D 
	* @param integer $width 单元格宽度
	*/
	public function setWidth( $col,$width ){
		$this->_objectPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth($width);
	}




	/**
	* 生成excel，数据带合并格式，其他数据中指定 key为 mergeDetails 为合并的标识
	* @param array $data
	*/
	public function createMergeExl( array $data ){
		if( empty( $this->_fileName )){
			echo "excel 文件名不能为空！<br />";
			exit;
		}

		$this->setData( $data );
		$this->outFile();
	}

	/*
$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');      //合并
$objPHPExcel->getActiveSheet()->unmergeCells('A1:F1');    // 拆分
//设置单元格样式
$this->_objectPHPExcel->setActiveSheetIndex(0)->getStyle( $k.$this->rows )->applyFromArray(
											array( 'font' => array ( 'bold' => true ),
													'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
																		'vertical'=> PHPExcel_Style_Alignment::VERTICAL_CENTER ) ) );
 $styleArray = array(
			'borders' => array(
				'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, //细边框
                //'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的

                //'color' => array('argb' => 'FFFF0000'),
				),
			),
		); */
	public function setData( array $data ){
		foreach ( $data as $key=>$val ){
			$bgcolor = $color = '';
			$mergeRows = 0;
			if( array_key_exists( 'mergeDetails', $val ) && is_array( $val['mergeDetails'] ) ){
				$mergeRows = count( $val['mergeDetails'] ) ;

				if ( ( $key & 1 ) != 0 ){
					$bgcolor = 'FFD3D3D3';
				}
			}

			$isTitle = false;
			if( array_key_exists( 'isTitle', $val ) ){
				$isTitle = true;
				$color = '6E8B3D';
				unset(  $val['isTitle'] );
			}

			$val = array_values($val);
			$c = count( $val );

			for( $i = 0; $i<$c;$i++ ){
				$k = PHPExcel_Cell::stringFromColumnIndex($i);
				if( is_array( $val[$i] ) ){
					$t = $k.$this->rows;
					$val[$i] = array_values($val[$i]);
					foreach ( $val[$i] as $kkey=>$_vval ){
						if( $kkey>0 ){
							$this->rows++;
						}
						$_vval = array_values($_vval);
						$c1 = count( $_vval  );
						for( $n = 0; $n<$c1;$n++ ){
							$k = PHPExcel_Cell::stringFromColumnIndex( $i+$n );
							$this->_objectPHPExcel->setActiveSheetIndex( $this->_sheetIndex )->setCellValue($k.$this->rows,$_vval[$n]);

							$cell = $this->_objectPHPExcel->setActiveSheetIndex( $this->_sheetIndex )->getStyle( $k.$this->rows );
							if( !empty($bgcolor) ){
								$cell->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
								$cell->getFill()->getStartColor()->setARGB( $bgcolor );
							}

							if( !empty($color) ){
								$cell->applyFromArray(
											array( 'font' => array ( 'color'=>array('argb' => $color ) ) ) );
							}
						}
					}
				}else{
					$this->_objectPHPExcel->setActiveSheetIndex( $this->_sheetIndex )->setCellValue($k.$this->rows,$val[$i]);

					if( $mergeRows>0 ){
						$r = $this->rows + $mergeRows - 1;
						$this->_objectPHPExcel->setActiveSheetIndex( $this->_sheetIndex )->mergeCells( $k.$this->rows.':'.$k.$r );

						$cell = $this->_objectPHPExcel->setActiveSheetIndex( $this->_sheetIndex )->getStyle( $k.$this->rows );
						if( !empty($bgcolor) ){
							$cell->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
							$cell->getFill()->getStartColor()->setARGB( $bgcolor );
						}
						$cell->applyFromArray(	array( 'font' => array ( 'color'=>array('argb' => $color ) ),'alignment' => array( 'vertical'=> PHPExcel_Style_Alignment::VERTICAL_CENTER ) ) );
					}else{
						if( $isTitle ){
							$this->_objectPHPExcel->setActiveSheetIndex( $this->_sheetIndex )->getStyle( $k.$this->rows )->applyFromArray(
											array( 'font' => array ( 'bold' => true, 'color'=>array('argb' => $color ), ),
													'alignment' => array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
																		'vertical'=> PHPExcel_Style_Alignment::VERTICAL_CENTER ) ) );
						}
					}
				}
			}
			$this->rows++;
		}
	}

	/**
	* 直接生成excel
	* @param array $data
	*/
 	public function createExl( array $data ){
		if( empty( $this->_fileName )){
			echo "excel 文件名不能为空！<br />";
			exit;
		}

		foreach ( $data as $val ){
			$val = array_values($val);
			$c = count( $val );
			for( $i = 0; $i<$c;$i++ ){
				$k = PHPExcel_Cell::stringFromColumnIndex($i);
				$this->_objectPHPExcel->setActiveSheetIndex(0)->setCellValue($k.$this->rows,$val[$i]);
			}

			$this->rows++;
		}

		$this->outFile();
	}

	public function outFile(){
		$objWriter = PHPExcel_IOFactory::createWriter( $this->_objectPHPExcel, 'Excel5');

		if( $this->_isSave ){
			$this->getSaveDir( );
			$saveFile = $this->_saveDir.'/'.$this->_fileName;
			$objWriter->save( $saveFile );
		}else{
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $this->_fileName . '"');
			header('Cache-Control: max-age=0');
			$objWriter->save('php://output');
		}
	}

	public function getSaveDir(){
		if( $this->_saveDir ){
			$this->_saveDir = $this->dirRoot.'/'.$this->_saveDir;
			if(!is_dir( $this->_saveDir )){
				if( !mkdir( $this->_saveDir ,0777,true ) ){
					echo "找不到 $dir 文件夹，且创建失败！<br />";
					exit;
				}
			}
		}else{
			$this->_saveDir = $this->dirRoot;
		}

		return $this->_saveDir;
	}
}
?>