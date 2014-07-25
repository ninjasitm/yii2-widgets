<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies;

use Yii;
use yii\helpers\Html;
use nitm\widgets\models\BaseWidget;
use nitm\models\Replies as RepliesModel;
use nitm\models\User;
use kartik\icons\Icon;

class RepliesCount extends BaseWidget
{
	public $fullDetails = true;
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'btn btn-sm',
		'role' => 'replyCount',
		'id' => 'reply-count'
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
			case !($this->model instanceof RepliesModel) && (($this->parentType == null) || ($this->parentId == null) || ($this->parentKey == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof RepliesModel) ? $this->model : RepliesModel::findModel([$this->parentId, $this->parentType, $this->parentKey]);
			break;
		}
		parent::init();
	}
	
	public function run()
	{
		$this->options['id'] .= $this->parentId;
		$this->options['class'] .= ' '.($this->model->getCount() >= 1 ? 'btn-primary' : 'btn-transparent');
		$this->options['label'] = (int)$this->model->getCount().' Replies '.Icon::show('eye');
		$this->options['href'] = \Yii::$app->urlManager->createUrl(['/reply/index/'.$this->parentType."/".$this->parentId, '__format' => 'modal']);
		$this->options['title'] = \Yii::t('yii', 'View Revisions');
		$info = \nitm\widgets\modal\Modal::widget([
			'options' => [
				'id' => $this->options['id'].'-modal'
			],
			'size' => 'large',
			'header' => 'Comments',
			'toggleButton' => $this->options,
		]);
		$new = $this->model->hasNew();
		switch($new)
		{
			case true:
			$info .= " New: ".Html::a(
				Html::tag('span', $new),
				"#",
				[
					'class' => 'btn btn-xs btn-success'
				]
			);
			break;
		}
		switch(((int)$this->model->getCount() >= 1) && ($this->model->last instanceof RepliesModel) && $this->fullDetails)
		{
			case true:
			$info .= Html::tag('span', " on ".$this->model->last->created_at, $this->options);
			$info .= Html::tag('span', " Last by ".$this->model->last->author()->fullName(true), $this->options);
			break;
		}
		$info = Html::tag('li', $info, $this->itemOptions);
		echo Html::tag('ul', $info, $this->widgetOptions);
	}
}
?>