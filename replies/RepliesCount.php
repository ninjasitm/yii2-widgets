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
		'class' => 'badge',
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
		switch($this->model instanceof RepliesModel)
		{	
			default:
			$this->options['id'] .= $this->parentId;
			$info = Html::a(
				Html::tag('span', (int)$this->model->count.' Replies '.Icon::show('eye'), $this->options),
				'/reply/index/'.$this->parentType."/".$this->parentId.(!empty($this->parentKey) ? "/".urlencode($this->parentKey) : '')."?__format=modal",
				[
					'data-toggle' => 'modal',
					'data-target' => '#replies-modal',
					'title' => 'View issue',
					'class' => 'btn btn-xs btn-primary'
				]
			);
			$new = $this->model->hasNew();
			switch($new)
			{
				case true:
				$info .= " New: ".Html::a(
					Html::tag('span', $new, $this->options),
					"#",
					[
						'class' => 'btn btn-xs btn-primary'
					]
				);
				break;
			}
			switch(((int)$this->model->count >= 1) && ($this->model->last->author() instanceof User) && $this->fullDetails)
			{
				case true:
				$info .= Html::tag('span', " on ".$this->model->last->created_at, $this->options);
				$info .= Html::tag('span', " Last by ".$this->model->last->author()->fullName(true), $this->options);
				break;
			}
			$info = Html::tag('li', $info, $this->itemOptions);
			break;
		}
		echo $info = Html::tag('ul', $info, $this->widgetOptions);
	}
}
?>