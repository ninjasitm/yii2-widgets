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
use nitm\widgets\models\User;
use nitm\widgets\models\Issues as IssuesModel;
use nitm\widgets\helpers\BaseWidget;
use kartik\icons\Icon;

class IssueCount extends BaseWidget
{
	public $enableComments;
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
		//print_r($this->model);
		//print_r($this->model->find()->where(['id' => $this->model->id])->with(['count', 'newCount'])->one());
		//exit;
		$this->options['id'] .= $this->parentId;
		$this->options['class'] .= ' '.($this->model->count() >= 1 ? 'btn-primary' : 'btn-transparent');
		$this->options['label'] = $this->getLabel();
		$this->options['href'] = \Yii::$app->urlManager->createUrl(['/issue/index/'.$this->parentType."/".$this->parentId, '__format' => 'modal', IssuesModel::COMMENT_PARAM => $this->enableComments]);
		$this->options['title'] = \Yii::t('yii', 'View Issues');
		$info = $this->getInfoLink();
		return Html::tag('div', $info, $this->widgetOptions).$this->getNewIndicator();
	}
}
?>