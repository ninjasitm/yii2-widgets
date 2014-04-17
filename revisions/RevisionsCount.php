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
	/*
	 * HTML options for generating the widget
	 */
	public $options = [
		'class' => 'badge',
		'role' => 'revisionsCount',
		'id' => 'revisions-count'
	];
	
	public $countOptions = [
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
		$info = 'Revisions: '.Html::tag('span', (int)$this->model->count, $this->options);
		switch($this->model->count >= 1)
		{
			case true:
			$info .= " ".Html::a(
				Icon::show('eye'), 
				'/revisions/index/'.$this->parentType."/".$this->parentId,
				[
					'data-toggle' => 'modal',
					'data-target' => '#revisions'.$this->parentId,
					'title' => 'View revisions'
				]
			)."<br>";
			$info .= " Last Revision by ".Html::tag('span', $this->model->last->authorUser->getFullName(true, $this->model->last->authorUser), $this->options);
			$info .= " on ".Html::tag('span', $this->model->last->created_at, $this->options);
			
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
			break;
		}
		
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
				"style" => "z-index: 10001"
			]
		);
		echo Html::tag('div', $info, $this->countOptions).$modalView;
	}
}
?>