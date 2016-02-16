<?php

namespace nitm\widgets\controllers;

use Yii;
use nitm\widgets\models\Issues;
use nitm\widgets\models\search\Issues as IssuesSearch;
use nitm\helpers\Response;
use nitm\helpers\ArrayHelper;
use nitm\helpers\Icon;
use nitm\widgets\issueTracker\IssueTracker;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;

/**
 * IssueController implements the CRUD actions for Issues model.
 */
class IssueController extends \nitm\controllers\DefaultController
{
	use \nitm\traits\Controller;

	public $legend = [
		'success' => 'Closed and Resolved',
		'warning' => 'Closed and Unresolved',
	];

	protected $result;
	protected $enableComments;

	public function init()
	{
		parent::init();
		$this->model = new Issues(['scenario' => 'default']);
		$this->enableComments = (\Yii::$app->request->get(Issues::COMMENT_PARAM) == true) ? true : false;
	}

    public function behaviors()
    {
		$behaviors = [
			'access' => [
				'rules' => [
					[
						'actions' => ['issues', 'duplicate'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
        return array_merge_recursive(parent::behaviors(), $behaviors);
    }

	public static function has()
	{
		$has = [
			'\nitm\widgets\issueTracker'
		];
		return array_merge(parent::has(), $has);
	}

    /**
     * Lists all Issues models.
	 * @param string $type The parent type of the issue
	 * @param int $id The id of the parent
     * @return mixed
     */
    public function actionIndex($type=null, $id=null)
    {
		Response::viewOptions(null, [
			'args' => [
				"content" => IssueTracker::widget([
					"parentId" => $id,
					"parentType" => $type,
					'useModal' => false,
					'enableComments' => \Yii::$app->request->get(Issues::COMMENT_PARAM)
				])
			],
			'modalOptions' => [
				'contentOnly' => true
			]
		], true);
		return $this->renderResponse(null, null, \Yii::$app->request->isAjax);
    }

	public function actionCreate($modelClass=null, $viewOptions=[])
	{
		$result = parent::actionCreate($modelClass, $viewOptions);
		if(ArrayHelper::getValue($result, 'success', false) === true)
			$result = array_merge($result, [
				'data' => $this->renderPartial('view', [
					'model' => $this->model,
					'asListItem' => true
				]),
				'message' => 'Sucessfully created new issue!'
			]);
		return $result;
	}

    /**
     * Displays a single Issues model.
     * @param integer $id
     * @return mixed
     */
    public function actionIssues($type, $id, $key=null)
    {
		switch($type)
		{
			case 'all':
			$this->model = new Issues();
			break;

			default:
			$this->model = Issues::findModel([$id, $type]);
			break;
		}
		$searchModel = new IssuesSearch([
			'queryOptions' => [
				'with' => ['closedBy', 'resolvedBy', 'count']
			]
		]);
		$get = \Yii::$app->request->getQueryParams();
		$params = array_merge($get, $this->model->constraints);
		unset($params['type'], $params['id'], $params['key']);

		$options = [
			'enableComments' => $this->enableComments
		];
		switch($key)
		{
			case 'duplicate':
			$params = array_merge($params, ['duplicate' => true]);
			$orderBy = ['id' => SORT_DESC];
			break;

			case 'closed':
			$params = array_merge($params, ['closed' => true]);
			$orderBy = ['closed_at' => SORT_DESC];
			break;

			case 'open':
			$params = array_merge($params, ['closed' => false]);
			$orderBy = ['id' => SORT_DESC];
			break;

			case 'resolved':
			$params = array_merge($params, ['resolved' => true]);
			$orderBy = ['resolved_at' => SORT_DESC];
			break;

			case 'unresolved':
			$params = array_merge($params, ['resolved' => false]);
			$orderBy = ['id' => SORT_DESC];
			break;

			default:
			$orderBy = [];
			break;
		}
		$dataProvider = $searchModel->search($params);
		$dataProvider->query->orderBy($orderBy);
		Response::viewOptions(null, [
			'args' => [
				"content" => $this->renderAjax('issues', [
					'enableComments' => $this->enableComments,
					'searchModel' => $searchModel,
					'dataProvider' => $dataProvider,
					'options' => $options,
					'parentId' => $id,
					'parentType' => $type,
					'filterType' => $key
				])
			],
			'modalOptions' => [
				'contentOnly' => true
			]
		], true);
		//$this->setResponseFormat(\Yii::$app->request->isAjax ? 'modal' : 'html');
		return $this->renderResponse(null, null, \Yii::$app->request->isAjax);
    }

	public function actionDuplicate($id)
	{
		return $this->booleanAction($this->action->id, $id);
	}

    public static function booleanActions()
	{
		return array_merge(parent::booleanActions(), [
			'duplicate' => [
				'scenario' => 'duplicate',
				'attributes' => [
					'attribute' => 'duplicate',
					'blamable' => 'duplicated_by',
					'date' => 'duplicated_at'
				],
				'title' => [
					'Duplicate',
					'Set as Unique'
				],
				'afterAction' => function ($model) {
					$model->load(\Yii::$app->request->post());
					switch(is_array($model->duplicate_id))
					{
						case true:
						$model->duplicate_id = implode(',', $model->duplicate_id);
						break;
					}
				}
			]
		]);
	}
}
