<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\revisions;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use nitm\models\User;
use nitm\models\Revisions as RevisionsModel;
use nitm\widgets\models\BaseWidget;
use kartik\icons\Icon;

class Revisions extends BaseWidget
{
	/*
	 * HTML options for generevision the widget
	 */
	public $options = [
		'class' => 'revision',
		'role' => 'entityRevisions',
		'id' => 'revision',
	];
	
	/**
	 * The actions that are supported
	 */
	private $_actions = [
		'view' => [
			'tag' => 'span',
			'action' => '/revisions/view',
			'text' => 'eye',
			'options' => [
				'class' => 'col-md-4 col-lg-4',
				'role' => 'viewRevision',
				'id' => 'revision-view',
				'title' => 'View this version',
				'data-toggle' => 'modal',
				'data-target' => '#revisions-view-modal'
			]
		],
		'restore' => [
			'tag' => 'span',
			'action' => '/revisions/restore',
			'text' => 'reply',
			'options' => [
				'class' => 'col-md-4 col-lg-4',
				'role' => 'dynamicAction',
				'id' => 'revision-restore',
				'title' => 'Restore this version'
			]
		],
		'delete' => [
			'tag' => 'span',
			'action' => '/revisions/delete',
			'text' => 'trash-o',
			'options' => [
				'class' => 'col-lg-4 col-md-4',
				'role' => 'dynamicAction',
				'id' => 'revision-delete',
				'title' => 'Delete this version'
			]
		],
	];
	
	public function init()
	{
		if (!($this->model instanceof RevisionsModel) && ($this->parentType == null) || ($this->parentId == null)) {
			$this->model = null;
		}
		else 
		{
			$this->model = ($this->model instanceof RevisionsModel) ? $this->model : new RevisionsModel([
				"constrain" => [$this->parentId, $this->parentType]
			]);
		}
		parent::init();
	}
	
	public function run()
	{
		$r->queryFilters['order_by'] = ['id' => SORT_DESC];
		switch(\nitm\models\User::isAdmin())
		{
			case true:
			break;
		}
		
		$dataProvider = new ArrayDataProvider([
			'allModels' => $r->getModels(),
			'pagination' => false,
		]);
		$revisions = GridView::widget([
			'dataProvider' => $dataProvider,
			//'filterModel' => $searchModel,
			'columns' => [
				[
					'attribute' => 'author',
					'label' => 'Author',
					'format' => 'html',
					'value' => function ($model, $index, $widget) {
						return Html::a($model->authorUser->getFullName(true, $model->authorUser), \Yii::$app->urlManager->createUrl(['', 'Revisions[author]' => $model->authorUser->id]));
					}
				],
				'created_at',
				'parent_type',
				[
					'class' => 'yii\grid\ActionColumn',
					'buttons' => $this->getActions(),
					'template' => "{view} {restore} {delete}",
					'urlCreator' => function($action, $model, $key, $index) {
						return \Yii::$app->controller->id.'/'.$action.'/'.$model->getId();
					},
					'options' => [
						'rowspan' => 3
					]
				],
			],
			'rowOptions' => function ($model, $key, $index, $grid)
			{
				return [
					"class" => \Yii::$app->controller->getStatusIndicator($model),
				];
			},
			"tableOptions" => [
					'class' => 'table'
			],
		]);
		$this->options['id'] .= $this->parentId;
		echo Html::tag('div', $revisions, $this->options);
	}
	
	public function getActions()
	{
		$actions = is_null($this->actions) ? $this->_actions : array_intersect_key($this->_actions, $this->actions);
		$ret_val = '';
		foreach($actions as $name=>$action)
		{
			switch(isset($action['adminOnly']) && ($action['adminOnly'] == true))
			{
				case true:
				switch(\Yii::$app->userMeta->isAdmin())
				{
					case true:
					$action['options']['id'] = $action['options']['id'].$this->parentId;
					$ret_val[$name] = function ($url, $model) use($action) {
						return Html::a(Icon::show($action['text']), $action['action'].'/'.$model->getId(), $action['options']);
					};
					break;
				}
				break;
				
				default:
				$action['options']['id'] = $action['options']['id'].$this->parentId;
				$ret_val[$name] = function ($url, $model) use($action) {
					return Html::a(Icon::show($action['text']), $action['action'].'/'.$model->getId(), $action['options']);
				};
				break;
			}
			
		}
		return $ret_val;
	}
}
?>