<?php

namespace nitm\widgets\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use nitm\widgets\models\Category;
use nitm\widgets\models\search\Category as CategorySearch;
use nitm\helpers\Helper;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends \nitm\controllers\DefaultController
{
	public function init()
	{
		$this->model = new Category(['scenario' => 'default']);
		parent::init();
	}

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
		return parent::actionIndex(CategorySearch::className(), [
			'with' => [
				'parent'
			],
		]);
    }
}
