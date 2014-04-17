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
	public $size = 'normal';
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'id' => 'issue-tracker-modal',
		'style' => 'z-index: 100001'
	];
	
	public function run()
	{
		echo \nitm\widgets\modal\Modal::widget([
			'options' => $this->options,
			'size' => $this->size,
		]);
	}
}
?>