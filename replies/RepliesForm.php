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
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'messages',
		'role' => 'replyForm',
		'id' => 'messagesForm'
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
				'class' => 'btn btn-sm btn-default',
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
		'reset' => [
			'tag' => 'span',
			'text' => '',
			'options' => [
				'class' => 'btn btn-sm btn-default',
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
	}
	
	public function run()
	{
		switch(is_null($this->model))
		{
			case true:
			$formBody = '';
			break;
			
			default:
			$this->options['id'] .= $this->parentId;			
			$this->model->setScenario('validateNew');
			$this->_form = ActiveForm::begin([
						'id' => 'reply_form'.$this->parentId,
						"action" => "/reply/new",
						"options" => [
										'data-parent' => 'messages'.$this->parentId,
										"class" => "form-inline",
										"role" => "replyForm",
										],
						"fieldConfig" => [
								"inputOptions" => ["class" => "form-control"]
								],
						"enableAjaxValidation" => true
						]);
							
			$formBody = Html::activeHiddenInput($this->model, 'constrain[unique]', ['value' => $this->parentId]);
			$formBody .= Html::activeHiddenInput($this->model, "constrain[for]", ['value' =>  $this->parentType]);
			$formBody .= Html::activeHiddenInput($this->model, "reply_to", ['value' =>  null]);
			/*$formBody .= $this->_form->field($this->model, 'message')->textarea([
				'class' => 'hidden',
				'id' => 'reply-message'.$this->parentId
			])->label('Message', ['class' => 'sr-only']);*/
			$formBody .= Html::button(
				'Click to Reply',
				[
					'role' => "startEditor",
					'data-container' => $this->options['id'],
					'data-id' => $this->parentId,
					'class' => 'btn btn-default center-block'
				]
			);
			$formBody .= $this->getActions(true);
			echo Html::tag('div', $formBody, $this->options);
			ActiveForm::end();
			break;
		}
	}
	
	/**
	 * Get teh actions supported for replying
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
			Html::tag('div', 
				$ret_val, [
					'class' => 'text-right'
				]),
			[
				'role' => 'replyActions',
				'class' => 'form-group pull-right '.(($hidden == true) ? 'hidden' : ''),
			]
		);
	}
}
?>