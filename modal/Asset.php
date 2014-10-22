<?php
/**
 * @link http://www.nitm.com/
 * @copyright Copyright (c) 2014 Ninjas In The Machine INC
 */

namespace nitm\widgets\modal;

use yii\web\AssetBundle;

/**
 * @author Malcolm Paul <lefteyecc@nitm.com>
 */
class Asset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/modal/assets';
	public $js = [
		'js/search-modal.js'
	];
	public $css = [
	];	
	public $depends = [
		'nitm\assets\AppAsset',
	];
}