<?php
/**
 * 客户购买统计代理模型
 * 处理获取客户购买统计查询条件的校验工作
 */
class MemberForm extends SalemanForm
{
	public $member;
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

    public function attributeLabels()
    {
        return [
            'member' => '客户名称',
            'start'   => '开始时间',
            'end'     => '结束时间',
            'product' => '产品名称',
        ];
    }

    /**
     * 创建查询对象
     */
    protected function &getProcedure()
    {
        $this->start = $this->first_days($this->start);
        $this->end   = $this->last_days($this->end);
        $command     = Yii::app()->db->createCommand("call proc_member_statistic({$this->member},'{$this->start}','{$this->end}','{$this->product}')");
        return $command;
    }

    /**
     * 获取业务员信息 */
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
