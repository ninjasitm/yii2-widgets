<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\modal\widget;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;

class Modal extends Widget
{	
	/*
	 * The size of the widget [large, mediaum, small, normal]
	 */
	public $size = null;
		
	/*
	 * HTML options for generating the widget
	 */
	public $options = [];
		
	/*
	 * HTML options for generating the widget content
	 */
	public $contentOptions = [];
	
	/*
	 * HTML options for generating the widget content
	 */
	public $dialogOptions = [];
	
	public $content = '';
	
	private $_defaultOptions = [
		'class' => 'modal fade',
		'role' => 'dialog',
		'id' => 'modal'
	];
	
	public $_defaultContentOptions = [
		"class" => "modal-content"
	];
	
	public $_defaultDialogOptions = [
		"class" => "modal-dialog"
	];
	
	public function run()
	{
		$this->options = array_merge($this->_defaultOptions, $this->options);
		$this->contentOptions = array_merge($this->_defaultContentOptions, $this->contentOptions);
		$this->dialogOptions = array_merge($this->_defaultDialogOptions, $this->dialogOptions);
		//Merge the class information in a unique manner to prevent duplicate classes
		$this->contentOptions['class'] = implode(' ', array_unique(explode(' ', $this->contentOptions['class'].' '.$this->_defaultContentOptions['class'])));
		$this->dialogOptions['class'] = implode(' ', array_unique(explode(' ', $this->dialogOptions['class'].' '.$this->_defaultDialogOptions['class'])));
		
		switch($this->size)
		{
			case 'x-large':
			$this->options['class'] .= " modal-xlg";
			break;
			
			case 'large':
			$this->options['class'] .= " modal-lg";
			break;
			
			case 'small':
			$this->options['class'] .= " modal-sm";
			break;
		}
		return Html::tag('div',
			Html::tag('div', Html::tag('div', $this->content,$this->contentOptions), $this->dialogOptions),
			$this->options
		);
	}
}
?>