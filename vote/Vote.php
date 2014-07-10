<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\vote;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use nitm\models\User;
use nitm\models\Vote as VoteModel;
use nitm\widgets\models\BaseWidget;
use kartik\icons\Icon;

class Vote extends BaseWidget
{
	
	/**
	 * The actions to enable
	 */
	public $actions;
	
	/*
	 * HTML options for genevote the widget
	 */
	public $widgetOptions = [
		'class' => 'vote',
		'role' => 'entityVote',
		'id' => 'vote',
	];
	
	/*
	 * The number of Vote to get on each select query
	 */
	public $limit = 10;
	
	/**
	 * int rating value
	 */
	public $rating;
	
	/**
	 * The actions that are supported
	 */
	private $_actions = [
		'up' => [
			'tag' => 'span',
			'action' => '/vote/up',
			'text' => 'thumbs-up',
			'options' => [
				'class' => 'col-md-6 col-lg-6 pull-left',
				'role' => 'voteUp',
				'id' => 'vote-up',
				'title' => 'Vote for this'
			]
		],
		'down' => [
			'tag' => 'span',
			'action' => '/vote/down',
			'text' => 'thumbs-down',
			'options' => [
				'class' => 'col-md-6 col-lg-6 pull-right',
				'role' => 'voteDown',
				'id' => 'vote-down',
				'title' => 'Vote against this'
			]
		],
		'reset' => [
			'tag' => 'span',
			'action' => '/vote/reset',
			'text' => 'refresh',
			'options' => [
				'class' => 'col-lg-12 col-md-12',
				'role' => 'resetVote',
				'id' => 'vote-reset',
				'title' => 'Reset the votes'
			],
			'adminOnly' => true
		],
	];
	
	public function init()
	{
		if (!($this->model instanceof VoteModel) && ($this->parentType == null) || ($this->parentId == null)) {
			$this->model = null;
		}
		else 
		{
			$this->model = ($this->model instanceof VoteModel) ? $this->model : VoteModel::findModel([$this->parentId, $this->parentType]);
		}
		parent::init();
	}
	
	public function run()
	{
		$vote = '';
		switch(\Yii::$app->user->identity->isAdmin())
		{
			case true:
			break;
		}
		$this->rating = $this->model->getRating();
		switch(isset($this->rating['negative']))
		{
			case true:
			$positive = Html::tag(
				'div',
				Html::tag(
					'strong', 
					$this->rating['positive'], 
					['id' => 'vote-value-positive'.$this->model->getId()]
				).($this->model->allowMultiple() ? '' : "%"),
				['class' => 'text-success col-md-6 col-lg-6']
			);
			$negative = Html::tag(
				'div',
				Html::tag(
					'strong', 
					$this->rating['negative'], 
					['id' => 'vote-value-negative'.$this->model->getId()]
				).($this->model->allowMultiple() ? '' : "%"),
				['class' => 'text-danger col-md-6 col-lg-6']
			);
			break;
			
			default:
			$negative = '';
			$positive = Html::tag(
				'strong', 
				$this->rating['positive'], 
				['id' => 'vote-value-positive'.$this->model->getId()]
			).($this->model->allowMultiple() ? '' : "%");
			break;
		}
		$vote .= Html::tag('div', 
			$positive.$negative,
			['class' => 'center-block text-center']
		);
		$vote .= $this->getActions();
		$this->widgetOptions['id'] .= $this->parentId;
		echo Html::tag('div', $vote, $this->widgetOptions);
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
				switch(\Yii::$app->user->identity->isAdmin())
				{
					case true:
					$action['options']['id'] = $action['options']['id'].$this->parentId;
					$ret_val .= Html::a(
						Html::tag(
							$action['tag'], 
							Icon::show(
								$action['text']
							)
						), 
						$action['action'].'/'.$this->parentType.'/'.$this->parentId, $action['options']
					);
					break;
				}
				break;
				
				default:
				$action['options']['id'] = $action['options']['id'].$this->parentId;
				switch(1)
				{
					case ($name == 'up') && (($this->rating['positive'] >= $this->model->getMax()) || ($this->model->currentUserVoted($name))):
					case ($name == 'down') && (($this->rating['positive'] <= 0) || ($this->model->currentUserVoted($name))):
					$action['options']['class'] .= ' hidden';
					break;
				}
				$ret_val .= Html::a(
					Html::tag(
						$action['tag'], 
						Icon::show(
							$action['text'], 
							['class' => 'fa-2x']
						)
					), 
					$action['action'].'/'.$this->parentType.'/'.$this->parentId,
					$action['options']
				);
				break;
			}
			
		}
		return Html::tag('div', $ret_val, ['class' => 'center-block text-center']);
	}
}
?>