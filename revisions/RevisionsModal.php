<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\revisions;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;

class RevisionsModal extends Widget
{	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'id' => 'revisions-view-modal',
		'style' => 'z-index: 100003',
	];
	
	public function run()
	{
		echo \nitm\widgets\modal\Modal::widget([
			'options' => $this->options
		]);
	}
}
?>