<?php

namespace nitm\widgets;

use nitm\helpers\Session;

class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
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

	public function getUrls($id = 'nitm-widgets')
	{
		return [
            $id => $id,
            $id . '/<controller:[\w\-]+>' => $id . '/<controller>/index',
            $id . '/<controller:[\w\-]+>/<action:[\w\-]+>' => $id . '/<controller>/<action>',
            $id . '/<controller:[\w\-]+>/<action:[\w\-]+>/<id>' => $id . '/<controller>/<action>',
            $id . '/<controller:[\w\-]+>/<action:[\w\-]+>/<type>/<id>' => $id . '/<controller>/<action>',
			'<controller:(alerts|reply|issue|revisions|vote|reply|request)>/<action>/<type>/<id:\d+>' => $id . '/<controller>/<action>',
		   	//Three type parameter routes
		   	'<controller:(alerts|reply|issue)>/<action>/<type>/<id:\d+>/<key>' => $id . '/<controller>/<action>',
		   	//Two type parameter routes
		   	//Single type parameter routes
		   	'<controller:(alerts|reply|issue|revisions|request)>/<action>/<id:\d+>' => $id . '/<controller>/<action>',
		   	'<controller:(request|alerts)>/<action>/<type>' => $id . '/<controller>/<action>',
		   	//No type parameter routes
		   	'<controller:(alerts|reply|issue|revisions|request)>/<action>' => $id . '/<controller>/<action>',

		   	//No parameter routes
		   	'<controller:(alerts|reply|issue|revisions|request)>' => $id . '/<controller>',
        ];
	}

	public function bootstrap($app)
	{
		/**
		 * Setup urls
		 */
        $app->getUrlManager()->addRules(self::getUrls(), false);
	}
}
