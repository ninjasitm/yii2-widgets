<?php
namespace nitm\widgets\notifications;

use Yii;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\bootstrap\Nav;
use kartik\popover\PopoverX;
use nitm\helpers\Icon;
use nitm\widgets\models\Issues;

class CommunicationCenter extends \yii\base\Widget
{
	public $items = [];
	public $chatItems = [];

	public $chatUpdateOptions = [];
	public $notificationUpdateOptions = [];

	public $contentOptions = [];

	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'role' => 'communicationCenterWrapper',
		'id' => 'communication-center-wrapper',
	];
	public $wrapperOptions = [
		'role' => 'communicationCenterContainer',
		'id' => 'communication-center-container',
		'class' => 'communication-center',
		'style' => 'display: none'
	];

	public function init()
	{
		parent::init();
		$this->chatUpdateOptions = array_merge($this->defaultChatUpdateOptions(), $this->chatUpdateOptions);
		$this->notificationUpdateOptions = array_merge($this->defaultNotificationUpdateOptions(), $this->notificationUpdateOptions);
		$this->contentOptions = array_merge($this->defaultContentOptions(), $this->contentOptions);
		CommunicationCenterAsset::register($this->getView());
	}

	public function run()
	{
		$chatModel = new \nitm\widgets\models\Replies([
			'constrain' => [
				'type' => 'chat'
			]
		]);
		$notificationModel = new \nitm\widgets\models\Notification([
			'constrain' => [
				'user_id' => \Yii::$app->user->getId()
			],
			'queryOptions' => [
				'read' => 0
			]
		]);
		$uniqid = uniqid();

		$chatWidget = Nav::widget([
			'encodeLabels' => false,
			'options' => [
				'id' => 'communication-center-messages-wrapper'.$uniqid,
				'class' => 'nav navbar-right navbar-nav chat-container'
			],
			'items' => [
				Html::tag('li', PopoverX::widget([
					'header' => '&nbsp;',
					'type' => 'info',
					'size' => 'lg',
					'placement' => 'bottom bottom-right',
					'content' => Html::tag('div', Html::tag('h2', 'Loading Messages...', [
							'class' => 'text-center'
						])
						/*.Html::script('$("#communication-center-messages-button'.$uniqid.'").one("mouseover", function (event) {
							$(this).trigger("click");
						})', ['type' => 'text/javascript'])*/, [
							'role' => 'chatParent',
							'id' => 'chat'.$uniqid,
							'class' => '',
						]),
					'toggleButton' => [
						'tag' => 'a',
						'label' => Icon::show('comment').Html::tag('span', $chatModel->hasNew(), [
							'class' => 'badge'
						]).Html::tag('span', '', ['class' => 'caret']),
						'class' => 'dropdown-toggle '.(!$chatModel->hasNew() ? 'text-disabled' : 'text-success'),
						'id' => 'communication-center-messages-button'.$uniqid,
						'title' => 'Click here again to refresh the info',
						'role' => 'dynamicValue',
						'data-run-once' => 1,
						'data-animation-target' => '#chat'.$uniqid,
						'data-animation-start-only' => 1,
						'data-type' => 'html',
						'data-id' => '#chat'.$uniqid,
						//'data-on' => '#communication-center-messages'.$uniqid.':visible',
						'data-url' => \Yii::$app->urlManager->createUrl(['/reply/index/chat/0', '__format' => 'html', \nitm\widgets\models\Replies::FORM_PARAM => true]),
					]
				]), [
					'class' => 'dropdown'
				])
			]
		]);

		$alertWidget = Nav::widget([
			'encodeLabels' => false,
			'options' => [
				'id' => 'communication-center-notifications-wrapper'.$uniqid,
				'class' => 'nav navbar-right navbar-nav'
			],
			'items' => [
				Html::tag('li', PopoverX::widget([
					'header' => '&nbsp;',
					'type' => 'info',
					'size' => 'lg',
					'placement' => 'bottom bottom-right',
					'toggleButton' => [
						'tag' => 'a',
						'label' => Icon::show('bell').Html::tag('span', $notificationModel->count(), [
							'class' => 'badge'
						]).Html::tag('span', '', ['class' => 'caret']),
						'class' => 'dropdown-toggle '.(!$notificationModel->count() ? 'text-disabled' : 'text-success'),
						'id' => 'communication-center-notifications-button'.$uniqid,
						'title' => 'Click here again to refresh the info',
						'role' => 'dynamicValue',
						'data-run-once' => 1,
						'data-animation-target' => '#communication-center-notifications'.$uniqid,
						'data-animation-start-only' => 1,
						'data-type' => 'html',
						//'data-on' => '#communication-center-notifications'.$uniqid.':visible',
						'data-id' => '#communication-center-notifications'.$uniqid,
						'data-url' => \Yii::$app->urlManager->createUrl(['/alerts/notifications', '__format' => 'html']),
					],
					'content' => Html::tag('div', Html::tag('h2', 'Loading Alerts...', [
							'class' => 'text-center'
						])
						/*.Html::script('$("#communication-center-notifications-button'.$uniqid.'").one("mouseover", function (event) {
							$(this).trigger("click");
						})', [
							'type' => 'text/javascript'
						])*/, [
							'id' => 'communication-center-notifications'.$uniqid,
							'class' => '',
						]),
					]), [
					'class' => 'dropdown'
				]),
			]
		]);
		$widget = $alertWidget.$chatWidget;

		//$js = "\$nitm.onModuleLoad('communication-center', function (module) {
		//	module.initChatTabs('#".$this->options['id']."');
		//});";
		$js = "";
		if($this->chatUpdateOptions['enabled'])
			$js .= "\$nitm.onModuleLoad('polling', function (module) {
				module.initPolling('chat', {
					enabled: true,
					url: '".$this->chatUpdateOptions['url']."',
					interval: ".$this->chatUpdateOptions['interval'].",
					container: '#nitm-communication-center-widget".$uniqid."'
				}, {object: \$nitm.module('replies'), method: 'chatStatus'});
			});";
		if($this->notificationUpdateOptions['enabled'])
			$js .= "
			\$nitm.onModuleLoad('polling', function (module) {
				module.initPolling('notifications', {
					enabled: true,
					interval: ".$this->notificationUpdateOptions['interval'].",
					url: '".$this->notificationUpdateOptions['url']."',
					container: '#nitm-communication-center-widget".$uniqid."'
				}, {object: \$nitm.module('notifications'), method: 'notificationStatus'});
			});";
		if($js)
			$this->getView()->registerJs($js);
		return $widget;
	}

	protected function defaultContentOptions()
	{
		return [
			'class' => 'col-md-4 col-lg-4 communication-center-item',
		];
	}

	/*
	 * Interval for updating new chat and chat info
	 */
	protected function defaultChatUpdateOptions()
	{
		return [
			"interval" => 30000,
			"enabled" => true,
			'url' => '/reply/get-new/chat/0'
		];
	}

	/*
	 * Interval for updating new notifications info
	 */
	protected function defaultNotificationUpdateOptions()
	{
		return [
			"interval" => 30000,
			"enabled" => true,
			'url' => '/alerts/get-new-notifications'
		];
	}
}
?>
