<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use nitm\helpers\Icon;
use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $model nitm\models\Alerts */

$viewUrl = \Yii::$app->urlManager->createUrl(['/alerts/view/'.$model->getId(), '__format' => 'modal']);
$updateUrl = \Yii::$app->urlManager->createUrl(['/alerts/form/update/'.$model->getId(), '__format' => 'modal']);
$deleteUrl = \Yii::$app->urlManager->createUrl(['/alerts/delete/'.$model->getId()]);
$remoteUrl = \Yii::$app->urlManager->createUrl(['/'.$model->remote_type.'/view/'.$model->remote_id, '__format' => 'modal']);

?>
<?php if(!isset($notAsListItem)): ?>
<li id="alert<?= $model->getId(); ?>" class="<?= \nitm\helpers\Statuses::getListIndicator($model->getPriority()) ?>">
<?php endif;?>
<div class="row">
	<div class="col-md-3 col-lg-3">
		<?= $model->setting('actions.'.$model->action) ?>
	</div>
	<div class="col-md-2 col-lg-2">
		<?php
			echo "<b>".$model->setting('allowed.'.$model->remote_type)."</b>";
			if(!is_null($model->remote_for) && ($model->remote_for != 'any')) echo " for <b>".$model->setting('for.'.$model->remote_for)."</b>";
			if(!is_null($model->remote_id)) {
				echo " with id <b>".$model->remote_id. "</b> ";
				echo \nitm\widgets\modal\Modal::widget([
					'size' => 'large',
					'toggleButton' => [
						'tag' => 'a',
						'label' => Icon::forAction('view'), 
						'href' => $remoteUrl,
						'title' => Yii::t('yii', 'View '),
						'role' => 'viewRemoteModel',
					],
				]);
			}
		?>
	</div>
	<div class="col-md-3 col-lg-3">
		that has a priority of <b><?= !empty($model->priority) ? $model->setting('priorities.'.$model->priority) : 'Normal' ?></b>
	</div>
	<div class="col-md-2 col-lg-2">
		alert me using <b><?= $model->methods; ?></b>
	</div>
	<div class="col-md-2 col-lg-2">
		<?= \nitm\widgets\modal\Modal::widget([
				'size' => 'large',
				'toggleButton' => [
					'tag' => 'a',
					'class' => 'btn btn-info',
					'label' => Icon::forAction('update'), 
					'href' => $updateUrl,
					'title' => Yii::t('yii', 'Update '),
					'role' => 'updateAlert',
				],
			]);
		?>
		<?= Html::a(Icon::forAction('delete'), '#', [
			'class' => 'btn btn-danger',
			'role' => 'removeAlert',
			'data-action' => $deleteUrl,
			'data-parent' => "#alert".$model->getId(),
			]); ?>
	</div>
</div>
<?php if(!isset($notAsListItem)): ?>
</li>
<?php endif; ?>
