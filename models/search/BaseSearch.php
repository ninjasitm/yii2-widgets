<?php

namespace nitm\widgets\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BaseElasticSearch provides the basic search functionality based on the class it extends from.
 */
class BaseSearch extends \nitm\search\BaseSearch
{	
	use \nitm\traits\Nitm, \nitm\traits\Relations,\nitm\traits\Cache,
		\nitm\widgets\traits\Relations;
	
	public $engine = 'elasticsearch';
	public static $namespace = '\nitm\widgets\models\\';
}