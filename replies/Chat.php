<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies;

use Yii;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use nitm\widgets\BaseWidget;
use nitm\widgets\models\User;
use nitm\widgets\models\Replies as RepliesModel;
use nitm\widgets\models\search\Replies as RepliesSearch;
use kartik\icons\Icon;

class Chat extends BaseWidget
{
	public $notificationModel;
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
		'style' => 'position: fixed;top: 6px; right: 6px;bottom: 40px;overflow: hidden;padding: 0px;box-shadow: 2px 2px 15px #000;'
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
		$this->notificationModel = new \nitm\widgets\models\Notification([
			'constrain' => [
				'user_id' => \Yii::$app->user->getId()
			]
		]);
		$this->updateOptions = array_merge($this->_updateOptions, $this->updateOptions);
		parent::init();
		Asset::register($this->getView());
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
						Html::tag('div', '', ['id' => 'chat-messages-container', 'style' => 'display:none']),
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
				$newMessage = $new." new messages";
				$newClass = "bg-success";
				break;

				default:
				$newMessage = 'No new messages';
				$newClass = "bg-transparent";
				break;
		}
		$uniqid = uniqid();
		$ret_val = Tabs::widget([
			'options' => [
				'id' => 'nitm-chat-widget'.$uniqid,
			],
			'encodeLabels' => false,
			'items' => [
				[
					'label' => 'Messages '.Html::tag('span', $this->model->hasNew(), ['class' => 'badge']),
					'active' => false,
					'content' =>Html::tag('div', '',
						[
							'id' => 'chat-widget-container'.$uniqid,
							'role' => 'chatParent',
							'id' => 'chat'.$uniqid,
							'class' => 'chat col-md-4 col-lg-4',
							'style' => 'z-index: 10000; position: fixed;top: 6px; right: 6px;bottom: 40px;overflow: hidden;padding: 0px;box-shadow: 2px 2px 15px #000; background-color: rgba(153,153,153,0.9);'
						]
					),
					'options' => [
						'id' => 'chat-widget-messages'.$uniqid
					],
					'headerOptions' => [
						'id' => 'chat-widget-messages-tab'.$uniqid
					],
					'linkOptions' => [
						'role' => 'visibility',
						'data-type' => 'html',
						'data-on' => '#chat-widget-messages'.$uniqid.':hidden',
						'data-id' => '#chat'.$uniqid,
						'data-url' => \Yii::$app->urlManager->createUrl(['/reply/index/chat/0', '__format' => 'html', \nitm\widgets\models\Replies::FORM_PARAM => true]),
						'id' => 'chat-widget-messages-link'.$uniqid
					]
				],
				[
					'label' => '',
					'content' => '',
					'active' => true,
					'headerOptions' => [
						'class' => 'hidden'
					]
				]
			]
		]);
		if(isset($this->updateOptions['enable']) && $this->updateOptions['enable'])
			$this->getView()->registerJs(new \yii\web\JsExpression('$nitm.module("replies").initChatActivity("[role=\'chatParent\']", "'.$this->updateOptions['url'].'", '.$this->updateOptions['interval'].')'));
		return $ret_val;
	}
}
?>
