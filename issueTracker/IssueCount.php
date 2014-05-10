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
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'badge',
		'role' => 'issueCount',
		'id' => 'issue-count'
	];
	
	public $countOptions = [
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
		switch($this->model->count >= 1)
		{
			case true:
			$this->options['class'] .= " bg-success";
			$info .= "Issues: ".Html::a(
				Html::tag('span', (int)$this->model->count.' '.Icon::show('eye'), $this->options), 
				'/issue/index/'.$this->parentType."/".$this->parentId."?__format=modal",
				[
					'data-toggle' => 'modal',
					'data-target' => '#issue-tracker-modal',
					'title' => 'View issue',
					'class' => 'btn btn-xs btn-primary'
				]
			);
			$info .= " Last Issue by ".Html::tag('span', $this->model->last->authorUser->getFullName(true, $this->model->last->authorUser), $this->options);
			$info .= " on ".Html::tag('span', $this->model->last->created_at, $this->options);
			$info = Html::tag('div', $info, $this->countOptions);
			break;
			
			default:
			$info = $this->showEmptyCount ? 'Issues: '.Html::tag('span', 0, $this->options) : '';
			break;
		}
		echo $info;
	}
}
?>