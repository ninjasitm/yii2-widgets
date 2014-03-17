<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies\widget;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use nitm\module\models\User;
use nitm\module\models\Replies as RepliesModel;

class RepliesForm extends Widget
{
	/*
	 * The options used to constraing the replies
	 */
	public $constrain;
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'messages',
		'role' => 'replyForm',
		'id' => 'messagesForm'
	];
	
	/*
	 * Options for replies
	 */
	public $replyOptions = [
		'class' => '',
		'id' => ''
	];
	
	/*
	 * The number of replies to get on each select query
	 */
	public $limit = 10;
	
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
	
	/**
	 * Does the user exist?
	 */
	private $_userExists = false;
	
	/**
	 * Active form
	 */
	private $_form = false;
	
	public function init()
	{	
		if ($this->constrain === null) {
			throw new InvalidConfigException('The "constain" property must be set.');
		}
	}
	
	public function run()
	{
		$model = new RepliesModel();
		$this->options['id'] .= $this->constrain['one'];
		
		$model->setScenario('validateNew');
		$this->_form = ActiveForm::begin([
					'id' => 'reply_form'.$this->constrain['one'],
					"action" => "/reply/new",
					"options" => [
									'data-parent' => 'messages'.$this->constrain['one'],
									"class" => "form-inline",
									"role" => "replyForm",
									],
					"fieldConfig" => [
							"inputOptions" => ["class" => "form-control"]
							],
					"enableAjaxValidation" => true
					]);
						
		$formBody = Html::activeHiddenInput($model, 'constrain[unique]', ['value' => $this->constrain['one']]);
		$formBody .= Html::activeHiddenInput($model, "constrain[for]", ['value' =>  $this->constrain['three']]);
		$formBody .= Html::activeHiddenInput($model, "reply_to", ['value' =>  null]);
		$formBody .= Html::button(
			'Click to Reply',
			[
				'role' => "startEditor",
				'data-container' => '#'.$this->options['id'],
				'data-id' => $this->constrain['one'],
				'class' => 'btn btn-default center-block'
			]
		);
		$formBody .= $this->getActions(true);
		
		echo Html::tag('div', $formBody, $this->options);
		ActiveForm::end();
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