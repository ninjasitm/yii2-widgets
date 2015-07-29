<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\alerts;

use Yii;
use yii\helpers\Html;
use nitm\widgets\models\Notification;
use nitm\widgets\models\search\Notification as NotificationSearch;
use kartik\icons\Icon;

class Notifications extends \yii\base\Widget
{	
	public $model;
	public $inline = true;
	public $contentOnly;
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'notification-list',
		'role' => 'notificationListContainer',
		'id' => 'notificationList'
	];
	
	public function init()
	{
		$this->model = ($this->model instanceof Notification) ? $this->model : new Notification();
		parent::init();
		NotificationAsset::register($this->getView());
	}
	
	public function run()
	{
		switch(($this->model instanceof Notification))
		{
			case true:
			$searchModel = new NotificationSearch;
			$searchModel->queryOptions['with'] = ['user'];
			$get = \Yii::$app->request->getQueryParams();
			$params = $get;
			unset($params['type'], $params['id']);
	
			$dataProvider = $searchModel->search([$this->model->formName() => $params]);
			$dataProvider->query->andWhere([
				'read' => false
			]);
			$dataProvider->setSort([
				'defaultOrder' => [
					'created_at' => SORT_DESC,
				]
			]);
			$dataProvider->query->andWhere([
				'user_id' => \Yii::$app->user->getId()
			]);
			$alerts = $this->getView()->render('@nitm/widgets/views/alerts/notifications', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
				'widget' => $this,
			]);
			break;
		}
		return $alerts;
	}
}
?>