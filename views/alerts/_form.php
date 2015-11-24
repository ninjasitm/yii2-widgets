<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;

/* @var $this yii\web\View */
/* @var $model nitm\models\Alerts */
/* @var $form kartik\widgets\ActiveForm */
$action = $model->getIsNewRecord() ? 'create' : 'update';
$model->setScenario($action);
$uniqid = uniqid();
$formOptions = array_replace_recursive($formOptions, [
	"type" => ActiveForm::TYPE_INLINE,
	'enableAjaxValidation' => true,
	'fieldConfig' => [
		'inputOptions' => ['class' => 'form-control'],
		'template' => "{label}\n<div class=\"\">{input}</div>\n<div class=\"col-lg-12\">{error}</div>",
		'labelOptions' => ['class' => 'sr-only'],
	],
	'action' => '/alerts/'.$action.($action == 'create' ? '' : '/'.$model->getId()),
	'options' => [
		"role" => $action."Alert"
	],
]);

?>

<div id="<?= $model->isWhat()?>_form_container" class="row">
	<div class="col-md-12 col-lg-12">
    <?php $form = include(\Yii::getAlias("@nitm/views/layouts/form/header.php")); ?>
	<?=
		$form->field($model, 'action', [
				'options' => [
					'class' => 'col-lg-3 col-md-3 col-sm-6'
				]
			])->widget(Select2::className(), [
			'data' => $model->setting('actions'),
			'theme' => Select2::THEME_KRAJEE,
			'options' => [
				'id' => 'alert-action'.$uniqid,
				'placeholder' => 'Alert me...',
				"allowClear" => true,
				'class' => 'col-sm-6'
			]
		])->label("Action");
	?>
	<?=
		$form->field($model, 'remote_type', [
				'options' => [
					'class' => 'col-lg-2 col-md-3 col-sm-6'
				]
			])->widget(DepDrop::className(), [
			'value' => $model->remote_type,
			'data' => [$model->remote_type => $model->properName($model->remote_type)],
			'options' => [
				'placeholder' => ' select something ',
				'id' => 'alert-type'.$uniqid
			],
			'type' => DepDrop::TYPE_SELECT2,
			'select2Options'=>[
				'id' => 'alert-remote-type'.$uniqid,
				'pluginOptions'=>['allowClear'=>true]
			],
			'pluginOptions'=>[
				'depends'=>['alert-action'.$uniqid],
				'url' => Url::to(['/alerts/list/types']),
				'loadingText' => '...',
				'placeholder' => ' type of '
			]
		])->label("Remote Type");
	?>
	<?=
		$form->field($model, 'remote_for', [
				'options' => [
					'class' => 'col-lg-2 col-md-3 col-sm-6'
				]
			])->widget(DepDrop::className(), [
			'value' => $model->remote_for,
			'data' => [$model->remote_for => $model->properName($model->remote_for)],
			'options' => [
				'placeholder' => ' for ',
				'id' => 'alert-for'.$uniqid
			],
			'type' => DepDrop::TYPE_SELECT2,
			'select2Options'=>['id' => 'alert-remote-type'.$uniqid, 'pluginOptions'=>['allowClear'=>true]],
			'pluginOptions'=>[
				'depends'=>['alert-type'.$uniqid],
				'url' => Url::to(['/alerts/list/for']),
				'loadingText' => '...',
				'placeholder' => ' and it\'s for a/an '
			]
		])->label("Remote For");
	?>
	<?=
		$form->field($model, 'priority', [
				'options' => [
					'class' => 'col-lg-2 col-md-3 col-sm-6'
				]
			])->widget(DepDrop::className(), [
			'value' => $model->priority,
			'data' => [$model->priority => $model->properName($model->priority)],
			'options' => ['placeholder' => ' and it if has a priority of ', 'id' => 'priority'.$uniqid],
			'type' => DepDrop::TYPE_SELECT2,
			'select2Options'=>['id' => 'alert-priority'.$uniqid, 'pluginOptions'=>['allowClear'=>true]],
			'pluginOptions'=>[
				'depends'=>['alert-type'.$uniqid],
				'url' => Url::to(['/alerts/list/priority']),
				'loadingText' => '...',
				'placeholder' => ' and has a proiority of '
			]
		])->label("Priority");
	?>
	<?=
		$form->field($model, 'methods', [
				'options' => [
					'class' => 'col-lg-2 col-md-3 col-sm-6'
				]
			])->widget(Select2::className(), [
			'value' => explode(',', $model->methods),
			'options' => ['id' => 'alert-methods'.$uniqid, 'placeholder' => ' then alert me using'],
			'data' => \Yii::$app->getModule('nitm')->alerts->store()->supportedMethods(),

		])->label("Priority");
	?>


	<?php if(!\Yii::$app->request->isAjax): ?>
	<div class="btn-group col-md-3 col-lg-1 col-sm-12">
		<?= Html::submitButton(ucfirst($action), [
			'class' => 'btn '.($model->isNewRecord ? 'btn-success' : 'btn-primary'),
			'style' => 'width: 100%'
		]) ?>
	</div>
	<?php endif; ?>

	<?php ActiveForm::end(); ?>
	</div>
</div><br>
<?php if(\Yii::$app->request->isAjax): ?>
<script type='text/javascript'>
$nitm.onModuleLoad('alerts', function () {
	$nitm.module('alerts').initForms('<?= $formOptions['options']['id'];?>');
});
</script>
<?php endif; ?>
