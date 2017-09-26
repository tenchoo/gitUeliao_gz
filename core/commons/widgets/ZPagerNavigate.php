<?php
/**
 * 翻页模板物件
 * @author yagas
 * @version 0.2
 * @package CBasePager
 * @example
 *
 */
class ZPagerNavigate extends CBasePager {

	/**
	 * 分页风格
	 * @var string
	 */
	public $type            = "default";

	/**
	 * default 风格显示分页区间数
	 * @var integer
	 */
	public $maxLinkCount    = 10;

	/**
	 * 上一页按钮标题
	 * @var string
	 */
	public $prevButtonTitle = "<i></i>";

	/**
	 * 下一页按钮标题
	 * @var string
	 */
	public $nextButtonTitle = "<i></i>";

	public function run() {
		$methodName = strtolower($this->type) . "Nav";
		echo call_user_func( array($this,$methodName) );
	}

	/**
	 * 创建翻页链接按钮
	 * @param string $lable 按钮标题
	 * @param integer $page 页码
	 * @param string $style css样式风格
	 */
	protected function createPageLink( $lable, $page, $htmlOptions ) {
		if( array_key_exists ( 'disabled',$htmlOptions ) ){
			$htmlOptions['href'] = 'javascript:;';
		}else{
			$htmlOptions['href'] = $this->createPageUrl( $page );
		}
		
		$link  = CHtml::tag( "a", $htmlOptions, $lable );
		return "<li>${link}</li>";
	}

	/**
	 * default风格上一页按钮
	 */
	protected function prevPageLink() {
		$lable   = $this->prevButtonTitle;
		$options = array('class'=>"btn btn-cancel btn-xs");
		$current = $this->getCurrentPage(false);

		if( $current < 1 ) {
			$options['class']    = "prev " . $options['class'];
			$options['disabled'] = 'disabled';
		}
		else {
			$lable           .= "上一页";
			$options['class'] = "prev-c " . $options['class'];
			--$current;
		}
		if( $current < 1 ) {
			$current = 0;
		}
		$link  = $this->createPageLink( $lable, $current, $options );
		return "<li>${link}</li>";
	}

	/**
	 * default风格下一页按钮
	 */
	protected function nextPageLink() {
		$lable   = $this->nextButtonTitle;
		$options = array('class'=>"btn btn-cancel btn-xs");
		$current = $this->getCurrentPage(false);

		if( ($next=$current+1) >= $this->getPageCount() ) {
			$options['class']    = "next-l " . $options['class'];
			$options['disabled'] = 'disabled';
			$current             = $this->getPageCount();
		}
		else {
			$lable            = "下一页" . $lable;
			$options['class'] = "next " . $options['class'];
			++$current;
		}

		$link  = $this->createPageLink( $lable, $current, $options );
		return "<li>${link}</li>";
	}

	/**
	 * 计算页码区间
	 */
	protected function getPageRange() {
		$currentPage  = $this->getCurrentPage();
		$pageCount    = $this->getPageCount();

		$beginPage    = max( 0, $currentPage-(int)($this->maxLinkCount/2) );
		if( ($endPage = $beginPage+$this->maxLinkCount-1) >= $pageCount ) {
			$endPage  = $pageCount-1;
			$beginPage=max( 0, $endPage - $this->maxLinkCount+1 );
		}
		return array( $beginPage, $endPage );
	}

	/**
	 * 分页风格：default
	 */
	protected function defaultNav() {
		if( ($pageCount = $this->getPages()->getItemCount()) <= 0 ) {
			$beginPage = $endPage = 0;
		}
		else {
			list( $beginPage, $endPage ) = $this->getPageRange();
		}

		$links = array("<div class=\"pull-right\">
					<div class=\"page pull-left\">
                    <ul class=\"list-unstyled\">");

		array_push( $links, $this->prevPageLink() );
		for( $i=$beginPage; $i<=$endPage; ++$i ) {
			$css = "page-num";
			if( $i == $this->getCurrentPage(false) ) {
				$css .= " active";
			}
			array_push( $links, $this->createPageLink( $i+1, $i, array("class"=>$css) ) );
		}
		array_push( $links, $this->nextPageLink() );
		array_push($links, '</ul>
		</div>
		<div class="page-text text-muted pull-left">
			<span>到第</span>
			<input type="text">
			<span>页</span>
		</div>
		<a href="" class="btn btn-cancel btn-xs">确定</a>
		</div>');
		return implode( "\n", $links );
	}

	protected function miniNav() {
		$offsetStrat = $this->getPages()->getOffset();
		$offsetEnd   = $offsetStrat + $this->getPageSize();
		$offsetStrat += 1;
		if( $offsetEnd > ($itemCount=$this->getItemCount()) ) {
			$offsetEnd = $itemCount;
		}
		$total       = $this->getPages()->getItemCount();
		$pageTotal   = $this->getPageCount();
		$currentPage = $this->getCurrentPage(false);
		$index       = $currentPage + 1;
		$prev        = $currentPage-1;
		$next        = $currentPage+1;
		if( $prev<1 ) {
			$prev = 0;
		}
		
		$nextOptions = array("class"=>"next btn btn-cancel btn-xs");
		if( $next >= $pageTotal ) {
			$next = $pageTotal;
			$nextOptions['disabled'] = 'disabled';
		}

		$links = array();
		$header = "<div class=\"pull-right\">
				<div class=\"page-text text-muted pull-left\">第 ${offsetStrat}-${offsetEnd} 条, 共 ${total} 条 <span>第${index}页/共${pageTotal}页</span></div>
				<div class=\"page pull-left\">
				<ul class=\"list-unstyled\">";
		array_push( $links, $header );
		array_push( $links, $this->createPageLink( $this->prevButtonTitle, $prev, array("class"=>"prev btn btn-cancel btn-xs")) );
		array_push( $links, $this->createPageLink( "下一页".$this->nextButtonTitle, $next, $nextOptions) );
		array_push( $links, "</ul></div></div>" );
		return implode( "\n", $links );
	}
}