<?php

namespace nitm\widgets\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\base\Event;
use yii\db\ActiveRecord;
use nitm\widgets\models\Data;
use nitm\widgets\User;
use nitm\widgets\models\security\Fingerprint;
use nitm\interfaces\DataInterface;
use nitm\helpers\Cache;

/**
 * Class BaseWidget
 * @package nitm\widgets\models
 *
 */

class BaseWidget extends \nitm\models\Data implements DataInterface
{
	use \nitm\traits\Nitm, \nitm\widgets\traits\BaseWidget, \nitm\filemanager\traits\Relations;
	
	protected static $userLastActive;
	
	public function init()
	{
		$this->setConstraints($this->constrain);
		parent::init();
		$this->addWith(['author']);
		if($this->initSearchClass)
			//static::initCache($this->constrain, self::cacheKey($this->getId()));
		
		static::$userLastActive = date('Y-m-d G:i:s', strtotime(is_null(static::$userLastActive) ? static::currentUser()->lastActive() : static::$userLastActive));
	}
	
	public function beforeSaveEvent($event)
	{
		static::prepareAlerts($event);
	}
	
	public function afterSaveEvent($event)
	{
	}
	
	public function scenarios()
	{
		$scenarios = [
			'count' => ['parent_id', 'parent_type'],
		];
		return array_merge(parent::scenarios(), $scenarios);
	}
	
	public static function has()
	{
		$has = [
			'author' => null, 
			'editor' => null,
			'hidden' => null,
			'deleted' => null,
		];
		return array_merge(parent::has(), $has);
	}
	
	public function currentUser()
	{
		if(\Yii::$app->user->getIsGuest()) {
			return \nitm\helpers\Cahce::getCachedModel($this, 
				'currentUser', 
				\Yii::$app->user->identityClass, 
				null, 
				[
					'id' => 1
				]);
		}
		else {
			return \Yii::$app->user->getIdentity();
		}
	}
}
?>