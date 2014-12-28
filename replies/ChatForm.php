<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies;

use Yii;
use yii\helpers\Html;
use yii\widgets\ActiveChat;
use yii\redactor\widgets\Redactor;
use nitm\widgets\helpers\BaseWidget;
use nitm\widgets\models\Replies as RepliesModel;
use kartik\icons\Icon;

class ChatForm extends BaseWidget
{	
	public $editor = 'redactor';
	public $inline = true;
	public $useModal = false;
	public $hidden = false;
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'messages',
		'role' => 'replyChatContainer',
		'id' => 'messagesChat'
	];
	
	public $editorOptions = [
		"toolbarSize" => "small",
		"size" => "small",
		'options' => [
			'style' => 'resize:none;',
			'autoresize' => false,
			'maxHeight' => 60,
			'minHeight' => 60
		]
	];
	
	/**
	 * The actions that are supported
	 */
	private $_actions = [
		'submit' => [
			'tag' => 'span',
			'action' => '/replies/reply',
			'text' => '',
			'options' => [
				'class' => 'btn btn-sm chat-form-btn',
				'role' => 'replyToChatMessage',
				'id' => 'reply_to_form_message',
				'title' => 'Reply',
				'type' => 'submit'
			],
			'tagOptions' => [
				'class' => 'glyphicon glyphicon-envelope',
				'style' => 'font-size: 16px'
			]
		],
		'reset' => [
			'tag' => 'span',
			'text' => '',
			'options' => [
				'class' => 'btn btn-sm chat-form-btn',
				'role' => 'resetChat',
				'id' => 'reset_form',
				'title' => 'Reset this form',
				'type' => 'reset'
			],
			'tagOptions' => [
				'class' => 'glyphicon glyphicon-refresh',
				'style' => 'font-size: 16px'
			]
		],
	];
	
	public function init()
	{
		$this->model = ($this->model instanceof RepliesModel) ? $this->model : new RepliesModel([
			'constrain' => [
				'type' => 'notes'
			]
		]);
		$this->model->maxLength = 140;
		parent::init();
		Asset::register($this->getView());
	}
	
	public function run()
	{
		switch(is_null($this->model))
		{
			case true:
			return '';
			break;
			
			default:		
			$this->model->setScenario('validateNew');
			return $this->getView()->render('@nitm/widgets/views/chat/form/_form', [
				'model' => $this->model,
				'parentId' => $this->parentId,
				'parentType' => $this->parentType,
				'parentKey' => $this->parentKey,
				'useModal' => $this->useModal,
				'widget' => $this,
				'inline' => $this->inline,
				'editor' => $this->editor,
				'editorOptions' => $this->editorOptions
			]);
			break;
		}
	}
	
	/**
	 * Get the actions supported for replying
	 */
	public function getActions($hidden=false)
	{
		$ret_val = '';
		foreach($this->_actions as $type=>$action)
		{
			switch($type)
			{
				case 'reset':
				$ret_val .= Html::resetButton(
						Html::tag($action['tag'], $action['text'], $action['tagOptions']), 
						$action['options']
					);
				break;
				
				case 'submit':
				$ret_val .= Html::submitButton(
						Html::tag($action['tag'], $action['text'], $action['tagOptions']), 
						$action['options']
					);
				break;
			}
		}
		return Html::tag('div', 
			$ret_val,
			[
				'role' => 'replyActions',
				'class' => 'text-right '.(($hidden == true) ? 'hidden' : ''),
			]
		);
	}
}
?>