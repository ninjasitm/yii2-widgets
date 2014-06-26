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

class ChatMessages extends BaseWidget
{	
	/*
	 * Interval for updating new chat and chat info
	 */
	public $updateOptions = [
		"interval" => 60000,
		"enabled" => true,
		'url' => 'reply/get-new/chat/0'
	];
	
	public $withForm = false;
	 
	/*
	 * HTML options for the chat message container
	 */
	public $options = [
		'role' => 'chatFormParent',
		'id' => 'chat',
		'class' => 'chat col-lg-4 col-md-4',
	];
	
	/*
	 * HTML options for generating the widget
	 */
	public $listOptions = [
		'class' => 'chat-messages',
		'role' => 'chatMessages',
		'id' => 'chat-messages',
		'data-parent' => 'chatParent'
	];
	
	/**
	 * \common\models\Reply $reply
	 */
	public $reply;
	
	/**
	 * The actions that are supported
	 */
	private $_actions = [
		'reply' => [
			'tag' => 'span',
			'action' => '/reply/to',
			'text' => 'reply',
			'options' => [
				'class' => '',
				'role' => 'replyTo',
				'id' => 'reply_to_message',
				'title' => 'Reply to this message'
			]
		],
		'quote' => [
			'tag' => 'span',
			'action' => '/reply/quote',
			'text' => 'quote',
			'options' => [
				'class' => '',
				'role' => 'quoteReply',
				'id' => 'quote_message',
				'title' => 'Quote this message'
			]
		],
		'hide' => [
			'tag' => 'span',
			'action' => '/reply/hide',
			'text' => '',
			'options' => [
				'class' => '',
				'role' => 'hideReply',
				'id' => 'hide_message',
				'title' => 'Hide this message'
			],
			'adminOnly' => true
		],
	];
	
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
		switch(($this->model instanceof RepliesModel))
		{
			case true:
			switch(empty($this->parentId))
			{
				/**
				 * This issue model was initialed through a model
				 * We need to set the parentId and parentType from the constraints values
				 */
				case true:
				//$this->parentId = $this->model->constraints['parent_id'];
				//$this->parentType = $this->model->constrain['parent_type'];
				break;
			}
			$searchModel = new RepliesSearch;
			$searchModel->withThese = ['replyTo', 'authorUser'];
			$get = \Yii::$app->request->getQueryParams();
			$params = array_merge($get, $this->model->constraints);
			unset($params['type']);
			unset($params['id']);
	
			$dataProvider = $searchModel->search(array_merge($params));
			$dataProvider->setSort([
				'defaultOrder' => [
					'id' => SORT_DESC,
				]
			]);
			$this->options['id'] .= $this->parentId;
			$replies = $this->getView()->render('@nitm/views/chat/index', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
				'useModal' => $this->useModal,
				'widget' => $this,
				'options' => $this->options,
				'listOptions' => $this->listOptions,
				'withForm' => $this->withForm,
				'primaryModel' => $this->model,
				'updateOptions' => $this->updateOptions
			]);
			//RepliesAsset::register($this->getView());
			break;
			
			default:
			//$replies = Html::tag('h3', "No comments", ['class' => 'text-error']);
			$replies = 'No Replies';
			break;
		}
		$this->options['id'] .= $this->parentId;
		return $replies;
	}
}
?>