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
use yii\redactor\widgets\Redactor;
use nitm\models\Revisions as RevisionsModel;
use nitm\widgets\models\BaseWidget;
use kartik\icons\Icon;
use nitm\widgets\revisions\assets\Asset as RevisionsAsset;

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
		if (!($this->model instanceof RevisionsModel) && (($this->parentType == null) && ($this->parentId == null))) {
			throw new \yii\base\InvalidConfigException("No model or parentType & parentId set");
		}
		else 
		{
			$this->model = ($this->model instanceof RevisionsModel) ? $this->model : new RevisionsModel([
				"constrain" => [$this->parentId, $this->parentType]
			]);
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
			$input = @Redactor::widget([
				'model' => $this->model,
				'name' => $this->name,
				'value' => $this->value,
				'options' => $revisionOptions,
				'clientOptions' => [
					'autoresize' => true,
					'autosave' =>  $this->autoSavePath,
					'autosaveInterval' => $this->autoSaveInterval,
				]
			]);
			break;
			
			default:
			$input = Html::activeTextarea($this->model, $this->name, $revisionOption);
			break;
		}
		$result = Html::tag('div', '', ['role' => 'revisionStatus']);
		echo Html::tag('div', $input.$result, $this->widgetOptions);
		echo Html::script("
			setTimeout(function () {
				var r = new Revisions();
				r.useRedactor = ".$this->enableRedactor.";
				r.saveUrl = '".$this->autoSavePath."';
				r.init('#".$this->widgetOptions['id']."');
			}, 3000);
		");
	}
}
?>