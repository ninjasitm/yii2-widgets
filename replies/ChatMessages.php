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

class ChatMessages extends Replies
{
	/*
	 * Interval for updating new chat and chat info
	 */
	public $updateOptions = [
		"interval" => 60000,
		"enabled" => true,
		'url' => 'reply/get-new/chat/0'
	];
	
	public $withForm = false;
	 
	/*
	 * HTML options for the chat message container
	 */
	public $options = [
		'role' => 'chatFormParent',
		'id' => 'chat',
		'class' => 'chat',
	];
	
	/*
	 * HTML options for generating the widget
	 */
	public $listOptions = [
		'class' => 'chat-messages',
		'role' => 'chatMessages',
		'id' => 'chat-messages',
		'data-parent' => 'chatParent'
	];
	
	public function init()
	{
		$this->model = ($this->model instanceof RepliesModel) ? $this->model : new RepliesModel([
			'constrain' => [
				'type' => 'chat'
			]
		]);
		parent::init();
	}
	
	public function run()
	{
		switch(($this->model instanceof RepliesModel))
		{
			case true:
			switch(empty($this->parentId))
			{
				/**
				 * This issue model was initialed through a model
				 * We need to set the parentId and parentType from the constraints values
				 */
				case true:
				//$this->parentId = $this->model->constraints['parent_id'];
				//$this->parentType = $this->model->constrain['parent_type'];
				break;
			}
			$searchModel = new RepliesSearch;
			$this->model->queryOptions['with'][] = 'replyTo';
			$searchModel->addWith($this->model->queryOptions['with']);
			$get = \Yii::$app->request->getQueryParams();
			$params = array_merge($get, $this->model->constraints);
			unset($params['type']);
			unset($params['id']);
			
			switch(\Yii::$app->user->identity->isAdmin())
			{
				case false:
				$params['hidden'] = 0;
				break;
			}
	
			$dataProvider = $searchModel->search($params);
			$dataProvider->setSort([
				'defaultOrder' => [
					'id' => SORT_DESC,
				]
			]);
			$this->options['id'] .= $this->parentId;
			$replies = $this->getView()->render('@nitm/widgets/views/chat/index', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel ,
				'widget' => $this,
			]);
			//RepliesAsset::register($this->getView());
			break;
			
			default:
			//$replies = Html::tag('h3', "No comments", ['class' => 'text-error']);
			$replies = 'No Replies';
			break;
		}
		$this->options['id'] .= $this->parentId;
		return $replies;
	}
}
?>