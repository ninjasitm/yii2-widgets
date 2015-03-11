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
	
	public $options = [
		'role' => 'parentListInput',
		'multiple' => true,
		'class' => 'form-control',
	];
	
	public function init()
	{
		if(!isset($this->model))
			throw new \yii\base\ErrorException(__CLASS__.'->'.__FUNCTION__."() needs a model for the parents list!");
		$this->options['id'] = isset($this->options['id']) ? $this->options['id'] : $this->model->isWhat().'ParentListInput'.uniqid();
		
		$this->options = array_merge($this->options, [
			'data-model-id' => $this->model->getId(),
			'data-real-input' => "#".$this->model->isWhat()."-parent_ids",
			'placeholder' => "Search for ".$this->model->isWhat()." parents"
		]);
		
		$this->pluginEvents = [
			'change' => 'function () {
				var $input = $(this);
				var modelId = $input.data("model-id");
<<<<<<< HEAD
				if(modelId && $input.val())
=======
<<<<<<< HEAD
				if(modelId && $input.val())
=======
				if(modelId)
>>>>>>> ec198e03b45eed4a18016c397e9fdac0bfc096d9
>>>>>>> db70d51ed5842473b032729bafdf99af79167a61
					$.post("/'.$this->model->isWhat().'/add-parent/"+modelId+"/"+$input.val(), function (result) {
						if(result !== false)
							$(\'[role="parentList"]\').append(result)
					});
			}',
		];
		
		parent::init();
	}
		
	
	public function run()
	{
		return parent::run();
	}
}
