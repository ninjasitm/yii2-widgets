<?php
/**
 * @link http://www.nitm.com/
 * @copyright Copyright (c) 2014 Ninjas In The Machine INC
 */

namespace nitm\widgets\replies;

use yii\web\AssetBundle;

/**
 * @author Malcolm Paul <lefteyecc@nitm.com>
 */
class Asset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/replies/assets';
	public $css = [
		'css/replies.css',
	];
	public $js = [
		'js/replies.js'
	];	
	public $depends = [
		'nitm\assets\AppAsset',
		'yii\imperavi\ImperaviRedactorAsset',
	];
}