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
use nitm\widgets\models\User;
use nitm\widgets\models\Revisions as RevisionsModel;
use nitm\widgets\helpers\BaseWidget;
use kartik\icons\Icon;

class RevisionsCount extends BaseWidget
{
	public $fullDetails = true;	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'btn btn-sm',
		'role' => 'revisionsCount',
		'id' => 'revisions-count',
		'tag' => 'a'
	];
	
	public $widgetOptions = [
		'class' => 'btn-group'
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
		$this->options['id'] .= $this->parentId;
		$this->options['class'] .= ' '.($this->model->count() >= 1 ? 'btn-primary' : 'btn-transparent');
		$this->options['label'] = (int)$this->model->count().' Revisions '.Icon::show('eye');
		$this->options['href'] = \Yii::$app->urlManager->createUrl(['/revisions/index/'.$this->parentType."/".$this->parentId, '__format' => 'modal']);
		$this->options['title'] = \Yii::t('yii', 'View Revisions');
		$info = \nitm\widgets\modal\Modal::widget([
			'options' => [
				'id' => $this->options['id'].'-modal'
			],
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
			$info .= Html::tag('span', "Last by ".$this->model->last->author()->fullName(true), $this->options);
		}
		echo $info = Html::tag('div', $info, $this->widgetOptions).$new;
	}
}
?>