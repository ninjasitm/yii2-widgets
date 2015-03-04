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

class BaseWidget extends \nitm\models\Entity implements DataInterface
{
	use \nitm\traits\Nitm, \nitm\widgets\traits\BaseWidget;
	
	protected static $userLastActive;
	protected static $currentUser;
	
	public function scenarios()
	{
		$scenarios = [
			'count' => ['parent_id', 'parent_type'],
		];
		return array_merge(parent::scenarios(), $scenarios);
	}
	
	public function behaviors()
	{
		$behaviors = [
		];
		return array_merge(parent::behaviors(), $behaviors);
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
	
	public function init()
	{
		$this->setConstraints($this->constrain);
		parent::init();
		$this->addWith(['author']);
		if($this->initSearchClass)
			//static::initCache($this->constrain, self::cacheKey($this->getId()));
		
		static::$userLastActive = date('Y-m-d G:i:s', strtotime(is_null(static::$userLastActive) ? static::currentUser()->lastActive() : static::$userLastActive));
		$this->initEvents();
	}
	
	protected function initEvents()
	{
		Event::on(static::className(), ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'beforeSaveEvent']);
		Event::on(static::className(), ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'beforeSaveEvent']);
		Event::on(static::className(), ActiveRecord::EVENT_AFTER_INSERT, [$this, 'afterSaveEvent']);
		Event::on(static::className(), ActiveRecord::EVENT_AFTER_UPDATE, [$this, 'afterSaveEvent']);
	}
}
?>