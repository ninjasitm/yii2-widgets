<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use nitm\helpers\Icon;
use nitm\widgets\activityIndicator\ActivityIndicator;
use nitm\widgets\models\Issues;

/**
 * @var yii\web\View $this
 * @var app\models\Issues $model
 */

//$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Issues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$enableComments = isset($enableComments) ? $enableComments : \Yii::$app->request->get(Issues::COMMENT_PARAM);
if($enableComments == true) $repliesModel = new \nitm\widgets\models\Replies([
	"constrain" => [$model->getId(), $model->isWhat()]
]);
$uniqid = uniqid();
?>
<div id="issue<?=$model->getId()?> issue<?= $uniqid ?>" class="issues-view <?= \nitm\helpers\Statuses::getIndicator($model->getStatus())?>" style="color:auto;border-bottom: solid thin gray" role="statusIndicator<?=$model->getId()?>">
	<div class="wrapper">
	<div class="row">
		<div class="col-md-12 col-lg-12">
                	<h4>
						<?php if(isset($isNew) && ($isNew === true) || $model->isNew()) echo ActivityIndicator::widget();?>
                        <?= $model->title; ?>&nbsp;<span class="badge"><?= $model->status ?></span>
                    </h4>
                    <h6>by <b><?= $model->author()->fullName(true) ?></b> on <?= $model->created_at ?></h6>
			<p class="text-left"><?= $model->notes; ?></p>
		</div>
		<div class="col-md-8 col-lg-8 text-left">
			<div class="pull-left">
			<?php if($model->edits) :?>
				<i class="small  text-right">Edited by <b><?= $model->author()->fullName(true) ?></b> on <?= $model->created_at ?></i>&nbsp;
			<?php endif; ?>
			<?php if($model->resolved) :?>
				<i class="small  text-right">Resolved by <b><?= $model->resolvedBy()->fullName(true) ?></b> on <?= $model->resolved_at ?></i>&nbsp;
			<?php endif; ?>
			<?php if($model->closed) :?>
				<i class="small  text-right">Closed by <b><?= $model->closedBy()->fullName(true) ?></b> on <?= $model->closed_at ?></i>
			<?php endif; ?>
			</div>
		</div>
		<div class="col-md-4 col-lg-4 text-right">
			<?php
				echo Html::a(Icon::forAction('close', 'closed', $model), \Yii::$app->urlManager->createUrl(['/issue/close/'.$model->id]), [
					'title' => Yii::t('yii', ($model->closed ? 'Open' : 'Close').' '),
					'class' => 'fa-2x',
					'role' => 'metaAction closeAction',
					'data-parent' => 'tr',
					'data-pjax' => '0',
				]);
				echo Html::a(Icon::forAction('update', null, $model), \Yii::$app->urlManager->createUrl(['/issue/form/update/'.$model->id, Issues::COMMENT_PARAM => $enableComments, '__format' => 'html']), [
					'title' => Yii::t('yii', 'Edit '),
					'class' => 'fa-2x'.($model->closed ? ' hidden' : ''),
					'role' => 'updateIssueTrigger disabledOnClose',
				]);
				echo Html::a(Icon::forAction('resolve', 'resolved', $model), \Yii::$app->urlManager->createUrl(['/issue/resolve/'.$model->id]), [
					'title' => Yii::t('yii', ($model->resolved ? 'Unresolve' : 'Resolve').' '),
					'class' => 'fa-2x'.($model->closed ? ' hidden' : ''),
					'role' => 'metaAction resolveAction disabledOnClose',
					'data-parent' => 'tr',
					'data-pjax' => '0',
				]);
				echo Html::a(Icon::forAction('duplicate', 'duplicate', $model), \Yii::$app->urlManager->createUrl(['/issue/duplicate/'.$model->id]), [
					'title' => Yii::t('yii', ($model->duplicate ? 'Flag as not duplicate' : 'flag as duplicate').' '),
					'class' => 'fa-2x',
					'role' => 'metaAction duplicateAction',
				]);
				if($enableComments==true)
				{
					echo Html::a(Icon::forAction('comment', null, null, ['size' => '2x']).ActivityIndicator::widget(['position' => 'top right', 'size' => 'small', 'text' => $repliesModel->count(), 'type' => 'info']), \Yii::$app->urlManager->createUrl(['/reply/index/'.$model->isWhat().'/'.$model->getId(), '__format' => 'html']), [
						'id' => 'issue-comment-link'.$uniqid,
						'title' => 'See comments for this issue',
						'data-id' => '#issue-comments'.$uniqid,
						'onclick' => '(function (event) {event.preventDefault(); $nitm.module("tools").visibility("#issue-comment-link'.$uniqid.'", true);})(event)'
					]);
				}
			?>
		</div>
		<?php if($enableComments==true): ?>
		<div class="col-lg-12 col-md-12">
			<div class="clear" style="display:none;" id="issue-comments<?=$uniqid;?>">
			</div>
		</div>
		<?php endif; ?>
		<br>
	</div>
	</div>
</div>
