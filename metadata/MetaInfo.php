<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\metadata;

use yii\helpers\Html;
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
	public $attributes = [];
	
	public function init()
	{
	}
	
	public function run()
	{
		$ret_val = '';
		if(isset($this->header) && is_string($this->header) && !is_bool($this->header)) $ret_val = Html::tag('h2', $this->header);
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
	
	protected function renderListItem($model, $key, $index, $widget)
	{
		$attributes = $this->attributes;
		$ret_val = '';
		$counter = $index+1;
		foreach($attributes as $k=>$v)
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
				$options = $func($model);
				break;
				 
				default:
				$options = !isset($this->itemOptions) ? [
					'class' => 'list-group-item list-group-item-default',
					'id' => $model->isWhat().$model->getId()
				] : $this->itemOptions;
				break;
			}
			$attrGetter = function ($_model, $parts, $value, $valueIsPart) {
				$ret_val = '';
				switch(1)
				{
					case sizeof($parts) >= 2:
					foreach($parts as $prop)
					{
						if(method_exists($_model, $prop)) {
							if(is_object($obj = call_user_func([$_model, $prop])))
								$_model = $obj;
							else
								$ret_val = $obj;
						}
						else if(property_exists($_model, $prop) && is_object($_model->$prop))
							$_model = $_model->$prop;
						else
							$ret_val = $_model->$prop;	
					}
					break;
					
					default:
					switch(1)
					{
						case is_callable($value):
						$ret_val = $value($_model);
						break;
						
						case !is_null($value):
						$ret_val = $value;
						break;
						
						default:
						$ret_val = ($valueIsPart === true) ? $parts[0] : $_model->$parts[0];
						break;
					}
					break;
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
				
			switch(isset($this->index))
			{
				case true:
				$priority = ($this->index===true) ? $counter : $model->getAttribute($this->index);
				$priority = $priority==0 ? $counter : $priority;
				break;
			}
			ob_start();
			echo "<a ".Html::renderTagAttributes($options).">";
			if(isset($priority))
				echo Html::tag('div', 
					Html::tag('h2', $priority, [
						'style' => 'height: 30px; display: table-cell; vertical-align: middle;'
					]),
					['style' => 'float: left; width: 10%']
				);
			echo Html::tag('div',
				Html::tag('h4', ucfirst($title), ['class' => 'list-group-item-heading']).
				Html::tag('p', $value, ['class' => 'list-group-item-text']),
				['style' => isset($priority) ? 'margin-left: 10%; width: 90%' : 'width: 100%']
			);
			echo "</a>";
			$item = ob_get_contents();
			ob_end_clean();
			$ret_val .= $item;
		}
		return $ret_val;
	}
}
