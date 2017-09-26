<?php
class PageNav extends CWidget {
	
	/**
	 * default 风格显示分页区间数
	 * @var integer
	 */
	public $maxLinkCount    = 10;
	
	/**
	 * 翻页物件
	 * @var CPagination
	 */
	public $pages;
	
	/**
	 * 导航风格
	 * @var string
	 */
	public $style='showNumber';
	
	public $prevText = "&lt;&lt;上一页";
	public $nextText = "下一页&gt;&gt;";
	
	/**
	 * 执行物件
	 * @see CWidget::run()
	 */
	public function run() {
		if( method_exists($this, $this->style) ) {
			call_user_func( array($this,$this->style) );
		}
	}
	
	/**
	 * 创建翻页链接按钮
	 * @param string $lable 按钮标题
	 * @param integer $page 页码
	 * @param string $style css样式风格
	 */
	protected function createPageLink( $lable, $page, $htmlOptions ) {
		$htmlOptions['href'] = $this->pages->createPageUrl( $this->owner, $page );
		return CHtml::tag( "a", $htmlOptions, $lable );
	}
	
	/**
	 * 计算页码区间
	 */
	protected function getPageRange() {
		$currentPage  = $this->pages->getCurrentPage();
		$pageCount    = $this->pages->getPageCount();
	
		$beginPage    = max( 0, $currentPage-(int)($this->maxLinkCount/2) );
		if( ($endPage = $beginPage+$this->maxLinkCount-1) >= $pageCount ) {
			$endPage  = $pageCount-1;
			$beginPage=max( 0, $endPage - $this->maxLinkCount+1 );
		}
		return array( $beginPage, $endPage );
	}
	
	/**
	 * 上一页按钮
	 */
	protected function prevPageLink() {
		$lable   = $this->prevText;
		$options = array('class'=>"prev");
		$current = $this->pages->getCurrentPage(false);
	
		if( $current < 1 ) {
			$options['disabled'] = 'disabled';
		}
		else {
			--$current;
		}
		if( $current < 1 ) {
			$current = 0;
		}
		return $this->createPageLink( $lable, $current, $options );
	}
	
	/**
	 * 下一页按钮
	 */
	protected function nextPageLink() {
		$lable     = $this->nextText;
		$options   = array('class'=>"next");
		$current   = $this->pages->getCurrentPage(false);
		$pageCount = $this->pages->getPageCount();
	
		if( ($current+1) >= $pageCount ) {
			$options['disabled'] = 'disabled';
			$current             = $pageCount-1;
		}
		else {
			++$current;
		}
		echo $this->createPageLink( $lable, $current, $options );
	}
	
	/**
	 * 显示数字导航风格
	 */
	protected function showNumber() {
		$pageCount = $this->pages->getPageCount();
		if( $pageCount <= 0 ) {
			$beginPage = $endPage = 0;
		}
		else {
			list( $beginPage, $endPage ) = $this->getPageRange();
		}
		
		echo $this->prevPageLink(),"\n";
		
		$current = $this->pages->getCurrentPage(false);
		
		for( $i=$beginPage; $i<=$endPage; $i++ ) {
			if( $i == $current ) {
				$htmlOption = array('class'=>'active');
			}
			else {
				$htmlOption = array();
			}
			echo $this->createPageLink( $i+1, $i, $htmlOption ),"\n";
		}
		
		if( $pageCount - $endPage > $this->maxLinkCount/2 ) {
			echo '<span class="ell">...</span>';
		}
		
		echo $this->nextPageLink(),"\n";
		echo "共{$pageCount}页，到第<input type=\"text\" name=\"page\" class=\"ent\" data-pageCount=\"{$pageCount}\"/>
		页<button class=\"btn\">确定</button>";
	}
	
	/**
	 * 无数据显示导航风格
	 */
	protected function hideNumber() {
		echo $this->prevPageLink(),"\n";
		echo $this->nextPageLink(),"\n";
	}
	
	/**
	 * 显示数字导航风格(没有输入框)
	 */
	protected function showNumberPlus() {
		$pageCount = $this->pages->getPageCount();
		if( $pageCount <= 0 ) {
			$beginPage = $endPage = 0;
		}
		else {
			list( $beginPage, $endPage ) = $this->getPageRange();
		}
	
		echo $this->prevPageLink(),"\n";
	
		$current = $this->pages->getCurrentPage(false);
	
		for( $i=$beginPage; $i<=$endPage; $i++ ) {
			if( $i == $current ) {
				$htmlOption = array('class'=>'active');
			}
			else {
				$htmlOption = array();
			}
			echo $this->createPageLink( $i+1, $i, $htmlOption ),"\n";
		}
	
		if( $pageCount - $endPage > $this->maxLinkCount/2 ) {
			echo '<span class="ell">...</span>';
		}
	
		echo $this->nextPageLink(),"\n";
	}
}