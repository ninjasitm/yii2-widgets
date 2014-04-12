<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\rating;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use nitm\models\User;
use nitm\models\Rating as RatingModel;
use nitm\widgets\models\BaseWidget;
use kartik\icons\Icon;

class Rating extends BaseWidget
{
	
	/**
	 * The actions to enable
	 */
	public $actions;
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'rating',
		'role' => 'entityRating',
		'id' => 'rating',
	];
	
	/*
	 * Options for Rating
	 */
	public $ratingOptions = [
		'class' => '',
		'id' => ''
	];
	
	/*
	 * The number of Rating to get on each select query
	 */
	public $limit = 10;
	
	/**
	 * \commond\models\rating $rating
	 */
	public $rating;
	
	/**
	 * Does the user exist?
	 */
	private $_userExists = false;
	
	/**
	 * The current user
	 */
	private $_user;
	/**
	 * The current users
	 */
	private $_users;
	
	/**
	 * The actions that are supported
	 */
	private $_actions = [
		'up' => [
			'tag' => 'span',
			'action' => '/rating/up',
			'text' => 'thumbs-up',
			'options' => [
				'class' => 'col-md-6 col-lg-6',
				'role' => 'ratingUp',
				'id' => 'rating-up',
				'title' => 'Rate this up'
			]
		],
		'down' => [
			'tag' => 'span',
			'action' => '/rating/down',
			'text' => 'thumbs-down',
			'options' => [
				'class' => 'col-md-6 col-lg-6',
				'role' => 'ratingDown',
				'id' => 'rating-down',
				'title' => 'Rate this down'
			]
		],
		'reset' => [
			'tag' => 'span',
			'action' => '/rating/reset',
			'text' => 'refresh',
			'options' => [
				'class' => 'col-lg-12 col-md-12',
				'role' => 'resetRating',
				'id' => 'rating-reset',
				'title' => 'Reset the ratings'
			],
			'adminOnly' => true
		],
	];
	
	public function init()
	{
		if (($this->parentType == null) || ($this->parentId == null)) {
			throw new InvalidConfigException('The parentType and parentId properties must be set.');
		}
		Icon::map($this->getView());
	}
	
	public function run()
	{
		$r = new RatingModel(['parent_id' => $this->parentId, 'parent_type' => $this->parentType]);
		$rating = '';
		switch(\nitm\models\User::isAdmin())
		{
			case true:
			break;
		}
		$rating .= Html::tag('div', 
			Html::tag(
				'strong', 
				$r->getRating($this->model), 
				['id' => 'rating-value'.$this->model->getUnique()]
			)."%",
			['class' => 'center-block text-center']
		);
		$rating .= $this->getActions();
		$this->options['id'] .= $this->parentId;
		echo Html::tag('div', $rating, $this->options);
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
				switch(\Yii::$app->userMeta->isAdmin())
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