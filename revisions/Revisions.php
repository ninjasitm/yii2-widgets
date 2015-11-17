<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\revisions;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use nitm\widgets\models\User;
use nitm\widgets\models\Revisions as RevisionsModel;
use nitm\widgets\BaseWidget;
use nitm\helpers\ArrayHelper;
use kartik\icons\Icon;

class Revisions extends BaseWidget
{
	/*
	 * HTML options for generevision the widget
	 */
	public $options = [
		'class' => 'revision',
		'role' => 'entityRevisions',
		'id' => 'revision',
	];

	/**
	 * The actions that are supported
	 */
	private $_actions = [
		'view' => [
			'tag' => 'span',
			'action' => '/revisions/view',
			'text' => 'eye',
			'options' => [
				'class' => 'col-md-4 col-lg-4',
				'role' => 'viewRevision',
				'id' => 'revision-view',
				'title' => 'View this version',
				'data-toggle' => 'modal',
				'data-target' => '#revisions-view-modal'
			]
		],
		'restore' => [
			'tag' => 'span',
			'action' => '/revisions/restore',
			'text' => 'reply',
			'options' => [
				'class' => 'col-md-4 col-lg-4',
				'role' => 'dynamicAction',
				'id' => 'revision-restore',
				'title' => 'Restore this version'
			]
		],
		'delete' => [
			'tag' => 'span',
			'action' => '/revisions/delete',
			'text' => 'trash-o',
			'options' => [
				'class' => 'col-lg-4 col-md-4',
				'role' => 'dynamicAction',
				'id' => 'revision-delete',
				'title' => 'Delete this version'
			]
		],
	];

	public function init()
	{
		switch(1)
		{
			case !($this->model instanceof RevisionsModel) && (($this->parentType == null) || ($this->parentId == null)):
			$this->model = null;
			break;

			default:
			$this->model = ($this->model instanceof RevisionsModel) ? $this->model : (new RevisionsModel(['initSearchClass' => false]))->findModel([$this->parentId, $this->parentType]);
			break;
		}
		parent::init();
		Asset::register($this->getView());
	}

	public function run()
	{
		$this->model->queryOptions['orderBy'] = ['id' => SORT_DESC];
		//$this->model->queryOptions['andWhere'] = array_merge(ArrayHelper::getValue($this->model->queryOptions, 'andWhere', []), ['disabled' => false]);
		$dataProvider = new \yii\data\ArrayDataProvider([
			"allModels" => (is_array($this->items) && !empty($this->items)) ? $this->items : $this->model->getModels(),
			'pagination' => false,
		]);

		if(!\Yii::$app->getUser()->getIdentity()->isAdmin())
			$this->model->queryOptions['andWhere']['disabled'] = false;

		$revisions = $this->render('@nitm/widgets/views/revisions/index', [
			'dataProvider' => $dataProvider
		]);
		$this->options['id'] .= $this->parentId;
		return Html::tag('div', $revisions, $this->options);
	}

	public function getActions()
	{
		$actions = is_null($this->actions) ? $this->_actions : array_intersect_key($this->_actions, $this->actions);
		$ret_val = '';
		foreach($actions as $name=>$action)
		{
			switch(isset($action['adminOnly']) && ($action['adminOnly'] == true))
			{
				case true:
				switch(\Yii::$app->userMeta->isAdmin())
				{
					case true:
					$action['options']['id'] = $action['options']['id'].$this->parentId;
					$ret_val[$name] = function ($url, $model) use($action) {
						return Html::a(Icon::show($action['text']), $action['action'].'/'.$model->getId(), $action['options']);
					};
					break;
				}
				break;

				default:
				$action['options']['id'] = $action['options']['id'].$this->parentId;
				$ret_val[$name] = function ($url, $model) use($action) {
					return Html::a(Icon::show($action['text']), $action['action'].'/'.$model->getId(), $action['options']);
				};
				break;
			}

		}
		return $ret_val;
	}
}
?>
