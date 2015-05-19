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
use nitm\widgets\models\User;
use nitm\widgets\models\Revisions as RevisionsModel;
use nitm\widgets\helpers\BaseWidget;
use kartik\icons\Icon;

class RevisionsCount extends BaseWidget
{
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'btn btn-sm',
		'role' => 'revisionsCount',
		'id' => 'revisions-count',
		'tag' => 'a'
	];
	
	public $widgetOptions = [
		'class' => 'btn-group'
	];
	
	public function init()
	{
		switch(1)
		{
			case !($this->model instanceof RevisionsModel) && (($this->parentType == null) || ($this->parentId == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof RevisionsModel) ? $this->model : RevisionsModel::findModel([$this->parentId, $this->parentType]);
			break;
		}	
		parent::init();
	}
	
	public function run()
	{
		$this->options['id'] .= $this->parentId;
		$this->options['class'] .= ' '.($this->model->count() >= 1 ? 'btn-primary' : 'btn-transparent');
		$this->options['label'] = (int)$this->model->count().' Revisions '.Icon::show('eye');
		$this->options['href'] = \Yii::$app->urlManager->createUrl(['/revisions/index/'.$this->parentType."/".$this->parentId, '__format' => 'modal']);
		$this->options['title'] = \Yii::t('yii', 'View Revisions');
		$info = $this->getInfoLink();
		return $info = Html::tag('div', $info, $this->widgetOptions).$this->getNewIndicator();
	}
}
?>