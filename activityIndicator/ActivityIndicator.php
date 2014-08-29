<?php
/**
* @link http://www.nitm.com/
* @copyright Copyright (c) 2013 NITM Inc
*/

namespace nitm\widgets\activityIndicator;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;

class ActivityIndicator extends Widget
{
	/**
	 * Defaults to the top left
	 */
	public $position;
		
	public $text;
	/**
	 * The indicator class options
	 */
	public $indicator = [];
	
	/**
	 * Default size is normal
	 */
	public $size = 'default';
	
	/**
	 * Default type is new
	 */
	public $type = 'new';
	
	/**
	 * The actions to enable
	 */
	public $actions;
	
	/*
	 * User HTML options for generating the widget
	 */
	public $options = [
	];
	
	/**
	 * Indicator types supports
	 */
	private $_types = [
		'error' => 'activity-error',
		'new' => 'activity-error',
		'info' => 'activity-info',
		'default' => 'activity-error',
		'success' => 'activity-success',
	];
	
	/**
	 * Sizes supported
	 */
	private $_sizes = [
		'small' => 'activity-sm',
		'default' => 'activity-default',
		'large' => 'activity-lg',
	];
	
	/**
	 * The default indicator class
	 */
	private $_indicatorOptions = [
		"options" => [
			"class" => 'glyphicon glyphicon-exclamation-sign',
		],
		'tag' => 'span',
	];
	
	/*
	 * HTML options for generating the widget
	 */
	public $_defaultOptions = [
		'class' => 'activity',
		'role' => 'activityIndicator',
		'id' => 'activity'
	];
	public function init()
	{
		parent::init();
		Asset::register($this->getView());
	}
	
	public function run()
	{
		$size = isset($this->_sizes[$this->size]) ? $this->_sizes[$this->size] : $this->_sizes['default'];
		$type = isset($this->_types[$this->type]) ? $this->_types[$this->type] : $this->_types['default'];
		$this->options = array_merge($this->_defaultOptions, $this->options);
		$this->options['class'] .= ' '.$size.' '.$type;
		$this->indicator = array_merge($this->_indicatorOptions, $this->indicator);
		if($this->text != null)
		{
			unset($this->indicator['options']['class']);
		}
		$positions = explode(' ', $this->position);
		foreach($positions as $position)
		{
			switch($position)
			{
				case 'right':
				$this->options['class'] .= ' activity-right';
				break;
				
				case 'top':
				$this->options['class'] .= ' activity-top';
				break;
				
				case 'bottom':
				$this->options['class'] .= ' activity-bottom';
				break;
			}
		}
		$this->options['class'] .= ' '.@$this->indicator['options']['class'];
		$this->options['id'] .= uniqid();
		echo Html::tag($this->indicator['tag'], 
			$this->text, 
			$this->options
		);
	}
}
?>