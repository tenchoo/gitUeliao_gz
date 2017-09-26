<?php 
/**
 * 自定义分页类
 */
class widgetLinkpager extends CLinkPager {
	/**
	 * 重写父类run方法，去掉自动生成样式表文件
	 * This overrides the parent implementation by displaying the generated page buttons.
	 */
	public function run()
	{
		$buttons=$this->createPageButtons();
		if(empty($buttons))
			return;
		echo $this->header;
		echo CHtml::tag('ul',$this->htmlOptions,implode("\n",$buttons));
		echo $this->footer;
	}
}