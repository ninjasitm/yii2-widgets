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
use nitm\models\Issues as IssueModel;
use nitm\models\search\Issues as IssueSearch;
use nitm\widgets\models\BaseWidget;

/**the issues associated with a request with support for solving them
 */
class IssueTracker extends BaseWidget
{
	public $useModal = true;
	
	/*
	 * HTML options for generevision the widget
	 */
	public $options = [
		'class' => 'issues',
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
			$this->model = ($this->model instanceof IssueModel) ? $this->model : new IssueModel([
				"constrain" => [$this->parentId, $this->parentType]
			]);
			break;
		}
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
				$this->parentId = $this->model->constrain[0];
				$this->parentType = $this->model->constrain[1];
				break;
			}
			$searchModel = new IssueSearch;
			$get = \Yii::$app->request->getQueryParams();
			$params = array_merge($get, $this->model->constraints);
			$dataProvider = $searchModel->search($params);
	
			$issues = $this->getView()->renderAjax('@nitm/views/issue/index', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
				'parentId' => $this->parentId,
				'parentType' => $this->parentType,
				'useModal' => $this->useModal
			]);
			break;
			
			default:
			//$replies = Html::tag('h3', "No comments", ['class' => 'text-error']);
			$issues = 'No Issues';
			break;
		}
		$this->options['id'] .= $this->parentId;
		return Html::tag('div', $issues, $this->options);
	}
}
