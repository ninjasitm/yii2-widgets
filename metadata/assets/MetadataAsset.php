<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\metadata\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MetadataAsset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/metadata/assets';
	public $js = [
		'js/metadata.js'
	];
	public $jsOptions = ['position' => \yii\web\View::POS_END];
	public $depends = [
		'nitm\assets\AppAsset'
	];
}
