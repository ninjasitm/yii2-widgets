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
use nitm\models\Issues as IssueModel;
use nitm\models\search\Issues as IssueSearch;
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
	];
	
	public function init()
	{
		switch(1)
		{
			case !($this->model instanceof IssueModel) && (($this->parentType == null) || ($this->parentId == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof IssueModel) ? $this->model : IssueModel::findModel([$this->parentId, $this->parentType]);
			break;
		}
		assets\Asset::register($this->getView());
		parent::init();
	}
	
	public function run()
	{
		switch(($this->model instanceof IssueModel))
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
			$searchModel = new IssueSearch;
			$searchModel->withThese = ['closeUser', 'resolveUser'];
			$get = \Yii::$app->request->getQueryParams();
			$params = array_merge($get, $this->model->constraints);
			unset($params['type']);
			unset($params['id']);
	
			$dataProviderOpen = $searchModel->search(array_merge($params, ['closed' => 0]));
			$dataProviderClosed = $searchModel->search(array_merge($params, ['closed' => 1]));
			$issues = $this->render('@nitm/views/issue/index', [
				'dataProviderOpen' => $dataProviderOpen,
				'dataProviderClosed' => $dataProviderClosed,
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
