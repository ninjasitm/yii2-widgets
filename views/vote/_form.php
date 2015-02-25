<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var nitm\module\models\Vote $model
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

    <?= $form->field($model, 'id') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
