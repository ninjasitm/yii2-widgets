<?php

namespace nitm\widgets\ajax;

use yii\web\JsExpression;
use kartik\widgets\Select2;

/**
 * This class makes it easier to instantiate an editor widget by providing options 
 * for differrent types of widgets. THis class is based on the Redactor editor
 * by imperavi
 *
 * This wrapper uses air buttons by default with a minimal toolbar
 */

class Dropdown extends Select2
{
	public $url;
	public $minLength = 3;
	
	private $ajaxOptions = [
		'ajax' => [
			'dataType' => 'json',
		]
	];
	
	public function init()
	{
		if($this->url) $this->ajaxOptions['ajax']['url'] = $this->url;
		$this->ajaxOptions['minimumInputLength'] = $this->minLength;
		$this->ajaxOptions['initSelection'] = new JsExpression("function (element, callback) {var data = {id: element.val(), text: element.attr('title')};callback(data);}");
		$this->ajaxOptions['ajax']['data'] = new JsExpression('function(term,page) { return {term:term}; }');
		$this->ajaxOptions['ajax']['results'] = new JsExpression('function(data,page) { return {results:data}; }');
		$this->pluginOptions = $this->ajaxOptions;
		parent::init();
	}
	
	public function run() {
		echo parent::run();
	}
}

?>