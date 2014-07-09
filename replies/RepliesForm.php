<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies;

use Yii;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use nitm\widgets\models\BaseWidget;
use nitm\models\Replies as RepliesModel;
use kartik\icons\Icon;

class RepliesForm extends BaseWidget
{	
	public $uniqid;	
	public $editor = 'redactor';
	public $inline = true;
	public $useModal = false;
	public $hidden = false;
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'messages',
		'role' => 'replyFormContainer',
		'id' => 'messagesForm'
	];
	
	public $editorOptions = [
		"toolbarSize" => "medium",
		"size" => "medium"
	];
	
	/**
	 * The actions that are supported
	 */
	private $_actions = [
		'reset' => [
			'tag' => 'span',
			'text' => '',
			'options' => [
				'class' => 'btn btn-default',
				'role' => 'resetForm',
				'id' => 'reset_form',
				'title' => 'Reset this form',
				'type' => 'reset'
			],
			'tagOptions' => [
				'class' => 'glyphicon glyphicon-refresh',
				'style' => 'font-size: 16px'
			]
		],
		'submit' => [
			'tag' => 'span',
			'action' => '/replies/reply',
			'text' => '',
			'options' => [
				'class' => 'btn btn-success',
				'role' => 'replyToFormMessage',
				'id' => 'reply_to_form_message',
				'title' => 'Reply',
				'type' => 'submit'
			],
			'tagOptions' => [
				'class' => 'glyphicon glyphicon-envelope',
				'style' => 'font-size: 16px'
			]
		],
	];
	
	public function init()
	{	
		switch(1)
		{
			case !($this->model instanceof RepliesModel) && (($this->parentType == null) || ($this->parentId == null) || ($this->parentKey == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof RepliesModel) ? $this->model : RepliesModel::findModel([$this->parentId, $this->parentType, $this->parentKey]);
			break;
		}
		parent::init();
		$this->uniqid = !$this->uniqid ? uniqid() : $this->uniqid;
		$this->options['id'] .= $this->uniqid;
		assets\Asset::register($this->getView());
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
			return $this->getView()->render('@nitm/views/replies/form/_form', [
				'model' => $this->model,
				'parentId' => $this->parentId,
				'parentType' => $this->parentType,
				'parentKey' => $this->parentKey,
				'useModal' => $this->useModal,
				'widget' => $this,
				'inline' => $this->inline,
				'editor' => $this->editor,
				'editorOptions' => $this->editorOptions,
				'uniqid' => $this->uniqid
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
				case 'submit':
				$ret_val .= Html::submitButton(
					Html::tag($action['tag'], $action['text'], $action['tagOptions']), 
					$action['options']
				);
				break;
				
				case 'reset':
				$ret_val .= Html::resetButton(
					Html::tag($action['tag'], $action['text'], $action['tagOptions']), 
					$action['options']
				);
				break;
			}
		}
		return Html::tag('div', 
			Html::tag('div', 
				$ret_val, [
					'class' => 'btn-group'
				]),
			[
				'role' => 'replyActions',
				'class' => 'form-group pull-right'.(($hidden == true) ? ' hidden ' : ''),
				"style" => "padding-right: 15px"
			]
		);
	}
}
?>