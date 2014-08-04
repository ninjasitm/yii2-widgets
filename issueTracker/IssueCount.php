<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\issueTracker;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use nitm\models\User;
use nitm\models\Issues as IssuesModel;
use nitm\widgets\models\BaseWidget;
use kartik\icons\Icon;

class IssueCount extends BaseWidget
{
	public $enableComments;
	public $fullDetails = true;
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'btn btn-sm',
		'role' => 'issueCount',
		'id' => 'issue-count',
		'tag' => 'a'
	];
	
	public $widgetOptions = [
		'class' => 'btn-group'
	];
	
	public function init()
	{ 
		switch(1)
		{
			case !($this->model instanceof IssuesModel) && (($this->parentType == null) || ($this->parentId == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof IssuesModel) ? $this->model : IssuesModel::findModel([$this->parentId, $this->parentType]);
			break;
		}	
		parent::init();
	}
	
	public function run()
	{
		$this->options['id'] .= $this->parentId;
		$this->options['class'] .= ' '.($this->model->count() >= 1 ? 'btn-primary' : 'btn-transparent');
		$this->options['label'] = (int)$this->model->count().' Issues '.Icon::show('eye');
		$this->options['href'] = \Yii::$app->urlManager->createUrl(['/issue/index/'.$this->parentType."/".$this->parentId, '__format' => 'modal', IssuesModel::COMMENT_PARAM => $this->enableComments]);
		$this->options['title'] = \Yii::t('yii', 'View Issues');
		$info = \nitm\widgets\modal\Modal::widget([
			'options' => [
				'id' => $this->options['id'].'-modal'
			],
			'size' => 'large',
			'header' => 'Issues',
			'toggleButton' => $this->options,
		]);
		$new = $this->model->hasNew();
		switch($new >= 1)
		{
			case true:
			$new = \nitm\widgets\activityIndicator\ActivityIndicator::widget([
				'type' => 'new',
				'position' => 'top right',
				'text' => Html::tag('span', $new." new")
			]);
			break;
			
			default:
			$new = '';
			break;
		}
		if($this->fullDetails)
		{
			$info .= Html::tag('span', " on ".$this->model->last->created_at, $this->options);
			$info .= Html::tag('span', "Last by ".$this->model->last->authorUser->fullName(true), $this->options);
		}
		return Html::tag('div', $info, $this->widgetOptions).$new;
	}
}
?>