<?php

namespace nitm\widgets\ajax;

use yii\web\JsExpression;
use kartik\widgets\Select2;
use nitm\helpers\ArrayHelper;

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
	public $allowClear =  true;
	public $dataType = 'json';
	public $data;
	public $theme = 'bootstrap';

	public function init()
	{
		$this->pluginOptions = array_merge($this->defaultAjaxOptions(), (array)$this->pluginOptions);
		if($this->url) $this->pluginOptions['ajax']['url'] = $this->url;
		$this->initValueText = ArrayHelper::getValue($this->options, 'title', '');
		$this->options['id'] .= uniqid();
		parent::init();
	}

	public function run() {
		return parent::run();
	}

	protected function defaultAjaxOptions()
	{
		return [
			'minimumInputLength' => $this->minLength,
			'allowClear' => $this->allowClear,
			'ajax' => [
				'dataType' => $this->dataType,
				'data' => new JsExpression('function(params) { return {q:params.term}; }'),
				'results' => new JsExpression('function(data,page) {return {results:data};}'),
				'processResults' => new JsExpression('function (data, params) {
					if(data.hasOwnProperty("results"))
						return data;
					else
						return {
							results: data
						};
				}')
			]
		];
	}

	protected function getInitSelectionJs()
	{
		$customSelection = "";
		if(isset($this->pluginEvents['afterSelect']))
			$customSelection = ArrayHelper::remove($this->pluginEvents, 'afterSelect');

		$js = "function (element, callback) {
				var data = {id: element.val(), text: element.attr('title')
			};
			callback(data);
			".$customSelection."
		}";
		return new JsExpression($js);
	}
}

?>
