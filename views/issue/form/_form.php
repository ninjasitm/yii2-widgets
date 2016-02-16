<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use nitm\widgets\models\Issues;

/**
 * @var yii\web\View $this
 * @var app\models\Issues $model
 * @var yii\widgets\ActiveForm $form
 */

$uniqid = uniqid();
$action = ($model->getIsNewRecord()) ? "create" : "update";
$enableComments = isset($enableComments) ? $enableComments : \Yii::$app->request->get(Issues::COMMENT_PARAM);
?>

<div class="issues-form row" id='issues-form<?=$uniqid?>'>
	<div class="col-lg-12 col-md-12">
		<?= \nitm\widgets\alert\Alert::widget(); ?>

		<?php $form = ActiveForm::begin([
			"type" => ActiveForm::TYPE_VERTICAL,
			'action' => \Yii::$app->urlManager->createUrl(['/issue/'.$action.($model->getIsNewRecord() ? "" : "/".$model->getId()), Issues::COMMENT_PARAM => $enableComments]),
			'options' => [
				"role" => $action."Issue",
				'id' => 'issue-'.$action.'-form'.$uniqid
			],
			'fieldConfig' => [
				'inputOptions' => ['class' => 'form-control'],
				'labelOptions' => ['class' => 'control-label'],
			],
			'enableAjaxValidation' => true
		]); ?>

		<?= $form->field($model, 'title', [
				'addon' => [
					'prepend' => [
						'content' => \nitm\widgets\priority\Priority::widget([
							'type' => 'addon',
							'size' => 'small',
							'inputsInline' => true,
							'addonType' => 'radiolist',
							'attribute' => 'status',
							'model' => $model,
							'form' => $form,
							'options' => [
								'style' => 'width: 200px'
							]
						]),
						'asButton' => true,
					],
					'groupOptions' => [
						'class' => 'input-group input-group-sm'
					]
				],
				'options' => [
					'id' => 'chat-message-title'.$uniqid,
				]
			])->textInput([
				'placeholder' => "Title for this issue",
				'tag' => 'span'
			])->label("Title", ['class' => 'sr-only']); ?>

		<?= $form->field($model, 'notes')->textarea()->label("Issue", ['class' => 'sr-only']) ?>
		<?php
			if($model->isNewRecord)
			{
				echo Html::activeHiddenInput($model, 'parent_id', ['value' => $parentId]);
				echo Html::activeHiddenInput($model, 'parent_type', ['value' => $parentType]);
			}
		?>

		<div class="pull-right">
			<?= Html::submitButton(ucfirst($action), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
<br>
