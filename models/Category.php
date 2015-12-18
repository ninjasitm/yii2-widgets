<?php

namespace nitm\widgets\models;

use Yii;
use nitm\helpers\Cache;

/**
 * This is the model class for table "categories".
 *
 * @property integer $id
 * @property integer $parent_ids
 * @property string $name
 * @property string $slug
 * @property string $html_icon
 * @property string $created
 * @property string $updated
 */
class Category extends \nitm\models\Category
{
	use \nitm\widgets\traits\BaseWidgetModel, \nitm\filemanager\traits\Relations;

	public function init()
	{
		$this->setConstraints($this->constrain);
		parent::init();
		$this->addWith(['author']);
		//if($this->initSearchClass)
			//static::initCache($this->constrain, self::cacheKey($this->getId()));
		static::updateCurrentUserActivity();
	}
}
