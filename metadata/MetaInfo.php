<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\metadata;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\icons\Icon;

/**
 * MetInfo widget renders the address of a short link and a modal view button
 */
class MetaInfo extends \yii\base\Widget
{
	public $header;
	public $widgetOptions = [];
	public $itemOptions;
	public $index;
	public $priorityOptions = [];
	public $options = [
		'class' => 'row'
	];
	public $items = [];
	/*
	 * 'attributes' => [
			'attribute', // attribute (in plain text)
			'description:html', // description attribute in HTML
			[
				'label' => 'Label',
				'value' => $value,
			],
	* ]*/
	public $displayAs = 'list';
	public $valuesOnly;
	public $attributes = [];

	public function run()
	{
		$ret_val = '';
		if(isset($this->header) && is_string($this->header) && !is_bool($this->header))
			$ret_val = Html::tag('h2', $this->header);
		switch($this->displayAs)
		{
			case 'grid':
			$this->items = is_array($this->items) ? $this->items : [$this->items];
			$this->widgetOptions = array_merge([
				'summary' => false,
				'layout' => '{items}',
				'showHeader' => $this->header,
				'dataProvider' => new \yii\data\ArrayDataProvider(['allModels' => $this->items]),
				'columns' => $this->attributes,
			], $this->widgetOptions);
			$ret_val .= \kartik\grid\GridView::widget($this->widgetOptions);
			break;

			case 'list':
			$this->widgetOptions = array_merge([
				'itemOptions' => [
					'tag' => false
				],
				'summary' => false,
				'dataProvider' => new \yii\data\ArrayDataProvider([
					'allModels' => $this->items
				]),
				'itemView' => function ($model, $key, $index, $widget) {
					return $this->renderListItem($model, $key, $index, $widget);
				}
			], $this->widgetOptions);
			$ret_val .= \yii\widgets\ListView::widget($this->widgetOptions);
			break;

			case 'csv':
			$ret_val = [];
			foreach($this->items as $index=>$item)
				$ret_val[] = $this->renderCsvItem($item, $index);
			$ret_val = Html::tag('div', implode(', ', $ret_val));
			break;

			case 'tags':
			foreach($this->items as $index=>$item)
				$ret_val .= $this->renderTagItem($item, $index);
			$ret_val = Html::tag('div', $ret_val);
			break;

			default:
			$this->widgetOptions['class'] = isset($this->widgetOptions['class']) ? $this->widgetOptions['class'] : 'table';
			$this->widgetOptions = array_merge([
				'model' => $this->items,
				'attributes' => $this->attributes,
				'options' => [
					'class' => 'table'
				]
			], $this->widgetOptions);
			$ret_val .= \yii\widgets\DetailView::widget($this->widgetOptions);
			break;
		}
		return $ret_val;
	}

	protected function renderCsvItem($model, $index)
	{
		$ret_val = '';
		$counter = $index+1;
		foreach($this->attributes as $k=>$v)
		{
			list($title, $value, $priority, $options) = $this->getParts($model, $k, $v, $counter);
			ob_start();
			$tag = ArrayHelper::remove($options, 'tag', 'span');
			if(isset($priority) && !is_null($priority))
				echo $priority.' - ';
			echo ucfirst($title).':&nbsp;'.$value;
			$item = ob_get_contents();
			ob_end_clean();
			$ret_val .= $item;
		}
		return $ret_val;
	}

	protected function renderTagItem($model, $index)
	{
		$ret_val = '';
		$counter = $index+1;
		foreach($this->attributes as $k=>$v)
		{
			list($title, $value, $priority, $options) = $this->getParts($model, $k, $v, $counter);
			ob_start();
			$tag = ArrayHelper::remove($options, 'tag', 'span');
			echo "<$tag ".Html::renderTagAttributes($options).">";
			if(isset($priority) && !is_null($priority))
				echo Html::tag('strong', $priority).' - &nbsp;';
			if(!$this->valuesOnly)
				echo Html::tag('strong', ucfirst($title)).':&nbsp;'.Html::tag('em', $value);
			echo "</$tag>";
			$item = ob_get_contents();
			ob_end_clean();
			$ret_val .= $item;
		}
		return $ret_val;
	}

	protected function renderListItem($model, $key, $index, $widget)
	{
		$ret_val = '';
		$counter = $index+1;
		foreach($this->attributes as $k=>$v)
		{
			list($title, $value, $priority, $options) = $this->getParts($model, $k, $v, $counter);
			ob_start();
			$tag = ArrayHelper::remove($options, 'tag', 'a');
			echo "<$tag ".Html::renderTagAttributes($options).">";
			if(isset($priority) && !is_null($priority))
				echo Html::tag('div',
					Html::tag(ArrayHelper::getValue($this->priorityOptions, 'tag', 'h2'), $priority, [
						'style' => 'line-height: '.($this->valuesOnly ? '.5' : '1.25').'; display: table-cell; vertical-align: middle;'
					]),
					['style' => 'float: left; min-width: 40px']
				);
			echo Html::tag('div',
				(!$this->valuesOnly ? Html::tag('h4', ucfirst($title), ['class' => 'list-group-item-heading']) : '').
				Html::tag('p', $value, ['class' => 'list-group-item-text']),
				['style' => isset($priority) ? 'margin-left: 40px; right: 15px' : 'right: 15px']
			);
			echo "</$tag>";
			$item = ob_get_contents();
			ob_end_clean();
			$ret_val .= $item;
		}
		return $ret_val;
	}

	private function getParts($model, $k, $v, $counter)
	{
		$value = is_string($k) ? $v : null;
		$attr = is_string($k) ? $k : $v;

		/**
		 * Doing it this way to avoid including a href attribute in anchor
		 */
		switch(is_callable($this->itemOptions))
		{
			case true:
			$func = $this->itemOptions;
			$options = $this->getItemOptions($model, $func($model));
			break;

			default:
			$options = $this->getItemOptions($model);
			break;
		}
		$attrGetter = function ($model, $parts, $value, $valueIsPart) {
			$ret_val = '';
			switch(1)
			{
				case sizeof($parts) >= 2:
				if(is_array($model))
					$ret_val = ArrayHelper::getValue($model, implode('.', $parts), '(not found)');
				else if (is_object($model))
					foreach($parts as $prop)
					{
						if(is_object($model) && property_exists($model, $prop)) {
							$model = ArrayHelper::getValue($model, $prop);
						} else if(is_object($model) && method_exists($model, $prop)) {
							$obj = call_user_func([$model, $prop]);
							if(is_object($obj) || is_array($object))
								$model = $obj;
							else {
								$ret_val = $obj;
								break;
							}
						}
						else
							$ret_val = ArrayHelper::getValue($model, $prop, $model);
					}
				break;

				default:
				switch(1)
				{
					case is_callable($value):
					$ret_val = $value($model);
					break;

					case !is_null($value):
					$ret_val = $value;
					break;

					default:
					$ret_val = ($valueIsPart === true) ? $parts[0] : $model->$parts[0];
					break;
				}
				break;
			}
			if(is_object($ret_val)){
			print_r($ret_val);
			exit;
			}
			return $ret_val;
		};
		$attr = is_array($attr) ? $attr : explode(':', $attr);
		$titleAttr = array_shift($attr);
		$valueAttr = count($attr) ? array_pop($attr) : $titleAttr;
		$title = $attrGetter($model, explode('.', $titleAttr), $value, (strpos(':', $titleAttr) === false));

		if(is_callable($valueAttr))
			$value = $valueAttr($model);
		else
			$value = strlen($valueAttr) ? $attrGetter($model, explode('.', $valueAttr), $value, false) : null;

		if($this->index){
			$priority = ($this->index===true) ? $counter : $model->getAttribute($this->index);
			$priority = $priority==0 ? $counter : $priority;
		} else
			$priority = null;

		return [$title, $value, $priority, $options];
	}

	private function getItemOptions($model, $options=[])
	{
		switch($this->displayAs)
		{
			case 'list':
			$defaultOptions = [
				'class' => 'list-group-item list-group-item-default',
				'id' => $model->isWhat().$model->getId()
			];
			break;

			case 'tags':
			$defaultOptions =  [
				'tag' => 'a',
				'style' => 'border: solid thin #ccc; padding: 5px; margin: 5px 5px 0 0; text-decoration: none; border-radius: 6px; display: inline-block',
				'id' => $model->isWhat().$model->getId()
			];
			break;

			default:
			$defaultOptions = [];
			break;
		}
		return array_merge($defaultOptions, $options);
	}
}
