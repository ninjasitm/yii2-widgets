<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\revisions\widget;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use nitm\module\models\User;
use nitm\module\models\Revisions as RevisionsModel;
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
		if (($this->parentId === null) || ($this->parentType == null)) {
			throw new InvalidConfigException('The parentId and parentType properties must be set.');
		}
	}
	
	public function run()
	{
		$this->model = RevisionsModel::findModel([$this->parentId, $this->parentType]);
		$this->options['id'] .= $this->parentId;
		$info = 'Revisions: '.Html::tag('span', $this->model->count, $this->options);
		echo "HERE:";
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
		echo Html::tag('div', $info, $this->countOptions);
	}
}
?>