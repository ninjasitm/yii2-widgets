<?php

use yii\helpers\Html;
use yii\grid\GridView;
use nitm\helpers\Icon;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\search\Categories $searchModel
 */

$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="categories-index">

	<?= yii\widgets\Breadcrumbs::widget([
		'links' => $this->params['breadcrumbs']
	]); ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $createButton; ?>
    </p>
    <p>
        <?= $filterButton; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
				'format' => 'html',
				'attribute' => 'parent_ids',
				'value' => function ($model) {
					//return $model->url('parent_ids', [$model->parent(), 'name']);
					
					return \nitm\widgets\ajax\Dropdown::widget([
						'name' => 'parent_id_autocomplete',
						'options' => [
							'multiple' => true,
							'value' => '',
							'class' => 'form-control',
							'id' => 'categories_parent',
							'role' => 'autocompleteSelect',
							'data-real-input' => "#categories-parent_ids"
						],
						'url' => '/api/autocomplete/category/true'
					]);
				}
			],
            'name',
            'slug',
            // 'created_at',
            // 'updated_at',

			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view} {form/update} {disable}',
				'buttons' => [
					'form/update' => function ($url, $model) {
						return \nitm\widgets\modal\Modal::widget([
							'toggleButton' => [
								'tag' => 'a',
								'label' => Icon::forAction('update'), 
								'href' => \Yii::$app->urlManager->createUrl([$url, '__format' => 'modal']),
								'title' => Yii::t('yii', 'Edit '),
								'role' => 'disabledOnClose',
								'data-pjax' => 0,
								'class' => $model->disabled ? 'hidden' : ''

							],
						]);
					},

					'view' => function ($url, $model) {
						return \nitm\widgets\modal\Modal::widget([
							'size' => 'large',
							'toggleButton' => [
								'tag' => 'a',
								'label' => Icon::forAction('view'), 
								'href' => \Yii::$app->urlManager->createUrl([$url, '__format' => 'modal']),
								'title' => Yii::t('yii', 'View '.$model->name),
								'role' => 'disabledOnClose',
								'data-pjax' => 0,
								'class' => $model->disabled ? 'hidden' : ''
							],
						]);
					},
					'disable' => function ($url, $model) {
						return Html::a(Icon::forAction('disable', 'disabled', $model), $url, [
							'title' => Yii::t('yii', 'Disable mood: '.$model->name),
							'role' => 'metaAction disableAction',
							'data-parent' => '#'.$model->isWhat().$model->getId(),
							'data-pjax' => 0,
							'data-method' => 'post'
						]);
					},
				],
"urlCreator" => function ($action, $model) {
					$csrfVar = \Yii::$app->request->csrfParam;
					$csrfToken = \Yii::$app->request->getCsrfToken();
					$params = [
						"/".$model->isWhat().'/'.$action."/".$model->id
					];
					return \yii\helpers\Url::toRoute($params);
				}
			],
        ],
		'options' => [
			'id' => 'categories'
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
			'container' => '#categories',
			'item' => ".item",
			'negativeMargin' => 150,
			'delay' => 500,
		]
    ]); ?>

</div>
