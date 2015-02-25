<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;

class RepliesModal extends Widget
{
	public $size = 'large';
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'id' => 'replies-modal',
		'style' => 'z-index: 100001',
	];
	
	public $dialogOptions = [
	];
	
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