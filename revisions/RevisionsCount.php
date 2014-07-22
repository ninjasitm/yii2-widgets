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
use nitm\models\User;
use nitm\models\Revisions as RevisionsModel;
use nitm\widgets\models\BaseWidget;
use kartik\icons\Icon;

class RevisionsCount extends BaseWidget
{
	public $fullDetails = true;	
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'badge',
		'role' => 'revisionsCount',
		'id' => 'revisions-count'
	];
	
	public $widgetOptions = [
		'class' => 'list-group'
	];
	
	public $itemOptions = [
		'class' => 'list-group-item'
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
		$info = '';
		switch($this->model->count >= 1)
		{
			case true:
			$this->options['class'] .= " bg-success";
			$info = Html::a(
				Html::tag('span', (int)$this->model->count.' Revisions '.Icon::show('eye'), $this->options), 
				'/revisions/index/'.$this->parentType."/".$this->parentId,
				[
					'data-toggle' => 'modal',
					'data-target' => '#revisions-view-modal',
					'title' => 'View revisions',
					'class' => 'btn btn-xs btn-primary'
				]
			);
			if($this->fullDetails)
			{
				$info .= Html::tag('span', " on ".$this->model->last->created_at, $this->options);
				$info .= Html::tag('span', "Last by ".$this->model->last->author()->fullName(true), $this->options);
			}
			$info = Html::tag('li', $info, $this->itemOptions);
			
			$info .= Html::tag(
				'div', 
				Html::tag(
					'div', 
					Html::tag(
						'div', 
						'', 
						['class' => 'modal-content']
					), 
					['class' => 'modal-dialog']
				), 
				['class' => 'modal fade', 'id' => 'revisions'.$this->parentId]
			);
			$modalView = Html::tag('div',
				Html::tag('div', 
					'',
					[
						'class' => "modal-content"
					]
				),
				[
					"role" => "dialog",
					"class" => "col-md-6 col-lg-6 col-sm-12 col-xs-12 col-md-offset-3 col-lg-offset-3 modal fade",
					"id" => "revisionsViewModal",
					"style" => "z-index: 1001"
				]
			);
			$info .= $modalView;
			break;
			
			default:
			$info = $this->showEmptyCount ? Html::tag('li', Html::tag('span', "0 Revisions", $this->options), $this->itemOptions) : '';
			break;
		}
		echo $info = Html::tag('ul', $info, $this->widgetOptions);
	}
}
?>