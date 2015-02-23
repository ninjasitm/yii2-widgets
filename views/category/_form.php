<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

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
			echo $form->field($model, 'parent_id')->widget(\yii\jui\AutoComplete::className(), [
				'name' => 'parent_id_autocomplete',
				'options' => [
					'value' => '',
					'class' => 'form-control',
					'id' => 'categories_parent',
					'role' => 'autocompleteSelect',
					'data-real-input' => "#categories-parent_ids"
				],
				'clientOptions' => [
					'source' => '/api/autocomplete/category/true',
				]
			]);
		?>
		<?php
			$value = is_null($model->parent()) ? '' : $model->parent()->name;
			if($value)
			{
				$currentParent = Html::label("Current Parent", '', ['class' => 'col-lg-2 control-label']);
				$currentParent .= Html::tag('h5', $value, ['class' => 'col-lg-10']);
				echo Html::tag('div', $currentParent, ['class' => 'form-group']); 
			}
		?>
		<?= Html::activeHiddenInput($model, 'parent_ids'); ?>
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