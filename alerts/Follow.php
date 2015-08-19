<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\alerts;

use Yii;
use yii\helpers\Html;
use nitm\widgets\models\Alerts;
use nitm\helpers\Icon;
use nitm\helpers\ArrayHelper;

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
	 *		'type' => [danger, info, warning, success, default, info]
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
		['label' => 'Email', 'url' => '#', 'icon' => 'envelope', 'method' => 'email', 'data-ajax-method' => 'post'],
		['label' => 'Mobile', 'url' => '#', 'icon' => 'mobile', 'method' => 'mobile', 'data-ajax-method' => 'post'],
		['label' => 'Send', 'url' => '#', 'icon' => 'send', 'method' => 'any', 'data-ajax-method' => 'post'],
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
		$this->options['id'] .= $this->uniqid;
		$this->options['data-ajax-method'] = 'post';
		$this->initializeMethods();
		FollowAsset::register($this->getView());
	}
	
	public function run()
	{
		$label = 'Follow';
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
				$this->options['class'] = 'btn-success';
				$this->options['data-url'] = '/alerts/un-follow/'.$this->model->getId();
				$this->options['role'] .= ' dynamicValue';
				$this->options['data-run-once'] = true;
				$this->options['data-type'] = 'callback';
				$this->options['data-ajax-method'] = 'post';
				$this->options['data-callback'] = 'function (result, elem) {$nitm.module("follow").afterAction(result, elem);}';
				break;
			}
			break;
			
			default:
			break;
		}
		
		$this->getButtonClass();
		
		$this->options['onchange'] = '$nitm.module("tools").dynamicValue(this);';
		$ret_val = \yii\bootstrap\ButtonDropdown::widget([
			'encodeLabel' => false,
			'split' => true,
			'label' => $label,
			'dropdown' => [
				'encodeLabels' => false,
				'options' => [
					'class' => $this->model->getIsNewRecord() ? '' : 'disabled',
					'role' => 'followDropdown',
					'data-ajax-method' => 'post'
				],
				'items' => $this->followMethods
			],
			'options' => $this->options
		]);
		if($this->model->getId())
			$ret_val .= Html::script("\$nitm.addOnLoadEvent(function () {\$('#".$this->options['id']."').parent().find(\"[class~='dropdown-toggle']\").addClass('disabled');});", ["type" => "text/javascript"]);
		return $ret_val;
	}
	
	protected function initializeMethods()
	{
		$this->followMethods = array_merge($this->_defaultFollowMethods, $this->followMethods);
		$supported = \nitm\components\alerts\DispatcherData::supportedMethods();
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
					'data-ajax-method' => 'post',
					'data-id' => '#'.$this->options['id'],
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
	
	protected function getButtonClass($size=null, $type=null)
	{
		$size = ArrayHelper::remove($this->buttonOptions, 'size', 'default');
		$type = ArrayHelper::remove($this->buttonOptions, 'type', 'default');
		switch($size)
		{
			case 'large':
			case 'lg':
			$size = 'btn-lg';
			break;
			
			case 'small':
			case 'sm':
			$size = 'btn-sm';
			break;
			
			case 'extra-small':
			case 'xs':
			$size = 'btn-xs';
			break;
			
			default:
			$size = 'btn-default';
			break;
		}
		
		switch($type)
		{
			case 'success':
			$type = 'btn-success';
			break;
			
			case 'danger':
			case 'error':
			$type = 'btn-danger';
			break;
			
			case 'info':
			$type = 'btn-info';
			break;
			
			case 'warning':
			$type = 'btn-warning';
			break;
			
			case 'primary':
			$type = 'btn-primary';
			break;
			
			default:
			$type = 'btn-default';
			break;
		}
		Html::addCssClass($this->options, [
			'size' => $size,
			'type' => $type
		]);
		return $size.' '.$type;
	}
}
?>