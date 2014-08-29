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
use nitm\widgets\editor\Editor;

class RevisionsInput extends BaseWidget
{
	public $revisionsModel;
	
	/**
	 * The name of this input widget
	 */
	public $name;
	
	/**
	 * The value for this input widget
	 */
	public $value;
	
	public $editorOptions = [
		'role' => 'revisionsInput',
		'toolbarSize' => 'medium',
		'size' => 'medium'
	];
		
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
			case !($this->revisionsModel instanceof RevisionsModel) && (($this->parentType == null) || ($this->parentId == null)):
			$this->_enableRevisions = false;
			break;
			
			default:
			$this->revisionsModel = ($this->revisionsModel instanceof RevisionsModel) ? $this->model : RevisionsModel::findModel([$this->parentId, $this->parentType]);
			break;
		}
		parent::init();
	}
	
	public function run()
	{
		$this->autoSavePath .= $this->parentType.'/'.$this->parentId;
		$this->options['id'] .= $this->parentId;
		$this->widgetOptions['id'] .= $this->parentId;
		
		switch($this->_enableRevisions)
		{
			case true:
			$this->revisionsModel->setScenario('validateNew');
			$revisionOptions =  [
				'role' => $this->options['role'],
				'id' => $this->options['id'].$this->parentId,
				'data-dave-path' => $this->autoSavePath,
				'data-use-redactor' => $this->enableRedactor,
			];
			Asset::register($this->getView());
			break;
			
			default:
			$revisionOptions = [];
			break;
		}
		switch($this->enableRedactor)
		{
			case true:
			$this->editorOptions['id'] = 'message'.uniqid();
			$this->editorOptions['model'] = $this->model;
			$this->editorOptions['attribute'] = $this->name;
			$this->editorOptions['options']['value'] = $this->value;
			$input = Editor::widget($this->editorOptions);
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