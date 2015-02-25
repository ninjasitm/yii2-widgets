<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\metadata;

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\base\Widget;
use nitm\helpers\Icon;

/**
 * Alert widget renders a parent from session flash. All flash parents are displayed
 * in the sequence they were assigned using setFlash. You can set parent as following:
 *
 * - \Yii::$app->getSession()->setFlash('error', 'This is the parent');
 * - \Yii::$app->getSession()->setFlash('success', 'This is the parent');
 * - \Yii::$app->getSession()->setFlash('info', 'This is the parent');
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcerative.ru>
 */
class ParentList extends Widget
{
	public $model;
	
	public $options = [
		'tag' => 'ul',
		'role' => 'parentList',
	];
	
	public $containerOptions = [];
	
	public $itemOptions = [
		'tag' => 'li',
		'class' => 'list-group-item'
	];
	
	/**
	 * Array containing legend mappint for classes
	 */
	public $parents = [];
	
	/*
	 * HTML options for generating the widget
	 */
	public $labelOptions = [
		'class' => 'tex-center',
	];
	/*
	 * HTML options for generating the label container
	 */
	public $labelContainerOptions = [];
	
	public $dataProvider;
	
	public function init()
	{
		parent::init();
		$this->options['id'] = isset($this->options['id']) ? $this->options['id'] : 'parentList'.uniqid();
		if(!isset($this->model))
			throw new \yii\base\ErrorException(__CLASS__.'->'.__FUNCTION__."() needs a model for the parents list!");
		$this->dataProvider = new \yii\data\ArrayDataProvider([
			'allModels' => (array)$this->parents
		]);
	}
		
	
	public function run()
	{
		$header = Html::tag('div', 
			Html::tag('h4', 'Parents', $this->labelOptions),
			$this->labelContainerOptions);
		$list = ListView::widget([
			'summary' => false,
<<<<<<< HEAD
			'emptyText' => Html::tag('ul', '', $this->options),
=======
>>>>>>> ec198e03b45eed4a18016c397e9fdac0bfc096d9
			'options' => $this->options,
			'itemOptions' => $this->itemOptions,
			'dataProvider' => $this->dataProvider,
			'itemView' => function ($model, $key, $index, $widget) {
				return $model->name.
					Html::tag('span',
						Html::a("Remove ".Icon::show('remove'), 
							'/'.$this->model->isWhat()."/remove-parent/".$this->model->getId().'/'.$model['id'], [
							'role' => 'parentListItem',
							'style' => 'color:white'
						]), [
						'class' => 'badge'
					]);
			}
		]);
		$script = Html::tag('script', new \yii\web\jsExpression('$(document).ready(function () {
			$("#'.$this->options['id'].'").find(\'[role="parentListItem"]\').each(function () {
				$(this).on("click", function (event) {
					event.preventDefault();
					var $element = $(this);
					$.post(this.href, function (result) {
						if(result) $element.parents("li").remove();
					});
				});
			});
		})'), ['type' => 'text/javascript']);
		return $header.Html::tag('div', $list, $this->containerOptions).$script;
	}
}
