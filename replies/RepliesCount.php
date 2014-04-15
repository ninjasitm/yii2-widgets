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

class RepliesCount extends BaseWidget
{	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'badge',
		'role' => 'replyCount',
		'id' => 'reply-count'
	];
	
	public function init()
	{
		switch(1)
		{
			case !($this->model instanceof RepliesModel) && (($this->parentType == null) || ($this->parentId == null) || ($this->parentKey == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof RepliesModel) ? $this->model : new RepliesModel([
				"constrain" => [$this->parentId, $this->parentType, $this->parentKey]
			]);
			break;
		}
		parent::init();
	}
	
	public function run()
	{
		switch(is_null($this->model))
		{
			case true:
			$info = 'Replies: '.Html::tag('span', 0, $this->options);
			break;
			
			default:
			$this->options['id'] .= $this->parentId;
			$info = 'Replies: '.Html::tag('span', (int)$this->model->count, $this->options);
			$new = $this->model->hasNew();
			switch($new)
			{
				case true:
				$info .= " New: ".Html::tag('span', $new, $this->options);
				break;
			}
			switch(((int)$this->model->count >= 1) && ($this->model->authorUser instanceof User))
			{
				case true:
				$info .= " Last Reply by ".Html::tag('span', $this->model->last->authorUser->getFullName(true, $this->model->last->authorUser), $this->options);
				$info .= " on ".Html::tag('span', $this->model->last->created_at, $this->options);
				break;
			}
			break;
		}
		echo Html::tag('div', $info, $this->widgetOptions);
	}
}
?>