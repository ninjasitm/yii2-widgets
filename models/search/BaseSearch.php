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
		\nitm\widgets\traits\BaseWidgetModel, \nitm\widgets\traits\Relations {
			\nitm\widgets\traits\BaseWidgetModel::getCount insteadof \nitm\traits\Relations;
		}

	public $engine = 'db';
	public static $namespace = '\nitm\widgets\models\\';
}
