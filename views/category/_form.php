<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var common\models\Categories $model
 * @var yii\widgets\ActiveForm $form
 */
$formOptions = array_replace_recursive($formOptions, [
	"type" => ActiveForm::TYPE_HORIZONTAL,
	'options' => [
		"role" => "ajaxForm"
	],
]);
?>

<div id="<?= $model->isWhat()?>_form_container" class="categories-form wrapper">
	<div class="row">
		<div class="col-md-12 col-lg-12">

    	<?php $form = include(\Yii::getAlias("@nitm/views/layouts/form/header.php")); ?>
	 
		<?php
			echo $form->field($model, 'parent_ids')->widget(\nitm\widgets\metadata\ParentListInput::className(), [
				'url' => '/api/autocomplete/category/true',
			]);
		?>
		<div class="form-group">
		<?=
			\nitm\widgets\metadata\ParentList::widget([
				'parents' => $model->parents(),
				'model' => $model,
				'labelOptions' => [
					'class' => 'text-right',
				],
				'labelContainerOptions' => [
					'class' => 'col-md-2 col-lg-2',
				],
				'containerOptions' => [
					'class' => 'col-md-10 col-lg-10',
				]
			]);
		?>
		</div>
		<?= $form->field($model, 'name') ?>
		<?= $form->field($model, 'slug') ?>
		<?php
			/*$form->field($model, 'parent_type')->widget(Select2::className(), [
				'data' => $model->getTypes(),
			])->label("Type");*/
		?>
	
		
        <?php if(!\Yii::$app->request->isAjax): ?>
        <div class="form-group pull-right">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    	<?php endif; ?>
	
    	<?php $entityType = 'entity'; include(\Yii::getAlias("@nitm/views/layouts/form/footer.php")); ?>
		</div>
	</div>
</div>