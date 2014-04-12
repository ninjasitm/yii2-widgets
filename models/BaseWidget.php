<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use kartik\icons\Icon;

class BaseWidget extends Widget
{
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
	 * Options for replies
	 */
	public $widgetOptions = [
		'class' => '',
		'id' => ''
	];
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
		//Map kartick-v icons
		Icon::map($this->getView());
	}
}