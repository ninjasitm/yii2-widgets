<?php

namespace nitm\widgets;

use nitm\helpers\Session;
use yii\helpers\Inflector;

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
		$parameters = [];
		$routeHelper = new \nitm\helpers\Routes([
			'moduleId' => $id,
			'map' => [
				'type-id-key' => '<controller:%controllers%>/<action>/<type>/<id:\d+>/<key>',
				'type-id-no-action' => ['<controller:%controllers%>/<type>/<id:\d+>' => '<controller>/index'],
				'type-id' => '<controller:%controllers%>/<action>/<type>/<id:\d+>',
				'id' => '<controller:%controllers%>/<action>/<id:\d+>',
				'type' => '<controller:%controllers%>/<action>/<type>',
				'action-only' => '<controller:%controllers%>/<action>',
				'none' => '<controller:%controllers%>'
			],
			'controllers' => [
				'alert', 'reply', 'issue', 'revision', 'request', 'vote'
			]
		]);
		$routeHelper->pluralize();
		$parameters['type-id-key'] = $routeHelper->getControllerMap(['alert', 'reply', 'issue']);
		$parameters['type-id-no-action'] = $routeHelper->getControllers();
		$parameters['type-id'] = $routeHelper->getControllers();
		$parameters['id'] = $routeHelper->getControllerMap(['alert', 'reply', 'issue', 'revision', 'request']);
		$parameters['type'] = $routeHelper->getControllerMap(['alert', 'request']);
		$parameters['action-only'] = $routeHelper->getControllerMap(['alerts', 'reply', 'issue', 'revision', 'request']);
		$parameters['none'] = $routeHelper->getControllerMap(['alert', 'reply', 'issue', 'revision', 'request']);
		$routes = $routeHelper->create($parameters);
		return $routes;
	}

	public function bootstrap($app)
	{
		/**
		 * Setup urls
		 */
        $app->getUrlManager()->addRules(self::getUrls(), false);
	}
}
