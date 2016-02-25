<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\ajax;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AjaxWidgetAsset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/ajax/assets';
	public $css = [
	];
	public $js = [
		'js/ajax-widget.js',
		'js/polling.js',
	];
	public $jsOptions = ['position' => \yii\web\View::POS_END];
	public $depends = [
		'nitm\assets\AppAsset',
		'nitm\widgets\jQueryScrollableAsset',
	];
}
