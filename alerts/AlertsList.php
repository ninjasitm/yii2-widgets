<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\alerts;

use Yii;
use yii\helpers\Html;
use nitm\widgets\models\Alerts;
use kartik\icons\Icon;

class AlertsList extends \yii\base\Widget
{	
	public $model;
	public $inline = true;
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'alerts-list',
		'role' => 'alertsListContainer',
		'id' => 'alertsList'
	];
	
	public function init()
	{
		$this->model = ($this->model instanceof Alerts) ? $this->model : new Alerts();
		parent::init();
		assets\AlertsAsset::register($this->getView());
	}
	
	public function run()
	{	
		$action = $this->model->isNewRecord ? 'create' : 'update';
		$this->model->setScenario($action);
		return $this->render('@nitm/widgets/views/alerts/'.$action, [
			'model' => $this->model,
			'widget' => $this,
			'inline' => $this->inline,
		]);
	}
}
?>