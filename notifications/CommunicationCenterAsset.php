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
class CommunicationCenterAsset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets';
	public $js = [
		'notifications/assets/js/communication-center.js',
		'replies/assets/js/replies.js',
		'ajax/assets/js/polling.js',
		'alerts/assets/js/notifications.js',
	];
	public $css = [
	];	
	public $depends = [
		'nitm\assets\AppAsset',
	];
}