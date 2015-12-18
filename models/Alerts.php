<?php

namespace nitm\widgets\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Event;
use nitm\models\User;

/**
 * This is the model class for table "alerts".
 *
 * @property integer $id
 * @property integer $remote_id
 * @property string $remote_type
 * @property string $remote_for
 * @property integer $user_id
 * @property string $action
 * @property integer $global
 * @property integer $disabled
 * @property string $created_at
 *
 * @property User $user
 */
class Alerts extends \nitm\models\Alerts
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
