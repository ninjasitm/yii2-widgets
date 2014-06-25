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
		'class' => 'badge',
		'role' => 'issueCount',
		'id' => 'issue-count'
	];
	
	public $widgetOptions = [
		'class' => 'list-group'
	];
	
	public $itemOptions = [
		'class' => 'list-group-item'
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
		$info = '';
		switch($this->model instanceof IssuesModel)
		{
			case true:
			$this->options['class'] .= " bg-success";
			$info .= Html::a(
				Html::tag('span', (int)$this->model->count.' Issues '.Icon::show('eye'), $this->options), 
				'/issue/index/'.$this->parentType."/".$this->parentId."?__format=modal".($this->enableComments ? '&'.$this->model->commentParam.'=1' : ''),
				[
					'data-toggle' => 'modal',
					'data-target' => '#issue-tracker-modal',
					'title' => 'View issue',
					'class' => 'btn btn-xs btn-primary'
				]
			);
			if($this->fullDetails)
			{
				$info .= Html::tag('span', " on ".$this->model->last->created_at, $this->options);
				$info .= Html::tag('span', "Last by ".$this->model->last->authorUser->getFullName(true, $this->model->last->authorUser), $this->options);
			}
			$info = Html::tag('li', $info, $this->itemOptions);
			$info = Html::tag('ul', $info, $this->widgetOptions);
			break;
			
			default:
			$info = $this->showEmptyCount ? 'Issues: '.Html::tag('span', 0, $this->options) : '';
			break;
		}
		echo $info;
	}
}
?>