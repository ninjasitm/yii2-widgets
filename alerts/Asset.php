<?php
/**
 * @link http://www.nitm.com/
 * @copyright Copyright (c) 2014 Ninjas In The Machine INC
 */

namespace nitm\widgets\alerts;

use yii\web\AssetBundle;

/**
 * @author Malcolm Paul <lefteyecc@nitm.com>
 */
class Asset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/alerts/assets';
	public $css = [
	];
	public $js = [
		'js/alerts.js'
	];	
	public $depends = [
		'nitm\assets\AppAsset',
		'kartik\widgets\DepDropAsset'
	];
}