<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies\widget;

use Yii;
use yii\helpers\Html;
use nitm\widgets\models\BaseWidget;
use nitm\module\models\Replies as RepliesModel;

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
		if (($this->parentId === null) || ($this->parentType == null)) {
			throw new InvalidConfigException('The "constain" property must be set.');
		}
	}
	
	public function run()
	{
		$this->model = RepliesModel::findModel([$this->parentId, $this->parentType]);
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
		echo Html::tag('div', $info, $this->widgetOptions);
	}
}
?>