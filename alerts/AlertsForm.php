<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\alerts;

use Yii;
use yii\helpers\Html;
use nitm\models\Alerts;
use kartik\icons\Icon;

class AlertsForm extends \yii\base\Widget
{	
	public $model;
	public $inline = true;
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'alerts',
		'role' => 'alertsFormContainer',
		'id' => 'alertsForm'
	];
	
	public function init()
	{
		$this->model = ($this->model instanceof Alerts) ? $this->model : new Alerts();
		parent::init();
		assets\Asset::register($this->getView());
	}
	
	public function run()
	{	
		$action = $this->model->isNewRecord ? 'create' : 'update';
		$this->model->setScenario($action);
		return $this->render('@nitm/views/alerts/'.$action, [
			'model' => $this->model,
			'widget' => $this,
			'inline' => $this->inline,
		]);
	}
}
?>