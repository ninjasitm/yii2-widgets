<?php
namespace nitm\widgets\traits;

use yii\base\Event;
use yii\db\ActiveRecord;

/**
 * Traits defined for expanding active relation scopes until yii2 resolves traits issue
 */

trait BaseWidget {
	
	public $_value;
	public $constrain;
	public $constraints = [];
	public $initSearchClass = true;
	
	public static $usePercentages;
	public static $allowMultiple;
	public static $individualCounts;
	public static $statuses = [
		'normal' => 'default',
		'important' => 'info',
		'critical' => 'error'
	];
	
	protected $_new; 
	protected $_supportedConstraints =  [
		'parent_id' => [0, 'id', 'parent_id'],
		'parent_type' => [1, 'type', 'parent_type'],
	];
	
	protected static $userLastActive;
	
	private static $_dateFormat = "D M d Y h:iA";
	
	public function scenarios()
	{
		$scenarios = [
			'count' => ['parent_id', 'parent_type'],
		];
		return array_merge(parent::scenarios(), $scenarios);
	}
	
	/**
	 * Get the constraints for a widget model
	 */
	public function getConstraints()
	{
		switch(sizeof($this->constraints) == 0)
		{
			case true:
			foreach($this->_supportedConstraints as $attribute=>$supported)
			{
				if($this->hasProperty($attribute) || $this->hasAttribute($attribute))
				{
					$this->constraints[$attribute] = $this->$attribute;
				}
			}
			$this->queryFilters = $this->constraints;
			break;
		}
		return $this->constraints;
	}
	
	/*
	 * Set the constrining parameters
	 * @param mixed $using
	 */
	public function setConstraints($using)
	{
		foreach($this->_supportedConstraints as $attribute=>$supported)
		{
			foreach($supported as $attr)
			{
				switch(isset($using[$attr]))
				{
					case true:
					switch($attribute)
					{
						case 'parent_type':
						$using[$attr] = strtolower(array_pop(explode('\\', $using[$attr])));
						break;
					}
					$this->constraints[$attribute] = $using[$attr];
					$this->$attribute = $using[$attr];
					break;
				}
			}
		}
		$this->queryFilters = array_replace($this->queryFilters, $this->constraints);
	}
	
	/**
	 * Find a model
	 */
	 public static function findModel($constrain)
	 {
		$model = self::initCache($constrain);
		$model->setConstraints($constrain);
		$model->addWith([
			'last' => function ($query) {
				$query->andWhere($model->queryFilters);
			}
		]);
		$ret_val = $model->find()->one();
		switch(is_a($ret_val, static::className()))
		{
			case true:
			$ret_val->queryFilters = $model->queryFilters;
			$ret_val->constraints = $model->constraints;
			//$ret_val->populateMetadata();
			break;
			
			default:
			$ret_val = $model;
			break;
		}
		return $ret_val;
	 }
	
	/**
	 * Get the count for the current parameters
	 * @return \yii\db\ActiveQuery
	 */
	 public function getCount()
	 {
		$primaryKey = $this->primaryKey()[0];
		$ret_val = parent::getCount($this->link);
		switch(isset($this->queryFilters['value']))
		{
			case true:
			switch($this->queryFilters['value'])
			{
				case -1:
				$andWhere = ['<=', 'value',  0];
				break; 
				
				case 1:
				$andWhere = ['>=', 'value', 1];
				break;
			}
			unset($this->queryFilters['value']);
			$ret_val->andWhere($andWhere);
			break;
		}
		return $ret_val;
	 }

    /**
	 * This is here to allow base classes to modify the query before finding the count
     * @return \yii\db\ActiveQuery
     */
    public function getFetchedValue()
    {
		$primaryKey = $this->primaryKey()[0];
		$ret_val = $this->hasOne(static::className(), $this->link);
		$valueFilter = @$this->queryFilters['value'];
		unset($this->queryFilters['value']);
		switch(static::$allowMultiple)
		{
			case true:
			$select = [
				"_down" => "SUM(IF(value<=0, value, 0))",
				"_up" => "SUM(IF(value>=1, value, 0))"
			];
			break;
			
			default:
			$select = [
				'_down' => "SUM(value=-1)",
				"_up" => "SUM(value=1)"
			];
			break;
		}
		$filters = $this->queryFilters;
		unset($filters['parent_id'], $filters['parent_type']);
		return $ret_val->select($select)
			->andWhere($filters);
    }
	
	public function fetchedValue()
	{
		return $this->hasProperty('fetchedValue') && isset($this->fetchedValue) ? $this->fetchedValue->_value : 0;
	}
	
	public function hasNew()
	{
		return \nitm\helpers\Relations::getRelatedRecord('newCount', $this, static::className(), [
			'_new' => 0
		])->_new;
	}
	
	public function getNewCount()
	{
		$primaryKey = $this->primaryKey()[0];
		$ret_val = $this->hasOne(static::className(), $this->link);
		$andWhere = ['or', "created_at>='".static::currentUser()->lastActive()."'"];
		$ret_val->select([
				'_new' => 'COUNT('.$primaryKey.')'
			])
			->andWhere($andWhere);
		static::currentUser()->updateActivity();
		return $ret_val;
	}
	
	/*
	 * Get the author for this object
	 * @return mixed user array
	 */
	public function isNew()
	{
		static::$userLastActive = is_null(static::$userLastActive) ? static::currentUser()->lastActive() : static::$userLastActive;
		return strtotime($this->created_at) > strtotime(static::$userLastActive);
	}
	
	/*
	 * Get the author for this object
	 * @return boolean
	 */
	public function hasAny()
	{
		return $this->count() >= 1;
	}
	
	
	/*
	 * Get the author for this object
	 * @return mixed user array
	 */
	public function getLast()
	{
		$ret_val = $this->hasOne(static::className(), $this->link)
			->orderBy([array_shift($this->primaryKey()) => SORT_DESC])
			->with('author');
		return $ret_val;
	}
	
	public function currentUser()
	{
		if(\Yii::$app instanceof \yii\console\Application)
			return new \nitm\models\User(['username' => 'console']);
			
		if(\Yii::$app->getUser()->getIsGuest()) {
			return \nitm\helpers\Cache::getCachedModel($this, 
				'currentUser', 
				\Yii::$app->getUser()->identityClass, 
				null, 
				[
					'id' => 1
				]);
		}
		else {
			return \Yii::$app->getUser()->getIdentity();
		}
	}
	 
	protected function populateMetadata()
	{
		switch(!isset($this->count) && !isset($this->hasNew))
		{
			case true:
			$sql = static::find()->select([
				"_count" => 'COUNT(id)',
				"_hasNew" => 'SUM(IF(created_at>='.static::currentUser()->lastActive().", 1, 0))"
			])
			->where($this->getConstraints());
			$metadata = $sql->createCommand()->queryAll();
			static::currentUser()->updateActivity();
			break;
		}
	}
	
	protected static function initCache($constrain, $key=null)
	{
		if(!\nitm\helpers\Cache::exists($key))
		{
			$class = static::className();
			$model = new $class(['initSearchClass' => false]);
			$model->setConstraints($constrain);
			$key = is_null($key) ? \nitm\helpers\Cache::cacheKey($model, ['parent_id', 'parent_type']) : array_keys($constrain);
			\nitm\helpers\Cache::setModel($key, [$model->className(), \yii\helpers\ArrayHelper::toArray($model)]);
		}
		else {
			$array = \nitm\helpers\Cache::getModel($key);
			$model = new $array[0]($array[1]);
		}
		return $model;
	}
}
?>
