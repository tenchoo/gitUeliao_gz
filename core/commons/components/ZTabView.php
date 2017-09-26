<?php
/**
 * 切换标签
 *
 * @author Morven <jdesign@zeeeda.com>
 * @link http://www.zeeeda.com/
 * @copyright 2015-2019 深圳市指易达电子商务有限公司
 * @license http://www.zeeeda.com/license/
 */

/**
 * CTabView displays contents in multiple tabs.
 *
 * At any time, only one tab is visible. Users can click on the tab header
 * to switch to see another tab of content.
 *
 * JavaScript is used to control the tab switching. If JavaScript is disabled,
 * CTabView still manages to display the content in a semantically appropriate way.
 *
 * To specify contents and their tab structure, configure the {@link tabs} property.
 * The {@link tabs} property takes an array with tab ID being mapped tab definition.
 * Each tab definition is an array of the following structure:
 * <ul>
 * <li>title: the tab title.</li>
 * <li>content: the content to be displayed in the tab.</li>
 * <li>view: the name of the view to be displayed in this tab.
 * The view will be rendered using the current controller's
 * {@link CController::renderPartial} method.
 * When both 'content' and 'view' are specified, 'content' will take precedence.
 * </li>
 * <li>url: a URL that the user browser will be redirected to when clicking on this tab.</li>
 * <li>data: array (name=>value), this will be passed to the view when 'view' is specified.</li>
 * </ul>
 *
 * For example, the {@link tabs} property can be configured as follows,
 * <pre>
 * $this->widget('CTabView', array(
 *     'tabs'=>array(
 *         'tab1'=>array(
 *             'title'=>'tab 1 title',
 *             'view'=>'view1',
 *             'data'=>array('model'=>$model),
 *         ),
 *         'tab2'=>array(
 *             'title'=>'tab 2 title',
 *             'url'=>'http://www.yiiframework.com/',
 *         ),
 *     ),
 * ));
 * </pre>
 *
 * By default, the first tab will be activated. To activate a different tab
 * when the page is initially loaded, set {@link activeTab} to be the ID of the desired tab.
 *
 * @author Morven <jdesign@zeeeda.com>
 * @package system.web.widgets
 * @since 1.0
 */
class ZTabView extends CWidget
{
	/**
	 * 默认CLASS
	 */
	const CSS_CLASS='tab';

	/**
	 * 
	 * @var 当前激活的TAB选项
	 */
	public $activeTab;
	/**
	 * @var tA数据
	 */
	public $viewData;
	/**
	 * @var array additional HTML options to be rendered in the container tag.
	 */
	public $htmlOptions;
	/**
	 * @var array tab definitions. The array keys are the IDs,
	 * and the array values are the corresponding tab contents.
	 * Each array value must be an array with the following elements:
	 * <ul>
	 * <li>title: the tab title. You need to make sure this is HTML-encoded.</li>
	 * <li>content: the content to be displayed in the tab.</li>
	 * <li>view: the name of the view to be displayed in this tab.
	 * The view will be rendered using the current controller's
	 * {@link CController::renderPartial} method.
	 * When both 'content' and 'view' are specified, 'content' will take precedence.
	 * </li>
	 * <li>url: a URL that the user browser will be redirected to when clicking on this tab.</li>
	 * <li>data: array (name=>value), this will be passed to the view when 'view' is specified.
	 * This option is available since version 1.1.1.</li>
	 * <li>visible: whether this tab is visible. Defaults to true.
	 * this option is available since version 1.1.11.</li>
	 * </ul>
	 * <pre>
	 * array(
	 *     'tab1'=>array(
	 *           'title'=>'tab 1 title',
	 *           'view'=>'view1',
	 *     ),
	 *     'tab2'=>array(
	 *           'title'=>'tab 2 title',
	 *           'url'=>'http://www.yiiframework.com/',
	 *     ),
	 * )
	 * </pre>
	 */
	public $tabs=array();

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		foreach($this->tabs as $id=>$tab)
			if(isset($tab['visible']) && $tab['visible']==false)
				unset($this->tabs[$id]);

		if(empty($this->tabs))
			return;

		if($this->activeTab===null || !isset($this->tabs[$this->activeTab]))
		{
			reset($this->tabs);
			list($this->activeTab, )=each($this->tabs);
		}

		$htmlOptions=$this->htmlOptions;
		if(isset($this->htmlOptions['id']))
			$this->id=$this->htmlOptions['id'];
		else
			$htmlOptions['id']=$this->id;
		if(!isset($htmlOptions['class']))
			$htmlOptions['class']=self::CSS_CLASS;		

		echo CHtml::openTag('ul',$htmlOptions)."\n";
		$this->renderHeader();		
		echo CHtml::closeTag('ul');
	}

	

	/**
	 * Renders the header part.
	 */
	protected function renderHeader()
	{
		
		foreach($this->tabs as $id=>$tab)
		{
			$title=isset($tab['title'])?$tab['title']:'undefined';
			$active=$id===$this->activeTab?' class="active"' : '';
			$url=isset($tab['url'])?$tab['url']:"#{$id}";			
			echo "<li{$active}>".CHtml::link($title,$url)."</li>\n";
		}
		
	}

}
