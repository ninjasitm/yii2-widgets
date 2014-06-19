<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\revisions;

use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use nitm\models\Revisions as RevisionsModel;
use nitm\widgets\models\BaseWidget;
use kartik\icons\Icon;
use nitm\widgets\revisions\assets\Asset as RevisionsAsset;
use nitm\widgets\editor\Editor;

class RevisionsInput extends BaseWidget
{
	/**
	 * The name of this input widget
	 */
	public $name;
	
	/**
	 * The value for this input widget
	 */
	public $value;
		
	/*
	 * HTML options for generating the widget
	 */
	public $widgetOptions = [
		'class' => 'form-group',
		'id' => 'revision_input_div'
	];
	
	/*
	 * HTML options for generating the widget elements
	 */
	public $options = [
		'id' => 'revision_input',
		'role' => 'createRevision',
	];
	
	/**
	 * Autosave path handler. With trailing slash
	 */
	public $autoSavePath = "/revisions/create/";
	
	/**
	 * Autosave eevery 5 seconds
	 */
	public $autoSaveInterval = 10;
	
	/**
	 * Enable redactor? True by default
	 */
	public $enableRedactor = true;
	
	private $_enableRevisions = true;
	
	public function init()
	{
		switch(1)
		{
			case !($this->model instanceof RevisionsModel) && (($this->parentType == null) || ($this->parentId == null)):
			$this->_enableRevisions = false;
			break;
			
			default:
			$this->model = ($this->model instanceof RevisionsModel) ? $this->model : RevisionsModel::findModel([$this->parentId, $this->parentType]);
			break;
		}
		parent::init();
	}
	
	public function run()
	{
		$this->autoSavePath .= $this->parentType.'/'.$this->parentId;
		$this->options['id'] .= $this->parentId;
		$this->widgetOptions['id'] .= $this->parentId;
		
		$this->model->setScenario('validateNew');
		switch($this->_enableRevisions)
		{
			case true:
			$revisionOptions =  [
				'role' => $this->options['role'],
				'id' => $this->options['id'].$this->parentId,
				'data-dave-path' => $this->autoSavePath,
				'data-use-redactor' => $this->enableRedactor,
			];
			RevisionsAsset::register($this->getView());
			break;
			
			default:
			$revisionOptions = [];
			break;
		}
		switch($this->enableRedactor)
		{
			case true:
			$editorOptions['toolbarSize'] = 'medium';
			$editorOptions['size'] = 'medium';
			$editorOptions['id'] = 'message'.$this->model->getId();
			$editorOptions['model'] = $this->model;
			$editorOptions['attribute'] = 'data';
			$editorOptions['options']['value'] = $this->value;
			$editorOptions['role'] = 'message';
			$input = Editor::widget($editorOptions);
			break;
			
			default:
			$input = Html::activeTextarea($this->model, $this->name, $revisionOption);
			break;
		}
		$result = Html::tag('div', '', ['role' => 'revisionStatus']);
		echo Html::tag('div', $input.$result, $this->widgetOptions);
	}
}
?>