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
use kartik\icons\Icon;

class Replies extends BaseWidget
{	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'messages',
		'role' => 'entityMessages',
		'id' => 'messages',
		'data-parent' => 'replyFormParent'
	];
	
	/**
	 * \commond\models\Reply $reply
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
		if (!($this->model instanceof RepliesModel) && ($this->parentType == null) || ($this->parentId == null) || ($this->parentKey == null)) {
			$this->model = null;
		}
		else 
		{
			$this->model = ($this->model instanceof RepliesModel) ? $this->model : new RepliesModel([
				"constrain" => [$this->parentId, $this->parentType, $this->parentKey]
			]);
		}
		parent::init();
	}
	
	public function run()
	{
		switch(($this->model instanceof RepliesModel) && ($this->model->hasAny()))
		{
			case true:
			$replies = '';
			switch(\nitm\models\User::isAdmin())
			{
				case true:
				break;
			}
			$arrow = Html::tag('div', '', ['class' => 'arrow']);
			foreach($this->model->getModels() as $reply)
			{
				switch(empty($reply->author))
				{
					case false:
					switch($this->userExists($reply))
					{
						case true:
						$this->reply = $reply;
						$replies .= $this->getReply();
						$this->_userExists = false;
						break;
					}
					break;
				}
			}
			break;
			
			default:
			//$replies = Html::tag('h3', "No comments", ['class' => 'text-error']);
			$replies = '';
			break;
		}
		$this->options['id'] .= $this->parentId;
		echo Html::tag('div', $replies, $this->options);
	}
	
	/**
	 * Does the user for this reply exist?
	 * @param Replies $repoly
	 * @return boolean user exists
	 */
	public function userExists($reply)
	{
		$this->_users[$reply->author] = (isset($this->_users[$reply->author]) &&  ($this->_users[$reply->author] instanceof User)) ? $this->_users[$reply->author] : User::find($reply->author)->one();
		$this->_user = $this->_users[$reply->author];
		$this->_userExists = $this->_user instanceof User;
		return $this->_userExists;
	}
	
	public function getFooter()
	{
		$ret_val = '';
		switch($this->_userExists)
		{
			case true:
			$actions = Html::tag('div', 
				$this->getActions(),
				[
					'class' => 'message-actions',
					'id' => 'messageActions'.$this->reply->unique
				]
			);
			$meta = Html::tag('div', 
				"posted on ".$this->reply->added_hr.' by '.Html::tag('strong', $this->_user->username), 
				[
					'class' => 'message-meta'
				]
			);
			$ret_val = Html::tag('div', Html::tag('hr').$meta.$actions, [
				'class' => 'message-footer',
				'id' => 'messageFooter'.$this->reply->unique
			]);
			break;
		}
		return $ret_val;
	}
	
	/**
	 * Return the avatar, user info and date info for user
	 * @return string $ret_val
	 */
	public function getHeader()
	{
		$ret_val = '';
		switch($this->_userExists)
		{
			case true:
			$avatar = Html::tag('div', 
				Html::img($this->_user->getAvatar(), ['class' => 'avatar avatar-small']),
				[
					'class' => 'avatar',
					'id' => 'messageAvatar'.$this->reply->unique
				]
			);
			$title = empty($title) ? '' : Html::tag('div', Html::tag('h4', $ths->reply->title), [
				'class' => 'message-title',
				'id' => 'messageTitle'.$this->reply->unique
			]);
			$title .= empty($this->reply->reply_to_author) ? '' : Html::a('@'.$this->reply->getReplyToAuthor(), '#'.'messageTitle'.$this->reply->reply_to, ['class' => 'message-reply-to']);
			$ret_val = $avatar.$title;
			break;
		}
		return $ret_val;
	}
	
	public function getBody()
	{
		$ret_val = '';
		switch($this->_userExists)
		{
			case true:
			$ret_val = Html::tag('div', 
				Html::tag('p', $this->reply->message, ['role' => 'message']),
				[
					'class' => 'message-body',
					'id' => 'messageBody'.$this->reply->unique
				]
			);
			break;
		}
		return $ret_val;
	}
	
	public function getReply()
	{
		$reply = $this->getHeader().$this->getBody().$this->getFooter();
		$class = 'message';
		if($this->reply->hidden)
		{
			$class .= ' message-hidden';
		}
		return Html::tag('div', $reply, [
					'class' => $class,
					'id' => 'message'.$this->reply->unique
				]);
	}
	
	public function getActions()
	{
		$actions = is_null($this->actions) ? $this->_actions : array_intersect_key($this->_actions, $this->actions);
		$ret_val = '';
		foreach($actions as $name=>$action)
		{
			switch(isset($action['adminOnly']) && ($action['adminOnly'] == true))
			{
				case true:
				switch($this->_user->isAdmin())
				{
					case true:
					$action['options']['data-parent'] = $this->parentId;
					$action['options']['data-reply-to'] = $this->reply->unique;
					$action['options']['id'] = $action['options']['id'].$this->reply->unique;
					$ret_val .= Html::tag($action['tag'], $action['text'], $action['options']);
					break;
				}
				break;
				
				default:
				switch($name)
				{
					case 'hide':
					//$action['options']['class'] .= ($this->reply->hidden) ? 'glyphicon-eye-open' : 'glyphicon-eye-close';
					$action['text'] = ($this->reply->hidden) ? 'unhide' : 'hide';
					break;
					
					case 'quote':
					$action['options']['data-author'] = $this->_user->username;
					break;
				}
				$action['options']['data-parent'] = $this->parentId;
				$action['options']['data-reply-to'] = $this->reply->unique;
				$action['options']['id'] = $action['options']['id'].$this->reply->unique;
				$ret_val .= Html::a(Html::tag($action['tag'], $action['text']), $action['action'].'/'.$this->reply->unique, $action['options']);
				break;
			}
			
		}
		return $ret_val;
	}
}
?>