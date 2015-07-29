<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies;

use Yii;
use yii\helpers\Html;
use nitm\widgets\helpers\BaseWidget;
use nitm\widgets\models\User;
use nitm\widgets\models\Replies as RepliesModel;
use nitm\widgets\models\search\Replies as RepliesSearch;
use kartik\icons\Icon;

class Replies extends BaseWidget
{
	public $noContainer;
	public $uniqid;	
	public $formOptions = [];
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'role' => 'entityMessages',
		'id' => 'messages',
		'data-parent' => 'replyFormParent'
	];
	
	public function init()
	{
		switch(1)
		{
			case !($this->model instanceof RepliesModel) && (($this->parentType == null) || ($this->parentId == null) || ($this->parentKey == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof RepliesModel) ? $this->model : (new RepliesModel(['initSearchClass' => false]))->findModel([$this->parentId, $this->parentType, $this->parentKey]);
			break;
		}
		parent::init();
		$this->options = array_merge($this->defaultOptions(), $this->options);
		$this->options['id'] .= $this->uniqid;
		Asset::register($this->getView());
	}
	
	public function run()
	{
		$dataProvider = null;
		switch(isset($this->items) && is_array($this->items))
		{
			case true:
			$dataProvider = new \yii\data\ArrayDataProvider(["allModels" => $this->items]);
			break;
			
			default:
			switch(($this->model instanceof RepliesModel))
			{
				case true:
				$get = \Yii::$app->request->getQueryParams();
				$params = array_merge($get, $this->model->getConstraints());
				unset($params['type']);
				unset($params['id']);
			
				if(!\Yii::$app->user->identity->isAdmin())
					$params['hidden'] = false;
				
				$searchModel = new RepliesSearch([
					'queryOptions' => [
						'with' => ['author', 'replyTo', 'count', 'last']
					]
				]);
				
				$dataProvider = $searchModel->search([$this->model->formName() => $params]);
				$dataProvider->setSort([
					'defaultOrder' => [
						'id' => SORT_DESC,
					]
				]);
				break;
			}
			break;
		}
		switch(isset($dataProvider))
		{
			case true:
			$defaultOptions = [
				'parentId' => $this->parentId,
				'parentType' => $this->parentType,
				'uniqid' => $this->uniqid,
				'model' => $this->model,
			];
			$this->formOptions = array_merge($defaultOptions, $this->formOptions);
			$viewOptions = array_merge($defaultOptions, [
				'dataProvider' => $dataProvider,
				//'searchModel' => $searchModel,
				'widget' => $this,
			]);
			$replies = $this->getView()->render('@nitm/widgets/views/replies/index', $viewOptions);
			break;
			
			default:
			//$replies = Html::tag('h3', "No comments", ['class' => 'text-error']);
			$replies = '';
			break;
		}
		return $replies;
	}
	
	protected function defaultOptions()
	{
		return  [
			'role' => 'entityMessages',
			'id' => 'messages',
			'data-parent' => 'replyFormParent',
			'class' => 'messages'
		];
	}
	
	protected function defaultActions()
	{
		return [
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
	}
}
?>