<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\issueTracker;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;

class issueTrackerModal extends Widget
{
	public $size = 'large';
	public $options = [];
	
	/*
	 * HTML options for generating the widget
	 */
	public $_options = [
		'id' => 'issue-tracker-modal',
		'style' => 'z-index: 1043',
	];
	
	public $dialogOptions = [
	];
	
	public function init()
	{
		$this->options = array_merge($this->_options, $this->options);
	}
	
	public function run()
	{
		echo \nitm\widgets\modal\Modal::widget([
			'options' => $this->options,
			'dialogOptions' => $this->dialogOptions,
			'size' => $this->size,
		]);
	}
}
?>