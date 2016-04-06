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
use kartik\builder\TabularForm;
use nitm\helpers\Icon;
use nitm\helpers\ArrayHelper;

/**
 * ShortLink widget renders the address of a short link and a modal view button
 */
class MetadataInfo extends \yii\base\Widget
{
	public $header;
	public $type;
	public $model;
	public $formOptions;
	/**
	 * Extra options for the \kartik\builder\Form widget
	 * @var array
	 */
	public $formBuilder;
	public static $controllerRoute;
	public $options = [
		'class' => 'row'
	];
	public $createButton = [];
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
	public $independentForms = false;
	public $asForm = false;
	public $withForm = false;

	protected $uniqid;

	public function init()
	{
		$this->uniqid = uniqid();
		$this->options['id'] = 'metadata'.$this->uniqid;
		$this->options['role'] = 'metadata';
		$this->items = is_array($this->items) ? $this->items : [$this->items];
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
		$attributeCount = count($this->attributes);
		$invisible = 0;
		array_walk($this->attributes, function ($attr) use(&$invisible) {
			if(ArrayHelper::getValue($attr, 'columnOptions.visible', true) === false) {
				$invisible--;
			}
		});
		$columnSize = floor(11/($attributeCount + $invisible));
		$attributes = $builderAttribtues = [];
		$type = $this->type;
		$items = array_filter($this->items);
		foreach($this->items as $idx=>$item)
		{
			$item->setScenario('update');
			array_walk($this->attributes, function ($value, $attribute) use (&$attributes, $columnSize, $type, $item, $idx, $ret_val) {
				$attribute = ArrayHelper::getValue($value, 'label', !$attribute ? $value : $attribute);
				$value = is_array($value) ? $value : ['value' => $item->$attribute];
				$value = array_merge([
					'options' => ['class' => 'form-control form-inline'],
					'label' => null,
				], $value);
				$value['label'] = ArrayHelper::getValue($value, 'label', ucfirst(ArrayHelper::getValue($attribute, 'label', $attribute)));
				$value['columnOptions'] = ArrayHelper::getValue($value, 'columnOptions', [
					'class' => "col-md-$columnSize col-lg-$columnSize"
				]);
				$value['options']['name'] = !empty($type) ? $item->formName()."[$type][$attribute]" : $item->formName()."[$attribute]";
				if(!$this->independentForms)
					$value['options']['name'] .= '[]';
				if($idx === 'lastMetadataItem' && !ArrayHelper::remove($value, 'keep', false)) {
					$value['options']['value'] = '';
				} else {
					$value['options']['value'] = ArrayHelper::remove($value, 'value', $item->$attribute);
				}
				//unset($value['value']);
				$value['type'] = ArrayHelper::getValue($value, 'type', Form::INPUT_TEXT);
				$attributes[strtolower($attribute)] = $value;
			});
			if($this->independentForms) {
				$this->setActions($item, $attributes, $idx);
				$ret_val .= $this->getItemAsForm($attributes, $item, $idx, $item);
			} else {
				$builderAttribtues[$idx] = $attributes;
			}
		}
		if($this->independentForms) {
			return Html::tag('div', $ret_val.$this->getAppender(), $this->options);
		} else {
			$ret_val = TabularForm::widget([
				'form' => $this->form,
				'attributes' => $builderAttribtues,
				'gridSettings' => [
					'rowOptions' => function ($item, $idx) {
						return [
							'class' => ($idx === 'lastMetadataItem') ? 'hidden' : 'visible bg-success',
							'id' => ($idx === 'lastMetadataItem') ? 'metadata-template'.uniqid() : 'metadata-item'.$item->getId(),
							'role' => ($idx === 'lastMetadataItem') ? 'metadataTemplate' : 'metadataItem'
						];
					}
				]
			]);
			return Html::tag('table', $ret_val.$this->getAppender(), $this->options);
		}
	}

	protected function getForm($options=[], $model=null)
	{
		$options = array_merge($this->formOptions, $options);
		$action = ArrayHelper::getValue($options, 'action');
		if($action && is_callable($action))
			$options['action'] = $action($model);
		$class = ArrayHelper::remove($options, 'class', \kartik\widgets\ActiveForm::className());
		return $class::begin(array_merge([
			'options' => [
				'role' => 'ajaxForm',
				'id' => 'metadata-form'.uniqid()
			]
		], (array)$options));
	}

	private function getAppender()
	{
		if($this->withForm) {
			$title = Icon::forAction('plus').' '.ArrayHelper::remove($this->createButton, 'text', Html::tag('span', 'New '.$this->model->properName(), ['class' => 'hide-md']));
			$ret_val = Html::beginTag('div', [
				'class' => 'text-right'
			]);
				$ret_val .= Html::a($title, \Yii::$app->urlManager->createUrl([$this->model->isWhat()]), array_merge([
					'title' => \Yii::t('yii', 'Add '.$this->header),
					'class' => 'btn btn-default pull-right',
					'data-pjax' => '0'
				], $this->createButton, [
					'role' => 'createMetadata',
				]));
			$ret_val .= Html::endTag('div');
			return $ret_val;
		}
		return '';
	}

	private function getItemAsForm($attributes, $item, $idx, $model=null)
	{
		$options = [
			'id' => ($idx === 'lastMetadataItem') ? 'metadata-template'.uniqid() : 'metadata-item'.$item->getId(),
			'role' => ($idx === 'lastMetadataItem') ? 'metadataTemplate' : 'metadataItem',
			'tag' => 'div',
			'style' => 'padding-top: 15px',
			'class' => 'col-sm-12'
		];

		Html::addCssClass($options, ($idx === 'lastMetadataItem') ? 'hidden' : 'visible bg-success');
		$ret_val = $this->independentForms ? Html::beginTag('div', $options) : '' ;
			ob_start();
			$widget = Form::begin(array_merge([
				'model' => $model ?: $this->model,
				'form' => $this->getForm([], $model ?: $this->model),
				'columns' => sizeof($attributes)+1,
				'attributes' => $attributes,
				'options' => ['tag' => false]
			], (array)$this->formBuilder));
			$widget->end();
			$widget->form->end();
			$ret_val .= ob_get_contents();
			ob_end_clean();
		$ret_val .= $this->independentForms ? Html::endTag('div') : '' ;
		return $ret_val;
	}

	private function setActions($item, &$attributes, $idx)
	{
		$idName = !empty($type) ? $item->formName()."[$type][id]" : $item->formName()."[id]";
		if(!$this->independentForms)
			$idName .= '[]';
		$attributes['id'] = [
			'type' => FORM::INPUT_RAW,
			'value' => Html::activeHiddenInput($item, 'id', [
				'name' => $idName,
				'role' => 'metadataId'
			])
		];
		$saveAction = $item->isNewRecord ? 'create' : 'update';
		$saveRole = $item->isNewRecord ? 'saveMetadata' : 'udpateMetadata';
		$deleteAction = $idx == 'lastMetadataItem' ? 'delete' : 'disable';
		$deleteRole = $idx == 'lastMetadataItem' ? 'deleteMetadata' : 'disableParent';
		$attributes['actions'] = [
			'type' => Form::INPUT_RAW,
			'label' => 'Delete',
			'columnOptions' => [
				'colspan' => 2
			],
			'value' => \yii\bootstrap\ButtonGroup::widget([
				'encodeLabels' => false,
				'buttons' => [
					[
						'label' => Html::a(Icon::forAction($saveAction)),
						'options' => [
							'href' => \Yii::$app->urlManager->createUrl([$this->model->isWhat()]),
							'title' => \Yii::t('yii', 'Save Metadata'),
							'data-pjax' => 0,
							'role' => $saveRole,
							'class' => 'btn btn-'.($item->isNewRecord ? 'success' : 'info'),
							'type' => $this->independentForms ? 'submit' : 'button'
						]
					], [
						'label' => Html::a(Icon::forAction($deleteAction)),
						'options' => [
							'href' => \Yii::$app->urlManager->createUrl([$this->model->isWhat()]),
							'title' => \Yii::t('yii', 'Delete Metadata'),
							'data-pjax' => 0,
							'role' => $deleteRole,
							'inline' => true,
							'data-parent' => '[role~="metadataItem"]',
							'data-depth' => 1,
							'class' => 'btn btn-danger'
						]
					]
				]
			])
		];
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
								'data-depth' => 1,
								'data-parent' => '[role~="metadataItem"]',
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
