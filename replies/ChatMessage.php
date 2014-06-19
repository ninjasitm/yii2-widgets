<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies;

use Yii;
use nitm\widgets\models\BaseWidget;
use nitm\models\Replies as RepliesModel;

class ChatMessage extends BaseWidget
{	
	public function init()
	{
		$this->model = ($this->model instanceof RepliesModel) ? $this->model : new RepliesModel([
			'constrain' => [
				'type' => 'chat'
			]
		]);
		parent::init();
	}
	
	public function run()
	{
		$message = '';
		switch(($this->model instanceof RepliesModel))
		{
			case true:
			$message = $this>render('@nitm/views/chat/view',['model' => $this->model]);
			break;
		}
		return $message;
	}
}
?>