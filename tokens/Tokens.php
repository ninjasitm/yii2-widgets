<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\tokens;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use nitm\models\User;
use nitm\models\Revisions as RevisionsModel;
use nitm\widgets\models\BaseWidget;
use kartik\icons\Icon;

class Tokens extends BaseWidget
{
	/*
	 * Either a user object or a userid
	 */
	public $identity;
	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'messages',
		'role' => 'entityMessages',
		'id' => 'messages'
	];
	
	
	public function init()
	{	
		if ($this->identity === null) {
			throw new InvalidConfigException('Need some user info in order to work');
		}
		$this->identity = ($this->identity instanceof User) ? $this->identity : User::find($this->identity);
	}
	
	public function run()
	{
		switch($this->identity->hasApiTokens())
		{
			case true:
			$dataProvider = new \yii\data\ArrayDataProvider([
				'allModels' => $this->identity->getApiTokens(),
			]);
			echo \yii\grid\GridView::widget([
				'dataProvider' => $dataProvider,
				'columns' => [
					'id',
					'token:ntext',
					'added',
					'active:boolean',
					'level',
					'revoked:boolean',
					'revoked_on',
				],
				'rowOptions' => function ($model, $key, $index, $grid)
				{
					return [
								"class" => \Yii::$app->controller->getStatusIndicator($this->identity)
							];
				},
				"tableOptions" => [
					'class' => 'table table-bordered'
				],
			]);
			break;
		
			default:
			echo Html::tag('div', "No tokens found");
			break;
		}
	}
}
?>