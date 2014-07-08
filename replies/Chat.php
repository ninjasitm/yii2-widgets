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
	//the information that gets stored in the miscellaneous pane
	public $miscPane = [
		'title' => 'Alerts',
		'content' => ''
		
	];
	
	public $updateOptions = [];
	 
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'role' => 'chatContainer',
		'id' => 'chat-container',
		'class' => 'pull-left'
	];
	 
	/*
	 * HTML options for the chat message container
	 */
	public $chatOptions = [
		'role' => 'chatParent',
		'id' => 'chat',
		'class' => 'chat col-md-4 col-lg-4',
	];
	 
	/*
	 * HTML options for the miscellaneous pane
	 */
	public $chatPaneOptions = [
		'style' => 'display:none',
		'id' => 'chat-messages-pane',
	];
	 
	/*
	 * HTML options for the miscellaneous pane
	 */
	public $miscPaneOptions = [
		'class' => 'fade',
		'id' => 'chat-misc-pane'
	];
	
	/*
	 * HTML options for the navigation
	 */
	public $navigationOptions = [
		'class' => 'nav nav-tabs',
		'role' => 'tablist',
		'id' => 'chat-navigation',
	];
	
	/*
	 * HTML options for the combining div for the mesages and form
	 */
	public $contentOptions = [
		'class' => 'tab-content',
		'id' => 'chat-content',
	];
		
	/*
	 * Interval for updating new chat and chat info
	 */
	private $_updateOptions = [
		"interval" => 60000,
		"enabled" => true,
		'url' => '/reply/get-new/chat/0'
	];
	
	public function init()
	{
		$this->model = new RepliesModel([
			'constrain' => [
				'type' => 'chat'
			]
		]);
		$this->updateOptions = array_merge($this->_updateOptions, $this->updateOptions);
		parent::init();
	}
	
	public function run()
	{
		echo Html::tag('div',
			$this->getNavigation().$this->getContent(),
			$this->options
		);
	}
	
	protected function getContent() 
	{
		$ret_val = Html::tag('div', 
			Html::tag('div',
					Html::tag('div',
						Html::tag('div', '', ['id' => 'chat-messages-container', 'style' => 'display:none']).
						ChatForm::widget(['model' => $this->model]),
						$this->chatOptions
					)
				, 
				$this->chatPaneOptions
			).
			Html::tag('div', $this->miscPane['content'], $this->miscPaneOptions),
			$this->contentOptions
		);
		return $ret_val;
	}
	
	protected function getNavigation() 
	{
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
			Html::tag('li', Html::a('Messages'.$newBadge, \Yii::$app->urlManager->createUrl(['/reply/index/chat/0', '__format' => 'html', RepliesModel::FORM_PARAM => false]), [
				'id' => 'chat-messages-nav', 
				'class' => $newClass,
			]), [
				'role' => 'visibility',
				'data-type' => 'html',
				'data-id' => '#chat-messages-container',
				'data-on' => '#chat-messages-pane:hidden',
				'data-toggle' => '#chat-messages-pane',
				'data-url' =>  \Yii::$app->urlManager->createUrl(['/reply/index/chat/0', '__format' => 'html', RepliesModel::FORM_PARAM => false])
			]).
			Html::tag('li', Html::a($this->miscPane['title'], '#chat-misc-pane', ['data-toggle' => '']), ['id' => 'chat-misc-nav']).
			Html::tag('li', Html::a($newMessage, '#', ['id' => 'chat-info-pane', 'class' => 'text-warning'])),
			$this->navigationOptions
		);
		return $ret_val;
	}
}
?>