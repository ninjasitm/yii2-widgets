<?php

namespace nitm\widgets\editor;

use yii\imperavi\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This class makes it easier to instantiate an editor widget by providing options 
 * for differrent types of widgets. THis class is based on the Redactor editor
 * by imperavi
 *
 * This wrapper uses air buttons by default with a minimal toolbar
 */

class Editor extends \yii\imperavi\Widget
{
	public $enableFiles = true;
	public $role = 'message';
	public $size;
	public $toolbarSize;
	public $toolbarFixed;
	
	public $enableAutoSave;
	
	/**
	 * Autosave path handler. With trailing slash
	 */
	public $autoSavePath;
	
	/**
	 * Autosave every X seconds
	 */
	public $autoSaveInterval = 60;
	
	/**
	 * The name of the autosave field
	 */
	public $autoSaveName;
	
	public $options = [];
	public $htmlOptions = [];
	
	protected $modelId;
	
	public function init()
	{
		parent::init();
		$this->modelId = $this->model->isNewRecord ? uniqid() : $this->model->getId();
		$this->initFiles();
		$this->initAutoSave();
	}
	
	public function run()
	{
		$this->options = array_merge($this->defaultOptions(), $this->options);
		$this->options['toolbarFixed'] = $this->toolbarFixed;
		if($this->toolbarFixed)
			$this->options['toolbarFixedTarget'] = '#'.$this->htmlOptions['id'];
		$this->htmlOptions = array_merge($this->defaultHtmlOptions(), $this->htmlOptions);
		$buttonParam = isset($this->options['airButtons']) && ($this->options['airButtons'] == true) ? 'airButtons' : 'buttons';

		$this->plugins = [
			'fullscreen', 'fontcolor', 'fontsize'
		];
		
		switch($this->toolbarSize)
		{
			case 'full':
			$this->options[$buttonParam] = [
				'html', 'formatting',  '|',
				'bold', 'italic', 'underline', 'deleted', 'fontsize', 'fontcolor', 'backcolor', '|',
				'unorderedlist', 'orderedlist', 'outdent', 'indent',  '|',
				'image', 'video', 'file', 'table', 'link', 'alignment', 'horizontalrule'
			];
			break;
			
			case 'medium':
			$this->options[$buttonParam] = [
				'bold', 'italic', 'underline', 'deleted', 'fontsize', 'fontcolor', 'backcolor', '|',
				'unorderedlist', 'orderedlist', '|', 
				'image', 'video', 'file', 'table', 'link'
			];
			break;
			
			default:
			$this->options[$buttonParam] = [
				'bold', 'italic', 'underline', 'deleted', 'link'
			];
			break;
		}
		
		switch($this->size)
		{
			case 'full':
			$this->htmlOptions['style'] = "height: 100%";
			break;
			
			case 'large':
			$this->htmlOptions['rows'] = 12;
			break;
			
			case 'medium':
			$this->htmlOptions['rows'] = 6;
			break;
			
			default:
			$this->htmlOptions['rows'] = 3;
			break;
		}
			
		$this->htmlOptions['role'] = $this->role;
		return parent::run().\yii\helpers\Html::style("#redactor_modal_overlay, #redactor_modal, .redactor_dropdown {z-index: 10000 !important;}");
	}
	
	protected function initFiles()
	{
		if($this->enableFiles) {
			array_push($this->plugins, 'imagemanager', 'filemanager');
			$this->options['imageUpload'] = ArrayHelper::getValue($this->options, 'imageUpload', '/image/save/'.$this->model->isWhat().'/'.$this->modelId);
			$this->options['imageManagerJson'] = json_encode(array_map(function ($image) {
				if(is_array($image)) {
					return [
						'thumb' => $image['metadata']['thumb'],
						'image' => $image['url'],
						'title' => $image['title']
					];
				}
			}, (array)\nitm\filemanager\models\Image::getImagesFor($this->model, true)->asArray()->all()));
			
			$this->options['fileUpload'] = ArrayHelper::getValue($this->options, 'fileUpload', '/file/save/'.$this->model->isWHat().'/'.$this->modelId);
			$this->options['fileManagerJson'] = json_encode(array_map(function ($file) {
				if(is_array($file)) {
					return [
						'name' => $file['metadata']['thumb'],
						'image' => $file['url'],
						'name' => $file['file_name'],
						'title' => $file['title'],
						'size' => \Yii::$app->formatter->asShortSize($file['size'])
					];
				}
			}, (array)\nitm\filemanager\models\File::getFilesFor($this->model, true)->asArray()->all()));
		}
	}
	
	protected function initAutoSave()
	{		
		if($this->enableAutoSave && $this->autoSavePath)
		{
			$this->options += [
				'autosave' => $this->autoSavePath,
				'autosaveName' => (isset($this->autoSaveName) ? $this->autoSaveName : $this->model->formName().'['.(isset($this->autoSaveName) ? $this->autoSaveName : $this->attribute).']'),
				'autosaveFields' => [
					'do' => true,
					'__format' => 'json',
					'getHtml' => true,
					'ajax' => true
				],
				'autosaveCallback' => new \yii\web\JsExpression('function (name, result) {
					if(result.success)
						$nitm.notify(result.message);
				}'),
				'initCallback' => $this->redactorAutosaveFix()
			];
			if(isset($this->autoSaveInterval) && $this->autoSaveInterval >= 10)
				$this->options += [
					'autosaveInterval' => $this->autoSaveInterval
				];
			else
				$this->options += [
					'autosaveOnChange' => true
				];
		}
	}
	
	protected function defaultOptions()
	{
		return [
			'height' => 'auto',
			'buttonOptions' => [
				'class' => 'btn btn-sm chat-form-btn'
			]
		];
	}
	
	protected function defaultHtmlOptions() 
	{
		return [
			'style' => 'z-index: 99999',
			'rows' => 3,
		];
	}
	
	protected function redactorAutosaveFix() {
		return new \yii\web\JsExpression("function () {
			var autosaveLoadFix = function() {
				switch(true)
				{
					case this.autosave.inProgress:
					case this.opts.autosaveInterval >= ((Date.now() - this.autosave.last)/1000):
					return;
					break;
					
					default:
					this.autosave.inProgress = true;
					this.autosave.source = this.code.get();
	
					if (this.autosave.html === this.autosave.source) return;
	
					// data
					var data = {};
					data['name'] = this.autosave.name;
					data[this.autosave.name] = this.autosave.source;
					data = this.autosave.getHiddenFields(data);
	
					// ajax
					var jsxhr = $.ajax({
						url: this.opts.autosave,
						type: 'post',
						data: data
					});
					
					jsxhr.done(function (data) {
						this.autosave.success(data);
						this.autosave.inProgress = false;
						this.autosave.last = Date.now();
					}.bind(this));
					break;
				}
			}.bind(this)
			this.autosave.load = $.Redactor.prototype.autosave.load = autosaveLoadFix;
		}");
	}
}

?>