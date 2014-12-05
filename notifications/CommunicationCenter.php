<?php
namespace nitm\widgets\notifications;

use Yii;
use yii\helpers\Html;
use nitm\helpers\Icon;
use yii\bootstrap\Tabs;
use yii\bootstrap\Nav;
use nitm\models\Issues;

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
		$chatModel = new \nitm\models\Replies([
			'constrain' => [
				'type' => 'chat'
			]
		]);
		$notificationModel = new \nitm\models\Notification([
			'constrain' => [
				'user_id' => \Yii::$app->user->getId()
			],
			'queryFilters' => [
				'read' => 0
			]
		]);
		$uniqid = uniqid();
		$tabs = Tabs::widget([
			'options' => [
				'id' => 'nitm-communication-center-widget'.$uniqid,
			],
			'encodeLabels' => false,
			'items' => [
				[
					'label' => Icon::show('comment').Html::tag('span', $chatModel->hasNew(),['class' => 'badge']),
					'active' => false,
					'content' =>Html::tag('div', '',
						[
							'id' => 'communication-center-messages'.$uniqid,
							'role' => 'chatParent',
							'id' => 'chat'.$uniqid,
						]
					),
					'options' => [
						'id' => 'communication-center-messages'.$uniqid,
						'class' => 'chat col-md-4 col-lg-4 communication-center-item',
					],
					'headerOptions' => [
						'id' => 'communication-center-messages-tab'.$uniqid,
						'class' => !$chatModel->hasNew() ? '' : 'bg-success'
					],
					'linkOptions' => [
						'role' => 'visibility',
						'data-type' => 'html',
						'data-on' => '#communication-center-messages'.$uniqid.':hidden',
						'data-id' => '#communication-center-messages'.$uniqid,
						'data-url' => \Yii::$app->urlManager->createUrl(['/reply/index/chat/0', '__format' => 'html', \nitm\models\Replies::FORM_PARAM => true]),
						'id' => 'communication-center-messages-link'.$uniqid
					]
				],
				[
					'label' => Icon::show('bell').Html::tag('span', $notificationModel->count(), ['class' => 'badge']),
					'content' =>Html::tag('div', '',
						[
							'id' => 'communication-center-notifications'.$uniqid,
						]
					),
					'options' => [
						'id' => 'communication-center-notifications'.$uniqid,
						'class' => 'col-md-4 col-lg-4 communication-center-item',
					],
					'headerOptions' => [
						'id' => 'communication-center-notifications-tab'.$uniqid,
						'class' => !$notificationModel->count() ? '' : 'bg-success'
					],
					'linkOptions' => [
						'role' => 'visibility',
						'data-type' => 'html',
						'data-on' => '#communication-center-notifications'.$uniqid.':hidden',
						'data-id' => '#communication-center-notifications'.$uniqid,
						'data-url' => \Yii::$app->urlManager->createUrl(['/alerts/notifications', '__format' => 'html']),
						'id' => 'communication-center-notifications-link'.$uniqid
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
		$widget = Html::tag('div', $tabs, $this->options);
		$js = "\$nitm.onModuleLoad('communication-center', function (module) {
			module.initChatTabs('#".$this->options['id']."');
		});";
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
				top: 60px; right: 6px; bottom: 0px; 
				overflow: hidden; 
				padding: 0px; box-shadow: 0px 4px 8px #999; 
				background-color: rgba(255,255,255,0.9);
				z-index: 10000;
			}");
	}
}
?>