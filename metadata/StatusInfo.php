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
 * ShortLink widget renders the address of a short link and a modal view button
 */
class StatusInfo extends \yii\base\Widget
{
	public $options = [
		'class' => 'list-group'
	];
	/**
	 * Array containing parameters to get status info for
	 * [
	 *		[
	 *			'attribute' => $this->model->$attribute,
	 *			'blamable' => user id,
	 *			'date' => date,
	 *			'value' => value,
	 *			'label' => [
	 *				'true' => label if value true
	 *				'false' => label if value false
	 *			]
	 *		],
	 *		...
	 * ];
	 */
	public $items = [];
	
	protected $useBadge = true;
	
	public function init()
	{
	}
	
	public function run() 
	{
		$statusInfo = '';
		foreach($this->items as $attribute)
		{
			$attribute['type'] = isset($attribute['type']) ? $attribute['type'] : null;
			$this->useBadge = is_null($attribute['type']) ? true : false;
			$attribute['options'] = isset($attribute['options']) ? $attribute['options'] : [];
			switch(empty($attribute['value']))
			{
				case false:
				$label = !$attribute['value'] ? $attribute['label']['false'] : $attribute['label']['true'];
				$user = (isset($attribute['blamable']) && is_object($attribute['blamable'])) ? $label.' by '.$attribute['blamable']->url() : $label;
				$date = isset($attribute['date']) ? $this->value($label.' on '.$attribute['date'], $attribute['type'], $attribute['options']) : '';
				switch(1)
				{
					case empty($date):
					$item = $this->label($label).$this->value($attribute['value'], $attribute['type'], $attribute['options']);
					break;
					
					default:
					$item = $this->label($user).$date;
					break;
				}
				$statusInfo .= Html::tag('li', 
					($this->useBadge ? $item : Html::tag('div', $item, ['class' => 'row'])),
					[
						'class' => 'list-group-item'
					]
				);
				break;
			}
		}
		return Html::tag('ul', $statusInfo, $this->options);
	}
	
	private function value($value, $type=null, $options=[])
	{
		$uniqid = uniqid();
		$options = array_merge([
			'role' => 'statusInfoValue',
			'id' => 'status-info-span'.$uniqid,
			'class' => 'col-md-7 col-lg-7'
		], $options);
		switch($type)
		{
			case 'link':
			$value = Html::a($value, $value);
			break;
			
			case 'input':
			$value = Html::textInput('status-info-input'.$uniqid, $value, [
				'id' => 'status-info-input'.$uniqid,
				'class' => 'form-control'
			]);
			break;
			
			case 'password':
			$value = Html::passwordInput('status-info-input'.$uniqid, $value, [
				'id' => 'status-info-input'.$uniqid,
				'class' => 'form-control'
			]);
			break;
			
			case 'raw':
			case 'text':
			break;
			
			default;
			$options['class'] = ' badge';
			break;
		}
		return Html::tag('span', $value, $options);
	}
	
	private function label($text)
	{
		$uniqid = uniqid();
		$options = [
			'role' => 'statusInfoLabel',
			'id' => 'status-info-label'.$uniqid,
		];
		if(!$this->useBadge) $options['class'] = 'col-md-5 col-lg-5';
		return Html::tag('span', $text, $options);
	}
}
