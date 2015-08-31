<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\ajax;

use yii\helpers\Html;

/**
 * TicketMetadata widget renders info about a ticket only after the page loads
 */
class AjaxWidget extends \yii\base\Widget
{	
	/**
	 * The url where the information should be taken from
	 */
	public $url;
	
	 /**
	  * The text shown when loading
	  */
	public $placeholder = "Loading...";
	
	/**
	 * The parameters to send with the url
	 */
	public $queryParams;
		
	public $options = [
		'class' => 'text-left',
		'role' => 'AjaxWidget',
		'style' => 'overflow: auto'
	];
	/**
	 * When should the request be pulled?
	 */
	public $on;
	
	private $_events = [
		'documentLoad' => 'On Document Load',
		'inViewport' => 'The element is in the vieport',
	];
	
	public function init()
	{
		$this->options['id'] = "ajaxWidget".uniqid();
		AjaxWidgetAsset::register($this->getView());
	}
	
	public function run()
	{
		$script = '';
		$this->options['data-type'] = $this->on;
		$this->options['data-url'] = $this->url;
		$this->options['data-query-params'] = json_encode($this->queryParams);
		$this->options['role'] = isset($this->options['role']) ? $this->options['role'].' AjaxWidget' : 'AjaxWidget';
		switch($this->on)
		{	
			case 'inViewport':
			$this->options['data-on-scrolled-in'] = $this->options['id'];
			$this->options['role'] .= ' onScrolledIntoView';
			break;
		}
		return Html::tag('div', Html::tag('span', $this->placeholder), $this->options);
	}
}
