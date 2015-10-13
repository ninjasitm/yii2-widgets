<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use nitm\helpers\Icon;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var lab1\models\search\Country $searchModel
 */

$this->title = Yii::t('app', 'Countries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="country-index">
	<?= yii\widgets\Breadcrumbs::widget([
		'links' => $this->params['breadcrumbs']
	]); ?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $createButton ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'code',
            'seq',
			[
				'class' => 'yii\grid\ActionColumn',
				'buttons' => [
					'form/update' => function ($url, $model) {
						return \nitm\widgets\modal\Modal::widget([
							'toggleButton' => [
								'tag' => 'a',
								'label' => Icon::forAction('update'), 
								'href' => \Yii::$app->urlManager->createUrl([$url, '__format' => 'modal']),
								'title' => Yii::t('yii', 'Edit '),
								'class' => 'fa-2x',
								'role' => 'dynamicAction updateAction disabledOnClose',
							],
						]);
					},
					'delete' => function ($url, $model) {
						return Html::a(Icon::forAction('delete'), \Yii::$app->urlManager->createUrl([$url]), [
							'title' => Yii::t('yii', 'Delete'),
							'class' => 'fa-2x',
							'role' => 'metaAction deleteAction',
							'data-parent' => 'tr',
							'data-pjax' => '0',
						]);
					},
					'view' => function ($url, $model) {
						return \nitm\widgets\modal\Modal::widget([
							'toggleButton' => [
								'tag' => 'a',
								'label' => Icon::forAction('view'), 
								'href' => \Yii::$app->urlManager->createUrl([$url, '__format' => 'modal']),
								'title' => Yii::t('yii', 'View '),
								'class' => 'fa-2x',
								'role' => 'dynamicAction viewAction disabledOnClose',
							],
						]);
					},
				],
				'template' => "{form/update} {delete} {view}",
				'urlCreator' => function($action, $model, $key, $index) {
					return '/'.$model->isWhat().'/'.$action.'/'.$model->getId();
				},
			],
        ],
		'options' => [
			'id' => 'countries'
		],
		'rowOptions' => function ($model, $key, $index, $grid)
		{
			return [
				"class" => 'item'
			];
		},
		'pager' => [
			'class' => \nitm\widgets\ias\ScrollPager::className(),
			'overflowContainer' => '.content',
			'container' => '#countries',
			'item' => ".item",
			'negativeMargin' => 150,
			'delay' => 500,
		]
    ]); ?>

</div>
