<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

/**
 * @var yii\web\View $this
 * @var lab1\models\Country $model
 * @var yii\widgets\ActiveForm $form
 */
$formOptions = array_replace_recursive($formOptions, [
	"type" => ActiveForm::TYPE_HORIZONTAL,
	'options' => [
		"role" => "ajaxForm"
	],
]);
 
?>

<div id="<?= $model->isWhat()?>_form_container">

    <?php $form = include(\Yii::getAlias("@nitm/views/layouts/form/header.php")); ?>
	
    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'code')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
