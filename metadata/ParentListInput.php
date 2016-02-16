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
class ParentListInput extends \nitm\widgets\ajax\Dropdown
{
	public $model;
	public $name = 'parent_id_autocomplete';
	public $data = [];

	public $options = [
		'role' => 'parentListInput',
		'multiple' => true,
		'class' => 'form-control',
	];

	public function init()
	{
		if(!($this->model instanceof \nitm\models\Data))
			throw new \yii\base\ErrorException(__CLASS__.'->'.__FUNCTION__."() needs a \\nitm\models\Data based model for the parents list!");
		$this->options['id'] = isset($this->options['id']) ? $this->options['id'] : $this->model->isWhat().'parent-list-input'.uniqid();

		$parents = [];
		foreach((array)$this->model->parents as $model)
			$parents[$model->getId()] = $model->title();

		$parentsCSV = implode(', ', array_map(function ($parent) { return $parent;}, $parents));

		$this->allowClear = false;
		$this->data = $parents;
		$this->model->parent_ids = array_keys($parents);

		$this->options = array_merge($this->options, [
			'title' => $parentsCSV,
			'data-model-id' => $this->model->getId(),
			'data-real-input' => "#".$this->model->isWhat()."-parent_ids",
			'placeholder' => "Search for parents "
		]);

		$this->pluginEvents = [
			'select2:select' => 'function (event) {
				"use strict";
				let $input = $(this),
				 	modelId = $input.data("model-id"),
					parentId = event.params.data.id;
				if(modelId && parentId)
					$.post("/'.$this->model->isWhat().'/add-parent/"+modelId+"/"+parentId, function (result) {
						if(result !== false) {
							$(\'[role="parentList"]\').append(result);
								$nitm.m("utils").notify("Successfully added parent", "success", event.target);
						} else {
							$nitm.m("utils").notify("Unable to add parent", "error", event.target);
						}
					});
			}',
			'select2:unselect' => 'function (event) {
				"use strict";
				let $input = $(this),
				 	modelId = $input.data("model-id"),
					parentId = event.params.data.id;
				if(modelId && parentId)
					$.post("/'.$this->model->isWhat().'/remove-parent/"+modelId+"/"+parentId, function (result) {
						if(result !== false) {
							$nitm.m("utils").notify("Successfully removed parent", "success", event.target);
						} else {
							$nitm.m("utils").notify("Unable to remove parent", "error", event.target);
						}
					});
			}',
		];

		parent::init();

		$this->value = array_keys($parents);
	}
}
