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
use nitm\models\User;
use nitm\models\Replies as RepliesModel;
use nitm\models\search\Replies as RepliesSearch;
use kartik\icons\Icon;

class Chat extends BaseWidget
{	
	/*
	 * Interval for updating new chat and chat info
	 */
	public $updateOptions = [
		"interval" => 60000,
		"enabled" => true,
		'url' => 'reply/get-new/chat/0'
	];
	 
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'role' => 'chatContainer',
		'id' => 'chat-container',
	];
	
	/*
	 * HTML options for generating the widget
	 */
	public $navigationOptions = [
		'class' => 'nav nav-tabs',
		'id' => 'chat-navigation',
	];
	
	/*
	 * HTML options for generating the widget
	 */
	public $contentOptions = [
		'class' => 'tab-content',
		'id' => 'chat-content',
	];
	
	public function init()
	{
		$this->model = new RepliesModel([
			'constrain' => [
				'type' => 'chat'
			]
		]);
		parent::init();
	}
	
	public function run()
	{
		echo Html::tag('div',
			$this->getNavigation().$this->getContent(),
			$this->options
		);
	}
	
	protected function getContent() {
		$ret_val = Html::tag('div', 
			Html::tag('div', 
				\nitm\widgets\replies\RepliesChat::widget([
					'model' => $this->model, 
					'withForm' => true,
					'updateOptions' => $this->updateOptions
				]), [
				'class' => 'tab-pane fade in',
				'id' => 'chat-messages'
			]).
			Html::tag('div', '', [
				'class' => 'tab-pane fade',
				'id' => 'chat-misc'
			]),
			$this->contentOptions
		);
		return $ret_val;
	}
	
	protected function getNavigation() {
		$new = $this->model->hasNew();
		switch($new >= 1)
		{
				case true:
				$newBadge = Html::tag('span', $new, ['class' => 'badge']);
				$newMessage = $new." new messages";
				$newClass = "bg-success";
				break;
				
				default:
				$newBadge = '';
				$newMessage = '';
				$newClass = "";
				break;
		}
		$ret_val = Html::tag('ul', 
			Html::tag('li', Html::a('Messages'.$newBadge, '#chat-messages', ['data-toggle' => 'tab', 'id' => 'chat-messages-nav', 'class' => $newClass]), []).
			Html::tag('li', Html::a('Information', '#chat-misc', ['data-toggle' => 'tab']), ['id' => 'chat-meta-nav']).
			Html::tag('li', Html::a($newMessage, '#', ['id' => 'chat-updates', 'class' => 'text-warning'])),
			$this->navigationOptions
		);
		return $ret_val;
	}
}
?>