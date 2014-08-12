<?php
/**
 * @link http://www.nitm.com/
 * @copyright Copyright (c) 2014 Ninjas In The Machine INC
 */

namespace nitm\widgets\alerts\assets;

use yii\web\AssetBundle;

/**
 * @author Malcolm Paul <lefteyecc@nitm.com>
 */
class NotificationAsset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/alerts/assets';
	public $css = [
	];
	public $js = [
		'js/notifications.js'
	];	
	public $depends = [
		'nitm\assets\AppAsset',
		'kartik\widgets\DepDropAsset'
	];
}