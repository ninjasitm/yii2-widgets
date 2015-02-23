<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var pickledup\models\Category $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
  'modelClass' => 'Category',
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', $model->properName()), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="<?= $formOptions['container']['id']; ?>" class="<?= $formOptions['container']['class']?> ">
	<?php if(!\Yii::$app->request->isAjax): ?>
	<?= \yii\widgets\Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]); ?>
	<h2><?= Html::encode($this->title) ?></h2>
	<?php endif; ?>
	
    <?= $this->render('_form', [
        'model' => $model,
		'formOptions' => $formOptions,
		'scenario' => $scenario,
		'action' => $action,
		'type' => $type
    ]) ?>
</div>