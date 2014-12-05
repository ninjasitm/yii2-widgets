<?php
/**
 * @link http://www.nitm.com/
 * @copyright Copyright (c) 2014 Ninjas In The Machine INC
 */

namespace nitm\widgets\notifications;

use yii\web\AssetBundle;

/**
 * @author Malcolm Paul <lefteyecc@nitm.com>
 */
class Asset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/ias/assets';
	public $js = [
		'js/nitm-ias.js'
	];
	public $css = [
		'css/nitm-ias.css'
	];	
	public $depends = [
		'kop\y2sp\assets\InfiniteAjaxScrollAsset',
		'nitm\assets\AppAsset',
	];
}