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
use nitm\models\Replies;
use nitm\models\Issues as IssuesModel;
use nitm\models\search\Issues as IssuesSearch;
use nitm\widgets\models\BaseWidget;
use nitm\widgets\issueTracker\assets\Asset as IssueAsset;

/**the issues associated with a request with support for solving them
 */
class IssueTracker extends BaseWidget
{
	public $enableComments;
	/*
	 * HTML options for generevision the widget
	 */
	public $options = [
		'class' => 'issues row',
		'role' => 'entityIssues',
		'id' => 'issues',
		'style' => 'font-size:smaller;'
	];
	
	public function init()
	{
		switch(1)
		{
			case $this->parentType == 'all':
			$this->model = new IssuesModel();
			break;
			
			case !($this->model instanceof IssuesModel) && (($this->parentType == null) || ($this->parentId == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof IssuesModel) ? $this->model : IssuesModel::findModel([$this->parentId, $this->parentType]);
			break;
		}
		assets\Asset::register($this->getView());
		parent::init();
	}
	
	public function run()
	{
		$dataProvdier = null;
		$searchModel = new IssuesSearch;
		$searchModel->addWith($this->model->withThese);
		$get = \Yii::$app->request->getQueryParams();
		$params = array_merge($get, $this->model->constraints);
		switch(is_array($this->items) && !empty($this->items))
		{
			case true:
			$dataProvider = new \yii\data\ArrayDataProvider(["allModels" => $this->items]);
			break;
			
			default:
			switch(($this->model instanceof IssuesModel))
			{
				case true:
				switch(empty($this->parentId))
				{
					/**
					 * This issue model was initialed through a model
					 * We need to set the parentId and parentType from the constraints values
					 */
					case true:
					//$this->parentId = $this->model->constraints['parent_id'];
					//$this->parentType = $this->model->constrain['parent_type'];
					break;
				}
				unset($params['type']);
				unset($params['id']);
		
				$dataProvider = $searchModel->search(array_merge($params));
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
			$dataProviderOpen = $searchModel->search(array_replace($params, ['closed' => 0]));
			$dataProviderClosed = $searchModel->search(array_replace($params, ['closed' => 1]));
			$dataProviderDuplicate = $searchModel->search(array_replace($params, ['duplicate' => 1]));
			$dataProviderResolved = $searchModel->search(array_replace($params, ['resolved' => 1]));
			$dataProviderUnresolved = $searchModel->search(array_replace($params, ['resolved' => 0]));
			$dataProviderOpen->query->orderBy(['id' => SORT_DESC]);
			$dataProviderClosed->query->orderBy(['closed_at' => SORT_DESC]);
			$issues = $this->render('@nitm/views/issue/index', [
				'dataProviderOpen' => $dataProviderOpen,
				'dataProviderClosed' => $dataProviderClosed,
				'dataProviderResolved' => $dataProviderResolved,
				'dataProviderUnresolved' => $dataProviderUnresolved,
				'dataProviderDuplicate' => $dataProviderDuplicate,
				'searchModel' => $searchModel,
				'parentId' => $this->parentId,
				'parentType' => $this->parentType,
				'useModal' => $this->useModal,
				'enableComments' => $this->enableComments
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
