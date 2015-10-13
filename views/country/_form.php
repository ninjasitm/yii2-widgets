<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

/**
 * @var yii\web\View $this
 * @var lab1\models\Country $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div id="<?= $formOptions['options']['id']; ?>-container" class='row'>
	<?php $form = include(\Yii::getAlias("@lab1/views/layouts/form/header.php")); ?>
	
    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'code')->textarea() ?>
	
	<div class="form-group">
		<?php if(!\Yii::$app->request->isAjax): ?>
		<div class="fixed-actions text-right">
			<?= Html::submitButton($action, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		</div>
		<?php endif; ?>
	</div>

	<?php include(\Yii::getAlias("@lab1/views/layouts/form/footer.php")); ?>

</div>
