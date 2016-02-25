<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class jQueryScrollableAsset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/assets';
	public $css = [
		"https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css"
	];
	public $js = [
		'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js',
		'js/jquery-plugins/jquery-ui-scrollable/jquery-ui-scrollable.js'
	];
	public $jsOptions = ['position' => \yii\web\View::POS_END];
	public $depends = [
		'nitm\assets\AppAsset',
	];
}
