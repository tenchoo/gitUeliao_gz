<?php
/**
 * 会员交易明细代理模型
 * 处理获取会员交易明细查询条件的校验工作
 */
class MemberdetailForm extends SalemandetailForm
{
    public $member; //客户编号
    public $start; //筛选开始时间
    public $end; //筛选结束时间
    public $product = ''; //产品单品编码

    protected $account; //客户账号
    protected $quantitys = 0; //销量统计
    protected $prices    = 0; //销售额统计

    /**
     * 查询条件校验规则的定义 */
    public function rules()
    {
        return [
            ['start,end,member', 'required', 'message' => Yii::t('warning', 'DATA_NOT_FOUND,{attribute}')],
            ['start,end', 'check_dateformat', 'message' => 'DATA_FORMAT_INVALID,{attribute}'],
            ['member', 'member_exists', 'message' => 'DATA_ACCOUNT_INVALID,{attribute}'],
            ['product', 'product_exists', 'message' => 'DATA_PRODUCT_INVALID,{attribute}'],			
        ];
    }

    /**
     * 字段标题
     */
    public function attributeLabels()
    {
        return [
            'member' => '客户名称',
            'start'   => '开始时间',
            'end'     => '结束时间',
            'product' => '产品编号',
        ];
    }

    /**
     * 创建查询对象
     */
    protected function &getProcedure()
    {
        $this->start = $this->first_days($this->start);
        $this->end   = $this->last_days($this->end);
        $command     = Yii::app()->db->createCommand("call proc_member_detail({$this->member},'{$this->start}','{$this->end}','{$this->product}')");
        return $command;
    }

    /**
     * 获取客户信息 */
    public function getAccount()
    {
        $account = tbMember::model()->findByPk($this->member);
        if (!$account) {
            return;
        }

        $username = $account->profiledetail->companyname;
        if (empty($username)) {
            $username = '[未命名的用户]';
        }
        return $username;
    }
}
