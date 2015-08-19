<?php

namespace nitm\widgets\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use nitm\widgets\models\Revisions;
use nitm\widgets\models\search\Revisions as RevisionsSearch;
use nitm\widgets\revisions\Revisions as RevisionsWidget;
use nitm\helpers\Response;

/**
 * RevisionsController implements the CRUD actions for Revisions model.
 */
class RevisionsController extends \nitm\controllers\DefaultController
{
	function init()
	{
		$this->model = new Revisions();
	}
	
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Revisions models.
     * @return mixed
     */
    public function actionIndex($type=null, $id=null)
    {
		Response::viewOptions(null, [
			'args' => [
				"content" => RevisionsWidget::widget([
					"parentId" => $id, 
					"parentType" => $type
				])
			],
			'modalOptions' => [
				'contentOnly' => true
			]
		], true);
		return $this->renderResponse(null, null, \Yii::$app->request->isAjax);
    }

    /**
     * Displays a single Revisions model.
     * @param integer $user_id
     * @param string $remote_type
     * @param integer $remote_id
     * @return mixed
     */
    public function actionView($id)
    {
        $ret_val = [
			'args' => [
            	'model' => $this->findModel(Revisions::className(), $id),
        	],
			'view' => 'view',
			'modalOptions' => [
				'contentOnly' => true
			]
		];
		$this->setResponseFormat('modal');
		return $this->renderResponse(null, $ret_val);
    }

    /**
     * Creates a new Revisions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type, $id)
    {
		$ret_val = [
			'success' => false,
			'message' => 'Unable to save the revision for $type: $id'
		];
		$fromExisting = false;
		
        $model = new Revisions;
		$model->setScenario('create');
		$model->parent_id = $id;
		$model->parent_type = $type;
		
		//Check to see if a revision was done in the last $model->interval interval
		$existing = Revisions::find()
			->select(['id', 'created_at'])
			->where([
				'parent_id' => $id,
				'parent_type' => $type,
				'author_id' => \Yii::$app->user->getId()
			])
			->orderBy(['id' => SORT_DESC])
			->one();
			
		if($existing instanceof Revisions && !$existing->isOutsideInterval()) {
			$model = $existing;
			$model->setScenario('update');
			$fromExisting = true;
		}
		
		$model->author_id = \Yii::$app->user->getId();
		$model->version = Revisions::find()->where([
			'parent_type' => $type,
			'parent_id' => $id
		])->count() + 1;
		
		$model->setAttribute('data', json_encode($_POST));

        $ret_val['success'] = $model->validate() && $model->save();
		
		if($fromExisting)
			$ret_val['message'] = 'Updated recent revision successfully!';
		else
			$ret_val['message'] = 'Saved revision successfully!';
			
		if(!Response::formatSpecified())
			$this->setResponseFormat('json');
			
		return $this->renderResponse($ret_val);
    }
	
	/**
	 * Restore a revision to the base model
	 */
	function actionRestore($id)
	{
		/**
		 * We need to find the class and then update the model
		 * May need to use a wiget search namespace in the module
		 */
		throw new \yii\web\BadRequestHttpException("Restoring isn't supported yet");
	}
}
