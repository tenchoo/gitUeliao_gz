<?php
/**
 * 自定义分页类,用nextId,只有上一页，下一页，首页三个链接。
 */
class widgetNextpager extends CLinkPager {

	//下一页调用
	public $nextId;

	//上一页调用
	public $preId;

	//翻页参数
	public $pageVar = 's';

	/**
	 * 重写父类run方法
	 */
	public function run()
	{
		$buttons = $this->createPageButtons();
		echo CHtml::tag('ul',$this->htmlOptions,implode("\n",$buttons));
	}

	/**
	 * 重写父类createPageUrl方法
	 */
 	protected function createPageUrl($page)
	{
		$params = $_GET;
		if( !empty($page) )
			$params[$this->pageVar] = $page;
		else
			unset($params[$this->pageVar]);
		return $this->getController()->createUrl($this->getController()->route,$params);
	}


	/**
	 * 重写父类createPageButtons方法
	 */
	protected function createPageButtons()
	{
		$buttons = array();

		// first page
		$buttons[] = $this->createPageButton($this->firstPageLabel,0,$this->firstPageCssClass,false,false);

		// prev page
		if( !empty($this->preId) ){
			$buttons[] = $this->createPageButton($this->prevPageLabel,'pre_'.$this->preId,$this->previousPageCssClass,false,false);
		}

		// next page
		if( !empty($this->nextId) ){
			$buttons[] = $this->createPageButton($this->nextPageLabel,'next_'.$this->nextId,$this->previousPageCssClass,false,false);
		}

		return $buttons;
	}
}