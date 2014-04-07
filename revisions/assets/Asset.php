<?php
/**
 * @link http://www.nitm.com/
 * @copyright Copyright (c) 2014 Ninjas In The Machine INC
 */

namespace nitm\widgets\revisions\assets;

use yii\web\AssetBundle;

/**
 * @author Malcolm Paul <lefteyecc@nitm.com>
 */
class Asset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/revisions/assets';
	public $css = [
	];
	public $js = [
		'js/revisions.js'
	];	
	public $depends = [
		'nitm\module\assets\AppAsset',
		'yii\redactor\widgets\RedactorAsset',
	];
}