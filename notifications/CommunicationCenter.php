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
	
	public $tabOptions = [
		'class' => 'tabs-left'
	];
	
	public $contentTabOptions = [
	];
	
	/*
	 * Interval for updating new chat and chat info
	 */
	private $_chatUpdateOptions = [
		"interval" => 30000,
		"enabled" => true,
		'url' => '/reply/get-new/chat/0'
	];
	
	/*
	 * Interval for updating new notifications info
	 */
	private $_notificationUpdateOptions = [
		"interval" => 30000,
		"enabled" => true,
		'url' => '/alerts/get-new-notifications'
	];
	
	public function init()
	{
		parent::init();
		$this->chatUpdateOptions = array_merge($this->_chatUpdateOptions, $this->chatUpdateOptions);
		$this->notificationUpdateOptions = array_merge($this->_notificationUpdateOptions, $this->notificationUpdateOptions);
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
						//'data-on' => '#communication-center-notifications-button'.$uniqid.':hidden',
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
							'options' => [
								'class' => 'col-md-4 col-lg-4 communication-center-item',
							]
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
							'options' => [
								'class' => 'col-md-4 col-lg-4 communication-center-item',
							]
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
		return $widget.$js.Html::tag('style', ".communication-center-item {
				position: fixed !important;
				top: 40px; bottom: 40px; left: auto; right: auto;
				margin-left: -60px;
				overflow: hidden; 
				background-color: #222 !important;
				z-index: 10000;
				padding: 15px;
				box-shadow: 0px 4px 8px #999; 
				color: #fff;
			}");
	}
}
?>