<?php

namespace nitm\widgets\controllers;

use Yii;
use nitm\widgets\models\Alerts;
use nitm\widgets\models\search\Alerts as AlertsSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use nitm\helpers\Response;

/**
 * AlertsController implements the CRUD actions for Alerts model.
 */
class AlertsController extends \nitm\controllers\DefaultController
{	
	public function init()
	{
		$this->logCollection = 'nitm-log';
		$this->model = new Alerts(['scenario' => 'default']);
		parent::init();
	}
	
	public function beforeAction($action)
	{
		switch($action->id)
		{
			case 'list':
			$this->enableCsrfValidation = false;
			break;
		}
		return parent::beforeAction($action);
	}
    public function behaviors()
    {
		$behaviors = [
			'access' => [
				//'class' => \yii\filters\AccessControl::className(),
				'rules' => [
					[
						'actions' => [
							'notifications', 
							'mark-notification-read', 
							'get-new-notifications',
							'un-follow',
							'follow'
						],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'actions' => [
					'follow' => ['post'],
					'un-follow' => ['post'],
				],
			],
		];
		return array_merge_recursive(parent::behaviors(), $behaviors);
    }
	
    /**
     * Lists all Alerts models.
     * @return mixed
     */
    public function actionIndex()
    {
        Response::viewOptions('args.content', \nitm\widgets\alerts\Alerts::widget());
		$this->setResponseFormat('html');
		return $this->renderResponse(null, Response::viewOptions(), \Yii::$app->request->isAjax);
    }
	
    /**
     * Lists all Notifications models.
     * @return mixed
     */
    public function actionNotifications()
    {
        Response::viewOptions('args.content', \nitm\widgets\alerts\Notifications::widget([
			'contentOnly' => (bool)\Yii::$app->request->get('__contentOnly')
		]));
		$this->setResponseFormat('html');
		return $this->renderResponse(null, Response::viewOptions(), \Yii::$app->request->isAjax);
    }
	
    /**
     * Mark notification read.
     * @return mixed
     */
    public function actionMarkNotificationRead($id)
    {
		$this->model = \nitm\widgets\models\Notification::findOne($id);
		if($this->model)
		{
			$this->model->read = true;
			$ret_val = $this->model->save();
		}
		else
		{
			$ret_val = false;
		}
		$this->setResponseFormat('json');
		return $this->renderResponse($ret_val, Response::viewOptions(), \Yii::$app->request->isAjax);
    }
	
	public function actionFollow($type, $id, $key)
	{
		$_REQUEST['do'] = true;
		\Yii::$app->request->setBodyParams([
			$this->model->formName() => [
				'remote_id' => $id,
				'remote_type' => $type,
				'methods' => $key,
				'action' => 'any'
			],
		]);
		$ret_val = [
			'message' => "There was an error following this ".$type.". Please try again and report this if it keeps happening",
			'success' => false
		];
		try {
			$ret_val = parent::actionCreate();
			$result['message'] = 'Successfully followed '.$type;
			if(ArrayHelper::getValue((array)$ret_val, 'success', false) === true)
			{
				$ret_val['data'] = '';
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
				$ret_val['actionHtml'] = 'Unfollow '.\nitm\helpers\Icon::show($methods);
				$ret_val['class'] = 'btn-success';
			}
			
		} catch(\Exception $e) {
			if(YII_DEBUG)
				throw $e;
		}
		if(ArrayHelper::getValue((array)$ret_val, 'success', false))
			\nitm\traits\Relations::setCachedRelationModel($this->model, ['remote_id', 'remote_type'], 'followModel');
		return $ret_val;
	}
	
	public function actionUnFollow($id)
	{
		$ret_val = [
			'message' => "Couldn't unfollow this for some reason",
			'success' => false
		];
		try {
			$ret_val = parent::actionDelete($id);
		} catch(\Exception $e) {
			if(YII_DEBUG)
				throw $e;
		}
		if($ret_val['success']) {
			\nitm\traits\Relations::deleteCachedRelationModel($this->model, ['remote_id', 'remote_type'], 'followModel');
			$ret_val['message'] = 'Successfully un-followed '.$this->model->isWhat();
		}
		$ret_val['actionHtml'] = 'Follow';
		$ret_val['class'] = 'btn-default';
		return $ret_val;
	}
	
    public static function booleanActions()
	{
		return array_merge(parent::booleanActions(), [
			'un-follow' => [
				'scenario' => 'follow',
				'attributes' => [
					'attribute' => 'id',
					'blamable' => 'user_id',
					'date' => 'created_at'
				],
				'title' => [
					'Unfollow',
					'Follow'
				]
			]
		]);
	}
	
	/**
     * Lists all new Replies models according to user activity.
	 * @param string $type The parent type of the issue
	 * @param int $id The id of the parent
	 * @param string $key The key of the parent
     * @return mixed
     */
    public function actionGetNewNotifications()
    {
		$this->model = new \nitm\widgets\models\Notification([
			'constrain' => [
				'user_id' => \Yii::$app->user->getId(),
				'read' => false
			],
		]);
		$ret_val = false;
		$new = $this->model->hasNew();
		switch($new >= 1)
		{
			case true:
			$ret_val = [
				'data' => '',
				'count' => $new,
				'success' => true
			];
			$ret_val['message'] = $ret_val['count']." new notifications";
			$searchModel = new \nitm\widgets\models\search\Notification([
				'queryOptions' => [
					'andWhere' => new \yii\db\Expression('UNIX_TIMESTAMP(created_at)>='.\Yii::$app->user->getIdentity()->lastActive())
				]
			]);
			$dataProvider = $searchModel->search($this->model->constraints);
			$dataProvider->setSort([
				'defaultOrder' => [
					'id' => SORT_DESC,
				]
			]);
			$newReplies = $dataProvider->getModels();
			foreach($newReplies as $newReply)
			{
				$ret_val['data'] .= $this->renderAjax('@nitm/views/alerts/view-notification', ['model' => $newReply, 'isNew' => true]);
			}
			Response::viewOptions(null, [
				'args' => [
					"content" => $ret_val['data'],
				],
				'modalOptions' => [
					'contentOnly' => true
				]
			]);
			break;
			
			default:
			Response::viewOptions(null, [
				'args' => [
					"content" => $ret_val,
				],
			]);
			break;
		}
		$this->setResponseFormat(\Yii::$app->request->isAjax ? 'json' : 'html');
		return $this->renderResponse($ret_val, null, \Yii::$app->request->isAjax);
    }
	
	/*
	 * Get the forms associated with this controller
	 * @param string $param What are we getting this form for?
	 * @param int $unique The id to load data for
	 * @return string | json
	 */
	public function actionForm($type=null, $id=null)
	{
		$options = [
			'modelOptions' => [
			],
			'title' => function ($model) {
				if($model->isNewRecord)
					return "Create Alert";
				else
					$header = 'Update Alert: '
					.' Matching '.$model->properName($model->priority)
					.' '.($model->remote_type == 'any' ? 'Anything' : $model->properName($model->remote_type));
					if(!empty($model->remote_for) && !($model->remote_for == 'any'))
						$header .= ' for '.$model->properName($model->remote_for);
					if(!empty($model->remote_id))
						$header .= ' '.(!$model->remote_id ? 'with Any id' : ' with id '.$model->remote_id);
					return $header;
			}
		];
		$options['force'] = true;
		return parent::actionForm($type, $id, $options);
	}
	
	public function actionList($type)
	{
		$this->setResponseFormat('json');
		$types = [];
		$dependsOn = \Yii::$app->request->post('depdrop_parents')[0];
		switch($type)
		{	
			case 'for':
			switch($dependsOn)
			{
				case 'issue':
				case 'replies':
				$types = (array)$this->model->setting('for');
				$ret_val = [
					"output" => array_map(function ($key, $value) {
						return [
							'id' => $key,
							'name' => $value
						];
					}, array_keys($types), array_values($types)), 
					"selected" => 0
				];
				array_unshift($ret_val['output'], ['id' => 0, 'name' => " for one of the following "]);
				break;
				
				default:
				$ret_val = ["output" => [['id' => 'any', 'name' => "then ignore what its for"]], "selected" => 'any'];
				break;
			}
			break;	
			
			case 'priority':
			switch(1)
			{
				case in_array($dependsOn, (array)array_keys($this->model->setting('priority_allowed'))):
				case $dependsOn == 'chat':
				$types = $this->model->setting('priorities');
				$ret_val = [
					"output" => array_map(function ($key, $value) {
						return [
							'id' => $key,
							'name' => $value
						];
					}, array_keys($types), array_values($types)), 
					"selected" => ''
				];
				array_unshift($ret_val['output'], ['id' => 0, 'name' => " but if the priority is "]);
				break;
				
				default:
				$ret_val = ["output" => [['id' => 'any', 'name' => "and ignore the priority"]], "selected" => "any"];
				break;
			}
			break;
			
			case 'types':
			switch($dependsOn)
			{
				case 'any':
				case 'create':
				case 'update':
				case 'i_create':
				case 'i_update':
				case 'complete':
				case 'resolve':
				case 'update_my':
				case 'resolve_my':
				case 'complete_my':
				$types = (array)$this->model->setting('allowed');
				break;
				
				case 'reply_my':
				case 'reply':
				$types =(array) $this->model->setting('reply_allowed');
				$types['chat'] = 'Chat';
				break;
			
				default:
				$types = ['any' => 'Anything'];
				break;
			}
			ksort($types);
			$ret_val = [
				"output" => array_map(function ($key, $value) {
					return [
						'id' => $key,
						'name' => $value
					];
				}, array_keys($types), array_values($types)), 
				"selected" => ''
			];
			//array_unshift($ret_val['output'], ['id' => 'any', 'name' => "Anything"]);
			break;
		}
		return $this->renderResponse($ret_val, null, true);
	}
}