<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\helpers;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use kartik\icons\Icon;

class BaseWidget extends Widget
{
	public $uniqid;
	public $items;
	public $useModal = true;
	/**
	 * Show the count even if the number is 0
	 */
	public $showEmptyCount = false;	
	public $fullDetails = true;	
	
	/*
	 * The options used to constrain the Revisions
	 */
	public $parentType;
	public $parentKey;
	public $parentId;
	public $model;
	
	/*
	 * HTML options for generating the widget
	 */
	public $options;
	
	/**
	 * The actions to enable
	 */
	public $actions = [];
	
	/*
	 * The number of replies to get on each select query
	 */
	public $limit = 10;
	
	/*
	 * Options for widget
	 */
	public $widgetOptions = [];
	/**
	 * Options for use in a modal
	 */
	public $modalOptions = [];
	
	/**
	 * Active form
	 */
	protected $_form = false;
	
	/**
	 * Does the user exist?
	 */
	protected $_userExists = false;
	
	/**
	 * The current user
	 */
	protected $_user;
	
	/**
	 * The current users
	 */
	protected $_users;
	
	public function init()
	{
		switch(empty($this->parentType) && !is_null($this->model))
		{
			/**
			 * This issue model was initialized through a model
			 * We need to set the parentId and parentType from the constraints values
			 */
			case true:
			$this->parentId = $this->model->parent_id;
			$this->parentType = $this->model->parent_type;
			switch($this->model->hasAttribute('parent_key'))
			{
				case true:
				$this->parentKey = $this->model->parent_key;
				break;
			}
			break;
		}
		if(!isset($this->uniqid))
			if(isset($this->parentType) && isset($this->parentId))
				$this->uniqid = '-'.$this->parentType.$this->parentId;
			else
				$this->uniqid = uniqid();
	}
	
	protected function getInfoLink($type=null)
	{
		$typeHr = ucfirst($type == null ? $this->model->isWhat() : $type);
		if($this->useModal) {
			$this->options['href'] .= '__format=modal';
			$info = \nitm\widgets\modal\Modal::widget([
				'options' => [
					'id' => $this->options['id'].'-modal'
				],
				'size' => 'large',
				'header' => $type,
				'toggleButton' => $this->options,
				'dialogOptions' => [
					'class' => 'modal-full'
				],
			]);
		} else {
			$this->widgetOptions['class'] = 'list-group';
			$header = $this->model->count().' '.$typeHr;
			$last = '';
			if(($this->model->count() >= 1) && (is_a($this->model->last, $this->model->className())) && $this->fullDetails)
			{
				$last = Html::tag('span', 'Last By '.$this->model->last->author()->url());
				$last .= Html::tag('span', " on ".$this->model->last->created_at, []);
			}
			$info = Html::tag('ul',
				Html::tag('li', 
					Html::tag('strong', $header, ['class' => 'list-group-item-heading'])
					.Html::tag('span', $last, ['class' => 'list-group-item-text', 'style' => 'margin-left: 15px'])
					.Html::tag('div', Html::a('View '.$typeHr.' '.Icon::show('eye'), $this->options['href'], [
						'role' => 'visibility',
						'data-id' => '#'.$type.'-for-'.$this->parentType.'-'.$this->parentId,
						'data-remote-once' => 1
					]), ['class' => 'pull-right']), [
						'class' => 'list-group-item '.($this->model->count() ? 'list-group-item-success' : ''),
						'style' => (!$this->model->count() ? 'background-color: transparent' : '')
					]),
			$this->widgetOptions)
			.Html::tag('div', '', [
				'id' => $type.'-for-'.$this->parentType.'-'.$this->parentId,
				'class' => 'hidden center-block',
				'style' => 'padding-bottom: 10px'
			]);
		}
		return $info;
	}
	
	protected function getNewIndicator()
	{
		$new = $this->model->hasNew();
		switch($new >= 1)
		{
			case true:
			$new = \nitm\widgets\activityIndicator\ActivityIndicator::widget([
				'type' => 'new',
				'position' => 'top right',
				'text' => Html::tag('span', $new." new")
			]);
			break;
			
			default:
			$new = '';
			break;
		}
		return $new;
	}
}