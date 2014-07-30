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

class AlertsForm extends \yii\base\wWidget
{	
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
	}
	
	public function run()
	{	
		$this->model->setScenario('create');
		return $this->render('@nitm/views/alerts/_form', [
			'model' => $this->model,
			'widget' => $this,
			'inline' => $this->inline,
		]);
	}
}
?>