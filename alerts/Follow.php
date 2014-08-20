<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\alerts;

use Yii;
use yii\helpers\Html;
use nitm\models\Alerts;
use nitm\helpers\Icon;

class Follow extends \yii\base\Widget
{	
	public $model;
	public $uniqid;
	public $remoteId;
	public $remoteType;
	public $remoteFor;
	public $userId;
	public $inline = true;
	/**
	 * Array of items for the button dropdown.
	 */
	public $followMethods = [];
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'btn btn-default',
		'role' => 'followContainer',
		'id' => 'follow',
		'role' => 'followButton'
	];
	
	/**
	 * Options for the button
	 * [
	 *		'size' => [xs, sm, lg]
	 *		'type' => [danger, info, warning, primary, default, info]
	 * ]
	 */
	public $buttonOptions = [
	];
	
	/**
	 * Options for the dropdown/follow options
	 * [
	 *		'label' => String
	 *		'url' => String
	 *		'icon' => String
	 *		'method' => String
	 * ]
	 */
	protected $_defaultFollowMethods = [
		['label' => 'Email', 'url' => '#', 'icon' => 'envelope', 'method' => 'email'],
		['label' => 'Mobile', 'url' => '#', 'icon' => 'mobile', 'method' => 'mobile'],
		['label' => 'Send', 'url' => '#', 'icon' => 'send', 'method' => 'any'],
	];
	
	
	public function init()
	{
		parent::init();
		switch(1)
		{
			case !($this->model instanceof Alerts) && (($this->remoteType == null) || ($this->remoteId == null)):
			$this->model = new Alerts;
			break;
			
			default:
			$this->model = ($this->model instanceof Alerts) ? $this->model : Alerts::find()->where([
				'remote_id' => $this->remoteId, 
				'remote_type' => $this->remoteType, 
				'remote_for' => $this->remoteFor, 
				'user_id' => \Yii::$app->user->getId()
			])->one();
			break;
		}
		$this->uniqid = uniqid();
		$this->initializeMethods();
		assets\FollowAsset::register($this->getView());
	}
	
	public function run()
	{
		$label = 'Follow';
		$this->options['id'] .= $this->uniqid;
		switch(($this->model instanceof Alerts))
		{
			case true:
			switch($this->model->user_id == \Yii::$app->user->getId())
			{
				case true:
				switch($this->model->methods)
				{
					case 'email':
					$methods = 'envelope';
					break;
					
					case 'mobile':
					$methods = 'mobile';
					break;
					
					default:
					$methods = 'send';
					break;
				}
				$label = 'Unfollow '.Icon::show($methods);
				$this->options['class'] = 'btn-primary';
				$this->options['data-url'] = '/alerts/un-follow/'.$this->model->getId();
				$this->options['role'] .= ' dynamicValue';
				$this->options['data-run-once'] = true;
				$this->options['data-type'] = 'callback';
				$this->options['data-callback'] = "function (result, elem) {\$nitm.module('follow').afterAction(result, elem);}";
				break;
			}
			break;
			
			default:
			break;
		}
		if(isset($this->buttonOptions['size']))
			$this->options['class'].= ' btn-'.$this->buttonOptions['size'];
		if(isset($this->buttonOptions['type']))
			$this->options['class'].= ' btn-'.$this->buttonOptions['type'];
			
		if(!isset($this->buttonOptions['type']))
			$this->options['class'].= ' btn-default';
		
		$ret_val = \yii\bootstrap\ButtonDropdown::widget([
			'encodeLabel' => false,
			'split' => true,
			'label' => $label,
			'dropdown' => [
				'encodeLabels' => false,
				'options' => [
					'class' => '',
					'role' => 'followDropdown'
				],
				'items' => $this->followMethods
			],
			'options' => $this->options
		]);
		$ret_val .= Html::script("\$nitm.onModuleLoad('tools', function () {\$nitm.module('tools').initDynamicValue('".$this->options['id']."');});", ['type' => 'text/javascript']);
		if(!$this->model->getIsNewRecord())
			$ret_val .= Html::script("\$nitm.addOnLoadEvent(function () {\$('#".$this->options['id']."').parent().find(\"[role~='followButton']\").last().addClass('disabled');});", ["type" => "text/javascript"]);
		return $ret_val;
	}
	
	protected function initializeMethods()
	{
		$this->followMethods = array_merge($this->_defaultFollowMethods, $this->followMethods);
		$supported = \nitm\helpers\alerts\Dispatcher::supportedMethods();
		foreach($this->followMethods as $idx=>$method)
		{
			switch(isset($supported[$method['method']]))
			{
				case true:
				$method['label'] = Icon::show($method['icon']).' '.$method['label'];
				$method['url'] = '/alerts/follow/'.$this->model->remote_type.'/'.$this->model->remote_id.'/'.$method['method'];
				$method['linkOptions'] = [
					'role' => 'dynamicValue',
					'data-pjax' => 0,
					'data-run-once' => true,
					'data-type' => 'callback',
					'data-callback' => "function (result, elem) { \$nitm.module('follow').afterAction(result, elem);}"
				];
				$this->followMethods[$idx] = $method;
				break;
				
				default:
				unset($this->followMethods[$idx]);
				break;
			}
		}
	}
}
?>