<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var lab1\models\Country $model
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
  'modelClass' => 'Country',
]) . ' ' . $model->title();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Countries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="country-update">

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
