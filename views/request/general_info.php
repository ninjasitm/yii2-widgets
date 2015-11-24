<?php

use\yii\grid\GridView;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use nitm\helpers\Icon;

echo GridView::widget([
	'showFooter' => false,
	'summary' => '',
	'dataProvider' => new ArrayDataProvider([
		'allModels' => [$model],
		'pagination' => false,
	]),
	'columns' => [
		[
			'attribute' => 'id',
			'label' => 'ID',
			'options' => [
				'rowspan' => 3
			]
		],
		[
			'attribute' => 'rating',
			'label' => '%',
			'format' => 'raw',
			'value' => function ($model, $index, $widget) {
				$rating = Html::tag('div',
					\nitm\widgets\vote\Vote::widget([
						'size' => 'large',
						'model' => $model->vote(),
						'parentType' => $model->isWhat(),
						'parentId' => $model->getId(),
					])
				);
				return $rating;
			},
			'options' => [
				'rowspan' => 3,
				'class' => 'col-md-2 col-lg-2'
			]
		],
		[
			'attribute' => 'author',
			'label' => 'Author',
			'format' => 'html',
			'value' => function ($model, $index, $widget) {
				return $model->author()->url(\Yii::$app->getModule('nitm')->useFullnames, \Yii::$app->request->url, [$model->formname().'[author]' => $model->author()->getId()]);
			}
		],
		'closed:boolean',
		'completed:boolean',
		[
			'class' => 'yii\grid\ActionColumn',
			'buttons' => [
				'close' => function ($url, $model) {
					return Html::a(Icon::forAction('close', 'closed', $model), $url, [
						'title' => Yii::t('yii', ($model->closed ? 'Open' : 'Close').' '.$model->title),
						'class' => 'fa-2x',
						'role' => 'metaAction closeAction',
						'data-parent' => 'tr',
						'data-pjax' => '0',
					]);
				},
				'complete' => function ($url, $model) {
					return Html::a(Icon::forAction('resolve', 'completed', $model), $url, [
						'title' => Yii::t('yii', ($model->completed ? 'Incomplete' : 'Complete').' '.$model->title),
						'class' => 'fa-2x',
						'role' => 'metaAction resolveAction disabledOnClose',
						'data-parent' => 'tr',
						'data-pjax' => '0',
					]);
				}
			],
			'template' => "{complete} {close}",
			'urlCreator' => function($action, $model, $key, $index) {
				return '/'.$this->context->id.'/'.$action.'/'.$model->getId();
			},
			'options' => [
				'rowspan' => 3
			]
		],
	],
	'rowOptions' => function ($model, $key, $index, $grid)
	{
		return [
			"class" => \nitm\helpers\Statuses::getIndicator($model->getStatus()),
			'role' => 'statusIndicator'.$model->getId()
		];
	},
	"tableOptions" => [
		'class' => 'table table-bordered'
	],
	'afterRow' => function ($model, $key, $index, $grid) {

		$shortLink = \nitm\widgets\metadata\ShortLink::widget([
			'url' => \Yii::$app->urlManager->createAbsoluteUrl([$model->isWhat().'/view/'.$model->getId()]),
			'viewOptions' => [
				'data-toggle' => 'modal',
				'data-target' => '#view'
			]
		]);

		$statusInfo = \nitm\widgets\metadata\StatusInfo::widget([
			'items' => [
				[
					'blamable' => $model->editor(),
					'date' => $model->updated_at,
					'value' => $model->edits,
					'label' => [
						'true' => "Updated ",
						'false' => "No updates"
					]
				],
				[
					'blamable' => $model->completedBy(),
					'date' => $model->completed_at,
					'value' => $model->completed,
					'label' => [
						'true' => "Completed ",
						'false' => "Not completed"
					]
				],
				[
					'blamable' => $model->closedBy(),
					'date' => $model->closed_at,
					'value' => $model->closed,
					'label' => [
						'true' => "Closed ",
						'false' => "Not closed"
					]
				],
			],
		]);

		//Extra information section
		$follow = \nitm\widgets\alerts\Follow::widget([
			'model' => $model->follow(),
			'buttonOptions' => [
				'size' => 'normal'
			]
		]);
		$files = \nitm\filemanager\widgets\FilesCount::widget([
			'model' => $model->file(),
			"parentId" => $model->getId(),
			"parentType" => $model->isWhat(),
			'fullDetails' => false,
		]);
		$images = \nitm\filemanager\widgets\ImagesCount::widget([
			'model' => $model->image(),
			"parentId" => $model->getId(),
			"parentType" => $model->isWhat(),
			'fullDetails' => false,
		]);

		$activityInfo = "<br>".Html::tag('div',
			Html::tag('div', $files, ['class' => 'col-md-4 col-lg-4 col-sm-4 center-block']).
			Html::tag('div', $images, ['class' => 'col-md-4 col-lg-4 col-sm-4 center-block']).
			Html::tag('div', $follow, ['class' => 'col-md-4 col-lg-4 col-sm-4 center-block']), [
			'class' => 'center-block clearfix',
			'style' => 'margin-bottom: 15px'
		]);

		$metaInfo = empty($statusInfo) ? $shortLink : $statusInfo.$shortLink;
		/*$issues = $this->context->issueCountWidget([
			"model" => $model->issues,
		]);*/
		return Html::tag('tr',
			Html::tag(
				'td',
				$metaInfo.$activityInfo,
				[
					'colspan' => 6,
					'rowspan' => 1,
					"class" => \nitm\helpers\Statuses::getIndicator($model->getStatus()),
					'role' => 'statusIndicator'.$model->getId()
				]
			)
		);
	}
]); ?>
