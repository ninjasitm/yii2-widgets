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
use nitm\widgets\models\User;
use nitm\widgets\models\Vote as VoteModel;
use nitm\widgets\helpers\BaseWidget;
use kartik\icons\Icon;

class Vote extends BaseWidget
{
	public $size;
		
	/**
	 * The actions to enable
	 */
	public $actions;
	
	/**
	 * Options for the vote Icons
	 * [
	 *		Action => [
	 *			'icon' => String icon being used
	 *			'text' => Text
	 *			'class' => The class for the icon
	 *		]
	 * ]
	 */
	public $iconOptions;
	
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
	
	private $_defaultIconOptions = [
		'up' => [
			'class' => 'fa',
			'text' => 'Up',
			'icon' => 'thumbs-up'
		],
		'down' => [
			'class' => 'fa',
			'text' => 'Down',
			'icon' => 'thumbs-down'
		],
		'reset' => [
			'class' => 'fa',
			'text' => 'Refresh',
			'icon' => 'refresh'
		]
	];
	
	/**
	 * The actions that are supported
	 */
	private $_actions = [
		'up' => [
			'tag' => 'span',
			'action' => '/vote/up',
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
			'options' => [
				'class' => 'col-lg-12 col-md-12 text-danger',
				'role' => 'resetVote',
				'id' => 'vote-reset',
				'title' => 'Reset the votes'
			],
			'adminOnly' => true
		],
	];
	
	public function init()
	{
		if ($this->model instanceof VoteModel && ($this->parentType == null) || ($this->parentId == null)) {
			$this->parentId = $this->model->parent_id;
			$this->parentType = $this->model->parent_type;
		} else if($this->parentType && $this->parentId)
			$this->model = VoteModel::findModel([$this->parentId, $this->parentType]);
		else 
			$this->model = new VoteModel([
				'parent_id' => $this->parentId,
				'parent_type' => $this->parentType
			]);
		$this->uniqid = uniqid();
		$this->iconOptions = !isset($this->iconOptions) ? $this->_defaultIconOptions: $this->iconOptions;
		parent::init();
		Asset::register($this->getView());
	}
	
	public function run()
	{
		$vote = '';
		switch(\Yii::$app->user->identity->isAdmin())
		{
			case true:
			break;
		}
		switch(VoteModel::$individualCounts)
		{
			case true:
			$positive = Html::tag(
				'div',
				Html::tag(
					'strong', 
					$this->model->rating()['positive'], 
					['id' => 'vote-value-positive'.$this->parentId]
				).(VoteModel::$usePercentages ? '%' : ""),
				['class' => 'text-success col-md-6 col-lg-6']
			);
			$negative = Html::tag(
				'div',
				Html::tag(
					'strong', 
					$this->model->rating()['negative'], 
					['id' => 'vote-value-negative'.$this->parentId]
				).(VoteModel::$usePercentages ? '%' : ""),
				['class' => 'text-danger col-md-6 col-lg-6']
			);
			break;
			
			default:
			$negative = '';
			$positive = Html::tag(
				'strong', 
				$this->model->rating()['positive'], 
				['id' => 'vote-value-positive'.$this->parentId]
			).(VoteModel::$usePercentages ? "%" :'');
			break;
		}
		$vote .= Html::tag('div', 
			$positive.$negative,
			['class' => 'center-block text-center']
		);
		$vote .= $this->getActions();
		$this->widgetOptions['id'] .= $this->parentId.$this->uniqid;
		return Html::tag('div', $vote, $this->widgetOptions).Html::script((new \yii\web\JsExpression("\$nitm.onModuleLoad('vote', function () {\$nitm.module('vote').init('".$this->widgetOptions['id']."');});")));
	}
	
	public function getActions()
	{
		$actions = is_null($this->actions) ? $this->_actions : array_intersect_key($this->_actions, $this->actions);
		$ret_val = '';
		foreach($actions as $name=>$action)
		{
			$iconOptions = !isset($this->iconOptions[$name]) ? ['clas' => $name, 'text' => $name, 'icon' => $name] : $this->iconOptions[$name];
			extract( $iconOptions, EXTR_PREFIX_ALL, '_');
			if(isset($action['adminOnly']) && ($action['adminOnly'] == true) && !\Yii::$app->user->identity->isAdmin())
			continue;
			
			$action['options']['id'] = $action['options']['id'].$this->parentId;
			switch(1)
			{
				case ($name == 'up') && (($this->model->rating()['positive'] >= $this->model->getMax()) || ($this->model->currentUserVoted($name))):
				case ($name == 'down') && (($this->model->rating()['positive'] <= 0) || ($this->model->currentUserVoted($name))):
				$action['options']['style'] = 'display:none';
				break;
			}
			$ret_val .= Html::a(
				Html::tag(
					$action['tag'], 
					$this->getActionHtml($__text, $__class, $__icon)
				), 
				$action['action'].'/'.$this->parentType.'/'.$this->parentId,
				$action['options']
			);
			
		}
		return Html::tag('div', $ret_val, ['class' => 'center-block text-center']);
	}
	
	protected function getActionHtml($text, $class, $icon)
	{
		$options = ['class' => $class];
		switch($class)
		{
			case 'fa':
			switch($this->size)
			{
				case 'large':
				$class .= '-2x';
				break;
				
				case 'x-large':
				$class .= '-4x';
				break;
			}
			$options['class'] = $class;
			break;
			
			default:
			switch($this->size)
			{
				case 'x-large':
				$style = 'font-size: 4rem';
				break;
				
				case 'large':
				$style = 'font-size: 2rem';
				break;
				
				default:
				$style = 'font-size: 1rem';
				break;
			}
			$options['style'] = $style;
			break;
		}
		return !empty($icon) ? Icon::show($icon, $options) : Html::span($text, ['class' => $class]);
	}
}
?>