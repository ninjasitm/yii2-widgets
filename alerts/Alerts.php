<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\alerts;

use Yii;
use yii\helpers\Html;
use nitm\widgets\models\Alerts as AlertsModel;
use nitm\widgets\models\search\Alerts as AlertsSearch;
use kartik\icons\Icon;

class Alerts extends \yii\base\Widget
{	
	public $model;
	public $withForm = false;
	 
	/*
	 * HTML options for the alert message container
	 */
	public $options = [
		'role' => 'alertFormParent',
		'id' => 'alerts',
		'class' => 'alert col-lg-4 col-md-4',
	];
	
	/*
	 * HTML options for generating the widget
	 */
	public $listOptions = [
		'class' => 'alerts-list',
		'role' => 'alertMessages',
		'id' => 'alerts-list',
		'data-parent' => 'alertParent'
	];
	
	public function init()
	{
		$this->model = ($this->model instanceof AlertsModel) ? $this->model : new AlertsModel();
		parent::init();
		Asset::register($this->getView());
	}
	
	public function run()
	{
		switch(($this->model instanceof AlertsModel))
		{
			case true:
			$searchModel = new AlertsSearch;
			$searchModel->withThese = ['user'];
			$get = \Yii::$app->request->getQueryParams();
			$params = $get;
			unset($params['type'], $params['id']);
	
			$dataProvider = $searchModel->search(array_merge($params));
			$dataProvider->setSort([
				'defaultOrder' => [
					'remote_type' => SORT_ASC,
				]
			]);
			$dataProvider->query->andWhere([
				'user_id' => \Yii::$app->user->getId()
			]);
			$alerts = $this->getView()->render('@nitm/widgets/views/alerts/index', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
				'widget' => $this,
				'options' => $this->options,
				'listOptions' => $this->listOptions,
				'withForm' => $this->withForm,
				'primaryModel' => $this->model,
			]);
			break;
		}
		return $alerts;
	}
}
?>