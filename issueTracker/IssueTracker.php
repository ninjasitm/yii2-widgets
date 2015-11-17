<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\issueTracker;

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\icons\Icon;
use nitm\widgets\models\Replies;
use nitm\widgets\models\Issues as IssuesModel;
use nitm\widgets\models\search\Issues as IssuesSearch;
use nitm\widgets\BaseWidget;

/**the issues associated with a request with support for solving them
 */
class IssueTracker extends BaseWidget
{
	public $enableComments;
	/*
	 * HTML options for generevision the widget
	 */
	public $options = [
		'class' => 'issues',
		'role' => 'entityIssues',
		'id' => 'issues',
		'style' => 'font-size:smaller;'
	];

	public function init()
	{
		parent::init();
		switch(1)
		{
			case $this->parentType == 'all':
			$this->model = new IssuesModel();
			break;

			case !($this->model instanceof IssuesModel) && (($this->parentType == null) || ($this->parentId == null)):
			$this->model = null;
			break;

			default:
			$this->model = ($this->model instanceof IssuesModel) ? $this->model : (new IssuesModel(['initSearchClass' => false]))->findModel([$this->parentId, $this->parentType]);
			break;
		}
		$this->options = array_merge($this->defaultOptions(), $this->options);
		$this->options['id'] .= $this->uniqid;
		Asset::register($this->getView());
	}

	public function run()
	{
		$dataProvdier = null;
		$searchModel = new IssuesSearch([
			'queryOptions' => [
				'with' => ['author', 'editor', 'closedBy', 'resolvedBy']
			]
		]);
		$get = \Yii::$app->request->getQueryParams();
		$params = array_merge($get, $this->model->getConstraints());
		$params['closed'] = false;
		switch(is_array($this->items) && !empty($this->items))
		{
			case true:
			$dataProvider = new \yii\data\ArrayDataProvider(["allModels" => $this->items]);
			break;

			default:
			switch(($this->model instanceof IssuesModel))
			{
				case true:
				unset($params['type'], $params['id']);
				$dataProvider = $searchModel->search([$this->model->formName() => $params]);
				break;
			}
			break;
		}
		switch(is_null($dataProvider))
		{
			case false:
			$dataProvider->setSort([
				'defaultOrder' => [
					'id' => SORT_DESC,
				]
			]);
			$dataProviderOpen = $searchModel->search(array_replace($params, ['closed' => false]));
			$dataProviderClosed = $searchModel->search(array_replace($params, ['closed' => true]));
			$dataProviderDuplicate = $searchModel->search(array_replace($params, ['duplicate' => true]));
			$dataProviderResolved = $searchModel->search(array_replace($params, ['resolved' => true]));
			$dataProviderUnresolved = $searchModel->search(array_replace($params, ['resolved' => false]));
			$dataProviderOpen->query->orderBy(['id' => SORT_DESC]);
			$dataProviderClosed->query->orderBy(['closed_at' => SORT_DESC]);
			$issues = $this->render('@nitm/widgets/views/issue/index', [
				'dataProviderOpen' => $dataProviderOpen,
				'dataProviderClosed' => $dataProviderClosed,
				'dataProviderResolved' => $dataProviderResolved,
				'dataProviderUnresolved' => $dataProviderUnresolved,
				'dataProviderDuplicate' => $dataProviderDuplicate,
				'searchModel' => $searchModel,
				'parentId' => $this->parentId,
				'parentType' => $this->parentType,
				'useModal' => $this->useModal,
				'enableComments' => $this->enableComments,
			]);
			break;

			default:
			$issues = 'No Issues';
			break;
		}
		$this->options['id'] .= $this->parentId;
		return Html::tag('div', $issues, $this->options);
	}
}
