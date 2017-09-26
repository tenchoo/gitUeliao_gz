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
	
	protected function createPageButtons()
	{
			$pageCount=$this->getPageCount();
	
			list($beginPage,$endPage)=$this->getPageRange();
			$currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
			$buttons=array();
	
			// first page
			if ($this->firstPageLabel !== false) {
				$buttons[]=$this->createPageButton($this->firstPageLabel,0,$this->firstPageCssClass,$currentPage<=0,false);
			}
			// prev page
			if ($this->prevPageLabel !== false) {
				if(($page=$currentPage-1)<0)
					$page=0;
					$buttons[]=$this->createPageButton($this->prevPageLabel,$page,$this->previousPageCssClass,$currentPage<=0,false);
			}
	
			// internal pages
			if( $pageCount==0 ) {
				$buttons[]=$this->createPageButton(1,0,$this->internalPageCssClass,false,true);
			}
			
			for($i=$beginPage;$i<=$endPage;++$i)
				$buttons[]=$this->createPageButton($i+1,$i,$this->internalPageCssClass,false,$i==$currentPage);
	
				// next page
				if ($this->nextPageLabel !== false) {
					if(($page=$currentPage+1)>=$pageCount-1)
						$page=$pageCount-1;
						$buttons[]=$this->createPageButton($this->nextPageLabel,$page,$this->nextPageCssClass,$currentPage>=$pageCount-1,false);
				}
				// last page
				if ($this->lastPageLabel !== false) {
					$buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,$this->lastPageCssClass,$currentPage>=$pageCount-1,false);
				}
	
				return $buttons;
	}
}