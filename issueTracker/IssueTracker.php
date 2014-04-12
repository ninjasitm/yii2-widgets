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
		if (!($this->model instanceof IssueModel) && ($this->parentType == null) || ($this->parentId == null)) {
			$this->model = null;
		}
		else 
		{
			$this->model = ($this->model instanceof IssueModel) ? $this->model : new IssueModel([
				"constrain" => [$this->parentId, $this->parentType]
			]);
		}
		parent::init();
	}
	
	public function run()
	{
		switch(($this->model instanceof IssueModel))
		{
			case true:
			$searchModel = new IssueSearch;
			$get = \Yii::$app->request->getQueryParams();
			$params = array_merge($get, $this->model->constraints);
			$dataProvider = $searchModel->search($params);
	
			$issues = $this->getView()->render('@nitm/views/issue/index', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
			]);
			break;
			
			default:
			//$replies = Html::tag('h3', "No comments", ['class' => 'text-error']);
			$issues = 'No Issues';
			break;
		}
		$this->options['id'] .= $this->parentId;
		echo Html::tag('div', $issues, $this->options);
	}
}
