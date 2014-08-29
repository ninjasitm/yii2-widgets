<?php
/**
 * @link http://www.nitm.com/
 * @copyright Copyright (c) 2014 Ninjas In The Machine INC
 */

namespace nitm\widgets\issueTracker;

use yii\web\AssetBundle;

/**
 * @author Malcolm Paul <lefteyecc@nitm.com>
 */
class Asset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/issueTracker/assets';
	public $css = [
	];
	public $js = [
		'js/tracker.js'
	];	
	public $depends = [
		'nitm\assets\AppAsset',
	];
}