<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\metadata;

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\builder\Form;
use nitm\helpers\Icon;

/**
 * ShortLink widget renders the address of a short link and a modal view button
 */
class MetaForm extends \yii\base\Widget
{
	public $header;
	public $type;
	public $model;
	public $form;
	public static $controllerRoute;
	public $options = [
		'class' => 'table table-condensed'
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
	public $attributes = [];
	public $formAttributes = [];
	public $asForm = false;
	public $withForm = false;
	
	protected $uniqid;
	
	public function init()
	{
		$this->uniqid = uniqid();
		$this->options['id'] = 'metadata'.$this->uniqid;
		assets\MetadataAsset::register($this->getView());
	}
	
	public function run () 
	{
		$title = Html::tag('h2', $this->header);
		switch($this->asForm)
		{
			case true:
			$metadata = $this->getAsForm();
			break;
			
			default:
			$metadata = $this->getAsGrid();
			break;
		}
		return $title.$metadata;
	}
	
	protected function getAsForm()
	{
		$ret_val = '';
		$this->items['lastMetadataItem'] = $this->model;
		$columnSize = floor(11/sizeof($this->attributes));
		$attributes = [];
		$type = $this->type;
		foreach($this->items as $idx=>$item)
		{
			$item->setScenario('update');
			array_walk($this->attributes, function ($value, $attribute) use (&$attributes, $columnSize, $type, $item, $idx){
				$attribute = is_array($value) ? $value['label'] : $value;
				$value = is_array($value) ? $value : ['value' => $item->$attribute];
				$value = array_merge([
					'options' => ['class' => 'form-control form-inline'], 
					'label' => null,
				], $value);
				$value['label'] = !empty($value['label']) ? $value['label'] : ucFirst($attribute);
				$value['columnOptions'] = isset($value['columnOptions']) ? $value['columnOptions'] : [
					'class' => "col-md-$columnSize col-lg-$columnSize"
				];
				$value['options']['name'] = !empty($type) ? $item->formName()."[$type][$attribute][]" : $item->formName()."[$attribute][]";
				if($idx === 'lastMetadataItem') {
					$value['options']['value'] = '';
				} else {
					$value['options']['value'] = !isset($value['value']) ? $item->$attribute : @$value['value'];
				}
				unset($value['value']);
				$value['type'] = isset($value['type']) ? $value['type'] : Form::INPUT_TEXT;
				$attributes[strtolower($attribute)] = $value;
			});
			if((string)$idx == 'lastMetadataItem')
			{
				$deleteAction = 'delete';
				$role = 'deleteMetadata';
				$dataParent = 'tr';
			} else {
				$deleteAction = 'ban';
				$role = 'disableParent';
				$dataParent = '#data-item'.$item->getId();
			}
			$attributes['delete'] = [
				'type' => Form::INPUT_RAW,
				'value' => Html::a(Icon::forAction($deleteAction), \Yii::$app->urlManager->createUrl([$this->model->isWhat()]), [
					'title' => \Yii::t('yii', 'Delete Metadata'),
					'data-pjax' => '0',
					'role' => $role,
					'inline' => true,
					'data-parent' => $dataParent,
					'data-depth' => 0
				]).
				Html::activeHiddenInput($item, 'id', [
					'name' => !empty($type) ? $item->formName()."[$type][id][]" : $item->formName()."[id][]",
					'role' => 'metadataId'
				]),
				'columnOptions' => [
					'class' => 'col-md-1 col-lg-1'
				]
			];
			$ret_val .= Html::tag('tr',  
				Html::tag('td', 
					Form::widget([
						'model' => $this->model,
						'form' => $this->form,
						'columns' => sizeof($attributes)+1,
						'attributes' => $attributes
					]),
					[
						'colspan' => sizeof($attributes)+1
					]
				),
				[
					'class' => (($idx === 'lastMetadataItem') ? 'hidden' : 'visible bg-success'),
					'id' => (($idx === 'lastMetadataItem') ? 'metadata-template'.$this->uniqid : 'data-item'.$item->getId()),
					'role' => (($idx === 'lastMetadataItem') ? 'metadataTemplate' : 'metadataItem'.$item->getId()),
					
				]
			);
		}
		$appender = '';
		if($this->withForm)
			$appender = 
			Html::tag('tr',
				Html::tag('td',
					Html::a(Icon::forAction('plus').' '.$this->header, \Yii::$app->urlManager->createUrl([$this->model->isWhat()]), [
						'title' => \Yii::t('yii', 'Add '.$this->header),
						'class' => 'btn btn-success btn-sm pull-right',
						'role' => 'createMetadata',
						'data-pjax' => '0'
					]),
					['colspan' => sizeof($this->attributes)+1]
				)
			);
			
		return Html::tag('table', $ret_val.$appender, $this->options);
	}
	
	protected function getAsGrid()
	{
		return GridView::widget([
			'dataProvider' => new \yii\data\ArrayDataProvider(['allModels' => $this->items]),
			'options' => [
				'role' => 'metadata',
				'id' => 'metadata'.$this->uniqid,
				'role' => 'statusIndicator'.$this->uniqid
			],
			'showHeader' => false,
			'showFooter' => false,
			'summary' => false,
			'layout' => "{items}",
			'columns' => array_merge($this->attributes,
			[
				[
					'class' => 'yii\grid\ActionColumn',
					'buttons' => [
						'form/update-metadata' => function ($url, $model) {
							return \nitm\widgets\modal\Modal::widget([
								'toggleButton' => [
									'tag' => 'a',
									'label' => Icon::forAction('pencil'), 
									'href' => \Yii::$app->urlManager->createUrl([$url, '__format' => 'modal']),
									'title' => \Yii::t('yii', 'Edit '),
									'role' => 'dynamicAction updateAction disabledOnClose',
								]
							]);
						},
						'delete-metadata' => function ($url, $model) {
							return Html::a(Icon::forAction('delete'), \Yii::$app->urlManager->createUrl([$url]), [
								'title' => \Yii::t('yii', 'Delete Metadata'),
								'role' => 'metaAction deleteAction',
								'data-depth' => 0,
								'data-parent' => 'tr',
								'data-pjax' => '0',
								'data-force' => 1
							]);
						},
					],
					'template' => "{form/update-metadata} {delete-metadata}",
					'urlCreator' => function($action, $model, $key, $index) {
						return (!MetadataInfo::$controllerRoute ? $model->isWhat() : MetadataInfo::$controllerRoute).'/'.$action.'/'.$model->getId();
					},
					'options' => [
						'rowspan' => 2,
						'class' => 'col-md-1 col-lg-1'
					],
				],
			]),
		]);
	}
}
