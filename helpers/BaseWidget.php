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
	public $actions;
	
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
		$this->uniqid = isset($this->uniqid) ? $this->uniqid : uniqid();
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
	}
}