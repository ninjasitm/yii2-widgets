<?php
/**
* @link http://www.nitm.com/
* @copyright Copyright (c) 2013 NITM Inc
*/

namespace nitm\widgets\priority;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use nitm\widgets\models\BaseWidget;

class Priority extends BaseWidget
{
	/**
	 * The active form model
	 */
	public $form;
		
	/**
	 * Default size is normal
	 */
	public $size = 'default';
	
	/**
	 * Default type is new
	 * Specified in the following format:
	 * [
	 *		'type' => [
	 *			'options' => [
	 *				'class' => Priority class,
	 *				...
	 *			],
	 *			'text' => If null &nbsp; will be used
	 *			'tag' => The tag used for displaying the info
	 *		],
	 *		...
	 * ];
	 */
	public $priorities;
	
	/**
	 * The widget type to return
	 */
	public $type;
	
	/**
	 * The addon type to return
	 */
	public $addonType;
	public $inputsInline;
	public $attribute = 'id';
	
	/*
	 * User HTML options for generating the widget
	 */
	public $options = [
	];
	
	/**
	 * Default priorities
	 */
	private $_defaultPriorities = [
		'critical' => [
			'class' => 'danger',
			'id' => 'priority-critical',
			'data-toggle' => 'tooltip',
			'data-placement' => 'top',
			'title' => 'Critical'
		],
		'important' => [
			'class' => 'info',
			'id' => 'priority-important',
			'data-toggle' => 'tooltip',
			'data-placement' => 'top',
			'title' => 'Important'
		],
		'normal' => [
			'class' => 'default',
			'id' => 'priority-normal',
			'data-toggle' => 'tooltip',
			'data-placement' => 'top',
			'title' => 'Normal'
		]
	];
	
	/**
	 * The types of return data supported
	 */
	private $_types = [
		'addon',	//Return an array for addon use
		'buttons',	//Return a widget using buttons
		'text',		//Return a widgets using divs and text
	];
	
	/**
	 * The types of return data supported
	 */
	private $_addonTypes = [
		'dropdown',	//Return an array for addon use
		'buttons',	//Return a widget using buttons
		'radios',	//Return a widgets using divs and text
		'checkbox',	//Return a widgets using divs and text
	];
	
	/**
	 * Sizes supported
	 */
	private $_sizes = [
		'tiny' => 'priority-xs',
		'small' => 'priority-sm',
		'default' => 'priority-default',
		'large' => 'priority-lg',
	];
	
	/*
	 * HTML options for generating the widget
	 */
	public $_defaultOptions = [
		'class' => 'priority',
		'role' => 'priorityIndicator',
		'id' => 'priority'
	];
	
	public function init()
	{
		$this->priorities = !is_array($this->priorities) ? $this->_defaultPriorities : $this->priorities;
	}
	
	public function run()
	{
		switch($this->type)
		{
			case 'buttons':
			echo $this->getAsButtons();
			break;
			
			case 'addon':
			echo $this->getAsAddon();
			break;
			
			default:
			echo $this->getAsText();
			break;
		}
	}
	
	public function getAsAddon()
	{
		$ret_val = '';
		$items = [];
		$itemsLabels = [];
		$form = $this->form;
		$attribute = $this->attribute;
		$this->model->$attribute = (!$this->model->$attribute || !array_key_exists($this->model->$attribute, $this->priorities)) ? 'normal' : $this->model->$attribute;
		foreach($this->priorities as $name=>$priority)
		{
			$text = isset($priority['text']) ? $priority['text'] : ucfirst($name);
			$priorityOptions = isset($this->_defaultPriorities[$name]) ? $this->_defaultPriorities[$name] : $this->defaultPriorities['normal'];
			$options = isset($priority['options']) ? array_merge($priorityOptions, $priority['options']) : $priorityOptions;
			switch($this->addonType)
			{
				case 'buttons':
				case 'checkboxlist':
				case 'radiolist':
				$btnQualifier = 'btn';
				break;
				
				default:
				$btnQualifier = 'bg';
				break;
			}
			$options['class'] = "$btnQualifier $btnQualifier-".$options['class'];
			switch($this->size)
			{
				case 'tiny':
				$options['class'] .= " $btnQualifier-xs";
				break;
				
				case 'small':
				$options['class'] .= " $btnQualifier-sm";
				break;
				
				case 'large':
				$options['class'] .= " $btnQualifier-lg";
				break;
			}
			$options['value'] = $name;
			$items[$name] = $text;
			$itemsLabels[$name] = [
				'label' => $text,
				'options' => $options,
				'url' => '#'
			];
		}
		$itemsLabels[$this->model->$attribute]['options']['class'] .= ' active';
		$this->options['inline'] = $this->inputsInline;
		switch($this->addonType)
		{
			case 'dropdown':
			$ret_val = \yii\bootstrap\ButtonDropdown::widget([
				'label' => 'Priority',
				'dropdown' => [
					'items' => $itemsLabels,
				],
				'options' => ['class'=>'btn-primary']
			]);
			break;
			
			case 'radiolist':
			$this->options['data-toggle'] = 'buttons';
			$this->options['class'] = 'btn-group';
			$this->options['item'] = function ($index, $label, $name, $checked, $value) use ($itemsLabels) {
				$itemOptions = [
					'value' => $value	
				];
				return Html::label(Html::radio($name, $checked, $itemOptions).' '. $label['label'], null, $itemsLabels[$value]['options']);
			};
			$ret_val = $this->form->field($this->model, $this->attribute)->radioList($itemsLabels, $this->options)->label("Priority", ['class' => 'sr-only']);
			break;
			
			case 'checkboxlist':
			$this->options['itemOptions'] = [
				'labelOptions' => [
					'class' => 'btn'
				]
			];
			$ret_val = $this->form->field($this->model, $this->attribute, [
				'options' => [
					'class' => 'btn-group',
					'data-toggle' => 'buttons',
				]
			])->checkBoxList($items, $this->options);
			break;
			
			default:	
			//Return as buttons by default
			$model = $this->model;
			$ret_val = implode(PHP_EOL, array_map(function ($item) use ($model, $form, $attribute){
				//$item['options']['name'] = $model::formName()."[$attribute]";
				$item['options']['id'] = strtolower($model::formName()."-$attribute");
				return Html::tag('button', $item['label'], $item['options']) ;
			}, $itemsLabels));
			break;
		}
		return $ret_val;
	}
	
	protected function getAsButtons()
	{
		$ret_val = '';
		foreach($this->priorities as $name=>$priority)
		{
			$tag = 'button';
			$text = isset($priority['text']) ? $priority['text'] : ucfirst($name);
			$priorityOptions = isset($this->_defaultPriorities[$name]) ? $this->_defaultPriorities[$name] : $this->defaultPriorities['normal'];
			$options = isset($priority['options']) ? array_merge($priorityOptions, $priority['options']) : $priorityOptions;
			$options['class'] = 'btn btn-'.$options['class'];
			//$options['name'] = $this->model->formName().'['.$this->attribute.']';
			switch($this->size)
			{
				case 'small':
				$options['class'] .= ' btn-sm';
				break;
				
				case 'large':
				$options['class'] .= ' btn-sm';
				break;
			}
			$ret_val .= Html::tag($tag, $text, $options);
		}
		$size = isset($this->_sizes[$this->size]) ? $this->_sizes[$this->size] : $this->_sizes['default'];
		$this->options = array_merge($this->_defaultOptions, $this->options);
		return Html::tag('div', $ret_val, $this->options);
	}
	
	protected function getAsText()
	{
		$ret_val = '';
		foreach($this->priorities as $name=>$priority)
		{
			$tag = isset($priority['tag']) ? $priority['tag'] : 'div';
			$text = isset($priority['text']) ? $priority['text'] : ucfirst($name);
			$priorityOptions = isset($this->_defaultPriorities[$name]) ? $this->_defaultPriorities[$name] : $this->defaultPriorities['normal'];
			$options = isset($priority['options']) ? array_merge($priorityOptions, $priority['options']) : $priorityOptions;
			$ret_val .= Html::tag($tag, $text, $options);
		}
		$size = isset($this->_sizes[$this->size]) ? $this->_sizes[$this->size] : $this->_sizes['default'];
		$this->options = array_merge($this->_defaultOptions, $this->options);
		$this->options['class'] .= ' '.$size;
		return Html::tag('div', $ret_val, $this->options);
	}
}
?>