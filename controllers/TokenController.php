<?php

namespace nitm\widgets\controllers;

use yii\web\NotFoundHttpException;
use nitm\widgets\models\search\Token as TokenSearch;
use nitm\widgets\models\api\Token;
use nitm\interfaces\nitm\controllers\DefaultControllerInterface;

/**
 * TokenController implements the CRUD actions for Token model.
 */
class TokenController extends \nitm\controllers\DefaultController
{
	
	public $legend = [
		'success' => 'Active Token',
		'danger' => 'Revoked Token',
		'default' => 'Inactive Token',
	];
	
	public function init()
	{
		$this->model = new Token(['scenario' => 'default']);
		parent::init();
	}
	
	public function behaviors()
	{
		$behaviors = [
			'access' => [
				'rules' => [
					[
						'actions' => ['generate'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
        return array_merge_recursive(parent::behaviors(), $behaviors);
	}
	
	public static function has ()
	{
		return [];
	}

    /**
     * Lists all Token models.
     * @return mixed
     */
    public function actionIndex()
    {
		return parent::actionIndex(TokenSearch::className(), [
			'with' => [
				'user'
			],
		]);
    }
	
	/**
	 * Generates a token for a specific user
	 * @param integer $id
	 * @return string
	 */
	public function actionGenerate()
	{
		$token = new Token();
		return $token->getUniqueToken((int) $id);
	}
}
