<?php

namespace nitm\widgets;

use nitm\helpers\Session;

class Module extends \yii\base\Module
{	
	/**
	 * @string the module id
	 */
	public $id = 'nitm-widgets';
	
	public $controllerNamespace = 'nitm\widgets\controllers';
	
	public $useFullnames;
	
	/**
	 * The classnames for the activity elements
	 */
	public $checkActivityFor = [
		'\nitm\widgets\models\Issues',
		'\nitm\widgets\models\Replies',
	];
	
	/*
	 * @var array options for nitm\models\Votes
	 */
	public $voteOptions = [
		'individualCounts' => true,
		'allowMultiple' => false,
		'usePercentages' => true
	];

	public function init()
	{
		parent::init();
		
		/**
		 * Aliases for nitm\widgets module
		 */
		\Yii::setAlias('nitm/widgets', dirname(__DIR__)."/yii2-widgets");
	}
}
