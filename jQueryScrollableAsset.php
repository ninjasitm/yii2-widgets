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
	];
	public $js = [
		'js/jquery-plugins/jquery-ui/ui/widget.js',
		'js/jquery-plugins/jquery-ui-scrollable/jquery-ui-scrollable.js'
	];
	public $jsOptions = ['position' => \yii\web\View::POS_END];
	public $depends = [
		'nitm\assets\AppAsset',
	];
}
