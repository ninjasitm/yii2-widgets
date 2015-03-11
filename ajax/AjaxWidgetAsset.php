<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\ajax;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AjaxWidgetAsset extends AssetBundle
{
	public $sourcePath = '@nitm/widgets/ajax/assets';
	public $css = [
	];
	public $js = [
		'js/ajax-widget.js'
	];
	public $jsOptions = ['position' => \yii\web\View::POS_END];
	public $depends = [
		'nitm\assets\AppAsset',
	];
	
	public function initView($view)
	{
		$files = [
			pathinfo('@nitm/assets/js/jquery-plugins/jquery.ui.widget.js'),
			pathinfo('@nitm/assets/js/jquery-plugins/jquery-ui-scrollable/jquery-ui-scrollable.js')
		];
		foreach($files as $f)
		{
			$asset = new \yii\web\AssetBundle([
				'sourcePath' => $f['dirname'],
				'js' => [$f['basename']],
			]);
			$asset->publish($view->getAssetManager());
			$view->assetBundles[static::className().'\\'.$f['basename']] = $asset;
		}
		return $this;
	}
}
