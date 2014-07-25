<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\modal;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;

class Modal extends \yii\bootstrap\Modal
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
		'class' => 'modal fade in',
		'role' => 'dialog',
		'id' => 'modal'
	];
	
	public $_defaultContentOptions = [
		"class" => "modal-content"
	];
	
	public $_defaultDialogOptions = [
		"class" => "modal-dialog"
	];
	
	public function init()
	{
		$this->initOptions();
	}
	
	public function run()
	{
		$this->options = array_merge($this->_defaultOptions, $this->options);
		//$this->options['id'] = $this->options['id'].uniqid();
		$this->contentOptions = array_merge($this->_defaultContentOptions, $this->contentOptions);
		$this->dialogOptions = array_merge($this->_defaultDialogOptions, $this->dialogOptions);
		//Merge the class information in a unique manner to prevent duplicate classes
		$this->contentOptions['class'] = implode(' ', array_unique(explode(' ', $this->contentOptions['class'].' '.$this->_defaultContentOptions['class'])));
		$this->dialogOptions['class'] = implode(' ', array_unique(explode(' ', $this->dialogOptions['class'].' '.$this->_defaultDialogOptions['class'])));
		
		switch($this->size)
		{
			case 'x-large':
			$this->dialogOptions['class'] .= " modal-xlg";
			break;
			
			case 'large':
			$this->dialogOptions['class'] .= " modal-lg";
			break;
			
			case 'small':
			$this->dialogOptions['class'] .= " modal-sm";
			break;
		}
		return $this->renderTogglebutton().Html::tag('div',
				Html::tag('div', 
					Html::tag('div', $this->renderHeader().$this->content, $this->contentOptions), 
				$this->dialogOptions),
			$this->options
		);
	}
	
	protected function renderToggleButton()
	{
		$options = isset($this->toggleButton['wrapper']) ? $this->toggleButton['wrapper'] : [];
		unset($this->toggleButton['wrapper']);
		$this->toggleButton['data-target'] = '#'.$this->options['id'];
		$tag = isset($options['tag']) ? $options['tag'] : 'span';
		return Html::tag($tag, parent::renderToggleButton(), $options);
	}
}
?>