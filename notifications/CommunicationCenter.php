<?php
namespace nitm\widgets\notifications;

use Yii;
use yii\helpers\Html;
use nitm\helpers\Icon;
use yii\bootstrap\Tabs;
use yii\bootstrap\Nav;
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
			'queryFilters' => [
				'read' => 0
			]
		]);
		$uniqid = uniqid();
		
		$chatWidget = Nav::widget([
			'encodeLabels' => false,
			'options' => [
				'id' => 'communication-center-messages-wrapper'.$uniqid,
				'class' => 'nav navbar-right navbar-nav'
			],
			'items' => [
				[
					'label' => Icon::show('comment').Html::tag('span', $chatModel->hasNew(),['class' => 'badge']),
					'options' => [
						'class' => !$chatModel->hasNew() ? 'text-disabled' : 'text-success',
					],
					'linkOptions' => [
						'id' => 'communication-center-messages-button'.$uniqid,
						'title' => 'Click here again to refresh the info',
						'role' => 'dynamicValue',
						'data-animation-target' => '#chat'.$uniqid,
						'data-animation-start-only' => 1,
						'data-type' => 'html',
						'data-id' => '#chat'.$uniqid,
						'data-url' => \Yii::$app->urlManager->createUrl(['/reply/index/chat/0', '__format' => 'html', \nitm\widgets\models\Replies::FORM_PARAM => true])
					],
					'items' => [
						[
							'label' => Html::tag('div', Html::tag('h2', 'Loading Messages...', ['class' => 'text-center']).Html::script("\$('#communication-center-messages-button$uniqid').one('mouseover', function (event) {
								$(this).trigger('click');
							})", ['type' => 'text/javascript']), [
								'role' => 'chatParent',
								'id' => 'chat'.$uniqid,
								'class' => '',
							]),
							'options' => $this->contentOptions
						]
					]
				],
			]
		]);
		
		$alertWidget = Nav::widget([
			'encodeLabels' => false,
			'options' => [
				'id' => 'communication-center-notifications-wrapper'.$uniqid,
				'class' => 'nav navbar-right navbar-nav'
			],
			'items' => [
				[
					'label' => Icon::show('bell').Html::tag('span', $notificationModel->count(), ['class' => 'badge']),
					'options' => [
						'class' => !$notificationModel->count() ? 'bg-disabled' : 'bg-success',
					],
					'linkOptions' => [
						'id' => 'communication-center-notifications-button'.$uniqid,
						'title' => 'Click here again to refresh the info',
						'role' => 'dynamicValue',
						'data-animation-target' => '#communication-center-notifications'.$uniqid,
						'data-animation-start-only' => 1,
						'data-type' => 'html',
						//'data-on' => '#communication-center-notifications-button'.$uniqid.':hidden',
						'data-id' => '#communication-center-notifications'.$uniqid,
						'data-url' => \Yii::$app->urlManager->createUrl(['/alerts/notifications', '__format' => 'html']),
					],
					'items' => [
						[
							'label' => Html::tag('div', Html::tag('h2', 'Loading Alerts...', ['class' => 'text-center']).Html::script("\$('#communication-center-notifications-button$uniqid').one('mouseover', function (event) {
								$(this).trigger('click');
							})", ['type' => 'text/javascript']), [
								'id' => 'communication-center-notifications'.$uniqid,
								'class' => '',
							]),
							'options' => $this->contentOptions
						]
					]
				],
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
		{
			$js = Html::script($js, ['type' => 'text/javascript']);
		}
		return $widget.$js;
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