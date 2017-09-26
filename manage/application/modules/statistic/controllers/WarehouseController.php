<?php

/**
 * 创建报表
 * User: yagas
 * Date: 2016-08-04
 * Time: 13:18
 */
class WarehouseController extends Controller
{

    /**
     * 进出存总表
     */
    public function actionIndex()
    {
        $st = Yii::app()->request->getQuery('start');
        $et = Yii::app()->request->getQuery('end');

        if(!$st && !$et) {
            $criteria = new warehouseCriteria(warehouseCriteria::TYPE_NOLIST, $st, $et);
        }
        else {
            $criteria = new warehouseCriteria(warehouseCriteria::TYPE_STATISTIC, $st, $et);
        }

        return $this->findAll($criteria, 'statistic');
    }

	/**
	* 仓库入库统计报表
	*/
	public function actionInput()
    {
        $st = Yii::app()->request->getQuery('start');
        $et = Yii::app()->request->getQuery('end');

        if(!$st && !$et) {
            $criteria = new warehouseCriteria(warehouseCriteria::TYPE_NOLIST, $st, $et);
        }
        else {
            $criteria = new warehouseCriteria(warehouseCriteria::TYPE_INPUT, $st, $et);
        }
        return $this->findAll($criteria, 'input');

    }

	/**
	* 仓库出库统计报表
	*/
	public function actionOutput()
    {
        $st = Yii::app()->request->getQuery('start');
        $et = Yii::app()->request->getQuery('end');

        if(!$st && !$et) {
            $criteria = new warehouseCriteria(warehouseCriteria::TYPE_NOLIST, $st, $et);
        }
        else {
            $criteria = new warehouseCriteria(warehouseCriteria::TYPE_OUTPUT, $st, $et);
        }
        return $this->findAll($criteria, 'output');
    }


	/**
	* 转换码和米
	* 1米(m)=1.0936133码(yd)
	* @param $num 换算的值
	*/
	public function ydTOmeter( $num ){
		return bcMul( $num,0.9144,2 );
	}

	/**
	* 转换米和码 --
	* 1米(m)=1.0936133码(yd)
	* @param $num 换算的值
	*/
	public function meterTOyd( $num ){
		return bcMul( $num,1.0936133,2 );
	}

    protected function findAll(warehouseCriteria $criteria, $view)
    {
        $end = strtotime($criteria->DateEnd);
        $end = strtotime('+1 day', $end);
        $end = date('Y-m-d', $end);
        switch ($criteria->type) {
            //仓库统计报表
            case warehouseCriteria::TYPE_STATISTIC:
                $cmd = Yii::app()->db->createCommand("CALL `warehouse_cash_on_hand`('{$criteria->DateStart}','{$end}')");
                break;

            //入库明细报表
            case warehouseCriteria::TYPE_INPUT:
                $cmd = Yii::app()->db->createCommand("CALL `warehouse_cash_on_input`('{$criteria->DateStart}','{$end}')");
                break;

            //出库明细报表
            case warehouseCriteria::TYPE_OUTPUT:
                $cmd = Yii::app()->db->createCommand("CALL `warehouse_case_on_output`('{$criteria->DateStart}','{$end}')");
                break;

            default:
                $cmd = null;
        }

        $result = is_null($cmd)? false : $cmd->queryAll();
        $this->render($view, [
            'data_list' => $result,
            'start' => $criteria->DateStart,
            'end' => $criteria->DateEnd
        ]);
    }
}

class warehouseCriteria
{
    public $type;
    public $DateStart;
    public $DateEnd;

    const TYPE_STATISTIC = 0;
    const TYPE_INPUT = 1;
    const TYPE_OUTPUT = 2;
    const TYPE_NOLIST = 3;

    public function __construct($type, $DateStart, $DateEnd)
    {
        $this->type = $type;
        $this->DateStart = $DateStart;
        $this->DateEnd = $DateEnd;
    }
}