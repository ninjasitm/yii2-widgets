<?php

namespace nitm\widgets\editor;

use yii\imperavi\Widget;
use yii\helpers\ArrayHelper;

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
	
	public $options = [];
	public $htmlOptions = [];
	
	public $_options =  [
		'height' => 'auto',
		'buttonOptions' => [
			'class' => 'btn btn-sm chat-form-btn'
		]
	];
	
	public $_htmlOptions = [
		'style' => 'z-index: 99999',
		'rows' => 3,
	];
	
	public function run()
	{
		$modelId = $this->model->isNewRecord ? uniqid() : $this->model->getId();
		$this->options = array_merge($this->_options, $this->options);
		$this->options['toolbarFixed'] = true;
		$this->options['toolbarFixedTarget'] = '#'.$this->htmlOptions['id'];
		$this->htmlOptions = array_merge($this->_htmlOptions, $this->htmlOptions);
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
		
		if($this->enableFiles) {
			array_push($this->plugins, 'imagemanager', 'filemanager');
			$this->options['imageUpload'] = ArrayHelper::getValue($this->options, 'imageUpload', '/image/save/'.$this->model->isWHat().'/'.$modelId);
			$this->options['imageManagerJson'] = json_encode(array_map(function ($image) {
				if(is_array($image)) {
					return [
						'thumb' => $image['metadata']['thumb'],
						'image' => $image['url'],
						'title' => $image['title']
					];
				}
			}, (array)\nitm\filemanager\models\Image::getImagesFor($this->model, true)->asArray()->all()));
			
			$this->options['fileUpload'] = ArrayHelper::getValue($this->options, 'fileUpload', '/file/save/'.$this->model->isWHat().'/'.$modelId);
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
			
		$this->htmlOptions['role'] = $this->role;
		return parent::run().\yii\helpers\Html::style("#redactor_modal_overlay, #redactor_modal, .redactor_dropdown {z-index: 10000 !important;}");
	}
}

?>