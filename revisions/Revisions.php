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
		switch(1)
		{
			case !($this->model instanceof RevisionsModel) && (($this->parentType == null) || ($this->parentId == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof RevisionsModel) ? $this->model : RevisionsModel::findModel([$this->parentId, $this->parentType]);
			break;
		}
		parent::init();
	}
	
	public function run()
	{
		switch(\nitm\models\User::isAdmin())
		{
			case true:
			break;
		}
		$revisionsTabs = Html::tag('ul', 
			Html::tag('li', Html::a('Open', '#open-issues', ['data-toggle' => 'tab'], ['class' => 'active']), 
			Html::tag('li', Html::a('Closed', '#closed-issues', ['data-toggle' => 'tab']), 
		);
		$revisionsOpen = $this->getIssues('open');
		$revisionsClosed = $this->getIssues('closed');
		$revisions = Html::tag('div',
			Html::tag('div', $revisionsOpen, ['class' => 'tab-pane fade in active', 'id' => 'open-issues']),
			Html::tag('div', $revisionsClosed, ['class' => 'tab-pane fade in', 'id' => 'closed-issues'])
			['class' => 'tab-content']
		);
		$this->options['id'] .= $this->parentId;
		return Html::tag('div', $revisionsTabs.$revisions, $this->options);
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
	
	protected function getIssues($status='open')
	{
		switch($status)
		{
			case 'closed':
			$this->model->queryFilters['closed'] = 1;
			break;
			
			default:
			$this->model->queryFilters['closed'] = 0;
			break;
		}
		
		$dataProvider = new ArrayDataProvider([
			'allModels' => $this->model->getModels(),
			'pagination' => false,
		]);
		$this->model->queryFilters['order_by'] = ['id' => SORT_DESC];
		return GridView::widget([
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
				[
					'attribute' => 'parent_type',
					'label' => 'Type',
				],
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
					"class" => \nitm\helpers\Statuses::getIndicator($model->getStatus()),
				];
			},
			"tableOptions" => [
					'class' => 'table'
			],
		]);
	}
}
?>