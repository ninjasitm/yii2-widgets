<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use nitm\models\Notification;

/* @var $this yii\web\View */
/* @var $searchModel nitm\models\search\Notification */
/* @var $dataProvider yii\data\ActiveDataProvider */
$itemOptions = [
	'id' => 'notification'.$model->getId(),
	'class' => \nitm\helpers\Statuses::getListIndicator($model->getPriority())
];

$activityIndicator = ((isset($isNew) && ($isNew === true) || $model->isNew()) ? \nitm\widgets\activityIndicator\ActivityIndicator::widget() : '');
$closeButton = Html::button(
	Html::tag('span', '&times;', ['aria-hidden' => true]), [
		'class' => 'close',
		'onclick' => '$.post("/alerts/mark-notification-read/'.$model->getId().'", function () {$("#'.$itemOptions['id'].'").remove()});',
		'data-parent' => '#notification'.$model->getId()
]);

echo Html::tag('div', 
		$activityIndicator
		.Html::tag('p', 
			$model->message.$closeButton, [
			'class' => 'list-group-item-text',
			'style' => 'white-space: normal'
		]),
	$itemOptions);
