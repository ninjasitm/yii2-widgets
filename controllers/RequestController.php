<?php

namespace nitm\widgets\controllers;

use nitm\widgets\models\Request;
use nitm\widgets\models\search\Request as RequestSearch;
use yii\db\Expression;
use nitm\helpers\Response;

/**
 * RequestController implements the CRUD actions for Request model.
 */
class RequestController extends \nitm\controllers\DefaultController
{
    public $legend = [
        'success' => 'Closed and Completed',
        'warning' => 'Open',
        'danger' => 'Closed and Incomplete',
        'info' => 'Completed',
    ];

    public function init()
    {
        $this->addJs('@nitm/widgets/assets/js/requests', true);
        parent::init();
        $this->model = new Request(['scenario' => 'default']);
    }

    public function behaviors()
    {
        $behaviors = [
        ];

        return array_merge(parent::behaviors(), $behaviors);
    }

    public static function has()
    {
        return [
        ];
    }

    protected function getWith()
    {
        return array_merge(parent::getWith(), [
            'author', 'editor', 'type', 'requestFor', 'completedBy', 'closedBy',
            'reply', 'issue', 'revision', 'vote', 'follow',
        ]);
    }

    /**
     * Lists all Request models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $queryOptions = [];
		$orderByQuery = $this->getOrderByQuery();

        switch ((sizeof(\Yii::$app->request->get()) == 0)) {
            case true:
            $queryOptions = array_merge([
				'distinct' => true,
                'select' => [
                    $this->model->tableName().'.*',
                    \nitm\helpers\QueryFilter::getHasNewQuery($this->model),
                ],
                'andWhere' => ['closed' => false],
            ], $queryOptions);
            break;
        }

        return parent::actionIndex(RequestSearch::className(), [
            'with' => $this->getWith(),
            'construct' => [
                'queryOptions' => $queryOptions,
                'defaults' => [
                    'orderby' => $orderByQuery,
                    'params' => ['closed' => false],
                ],
            ],
        ]);
    }

    public function actionFilter()
    {
        $options = [
            'namespace' => '\nitm\widgets\models\search\\',
            'className' => '\nitm\widgets\models\search\Request',
            'view' => 'data',
            'with' => $this->getWith(),
        ];

        return parent::actionFilter($options);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return parent::actionUpdate($id, null, ['completedBy', 'closedBy']);
    }

    /**
     * Displays a single model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id, $modelClass = null, $with = [])
    {
        Response::$forceAjax = true;

        return parent::actionView($id);
    }

    /*
     * Get the forms associated with this controller
     * @param string $type What are we getting this form for?
     * @param int $unique The id to load data for
     * @return string | json
     */
    public function actionForm($type = null, $id = null)
    {
        $options = [
            'modelOptions' => [
                'queryOptions' => [
                    'with' => ['type', 'requestFor'],
                ],
            ],
        ];

        return parent::actionForm($type, $id, $options);
    }

    /**
     * Get the query that orders items by their activity.
     */
    protected function getOrderByQuery()
    {
		$isWhat = $this->model->isWHat();
		$remoteTable = $this->model->tableName();
		$voteTable = \nitm\widgets\models\Vote::tableName();
        $localOrderBy = [
			/*serialize(new Expression('COALESCE('.implode(', ', array_map(function ($table) {
				return implode(', ', array_map(function($field) use($table) {
					return $table.'.'.$field;
				}, ['created_at', 'updated_at']));
			}, [
				\nitm\widgets\models\Issues::tableName(),
				\nitm\widgets\models\Replies::tableName(),
				\nitm\widgets\models\Revisions::tableName()
			])).')')) => SORT_DESC,*/
			'issue.created_at' => SORT_DESC,
			'issue.updated_at' => SORT_DESC,
			'reply.created_at' => SORT_DESC,
			'reply.updated_at' => SORT_DESC,
			'revision.created_at' => SORT_DESC,
            serialize(new Expression("(SELECT COUNT(*) FROM $voteTable WHERE
				$voteTable.parent_id=$remoteTable.id AND
				$voteTable.parent_type='$isWhat'
			)")) => SORT_DESC,
            serialize(new Expression("(CASE $remoteTable.status
				WHEN 'normal' THEN 0
				WHEN 'important' THEN 1
				WHEN 'critical' THEN 2
			END)")) => SORT_DESC,
        ];

        return array_merge($localOrderBy, \nitm\helpers\QueryFilter::getOrderByQuery($this->model));
    }
}
