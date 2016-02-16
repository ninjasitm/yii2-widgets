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

class BaseWidgetModel extends \nitm\models\Entity
{
	use \nitm\widgets\traits\BaseWidgetModel, \nitm\filemanager\traits\Relations;

	protected $link = [
		'parent_type' => 'parent_type',
		'parent_id' => 'parent_id'
	];

	public function attributes()
	{
		return array_merge(parent::attributes(), [
			'_count', '_new'
		]);
	}

	public function init()
	{
		$this->setConstraints($this->constrain);
		$this->addWith(['author']);
		if($this->initSearchClass)
			//static::initCache($this->constrain, self::cacheKey($this->getId()));

		if(is_object(static::currentUser()))
			static::$userLastActive = date('Y-m-d G:i:s', strtotime(is_null(static::$userLastActive) ? static::currentUser()->lastActive() : static::$userLastActive));
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

	public function indexUrl($baseUrl=null, $extraParams = [])
	{
		$urlParams = [];
		$baseUrl = (is_null($baseUrl) ? $this->isWhat() : $baseUrl).'/index';
		$urlParams['__format'] = isset($extraParams['__format']) ? $extraParams['__format'] : 'prepared';
		$attributes = array_intersect_key($this->link, array_flip(['parent_type', 'parent_id', 'remote_type', 'remote_id']));
		return \Yii::$app->urlManager->createUrl(array_merge([rtrim($baseUrl, '/').'/'.implode('/', $this->getAttributes($attributes))], $urlParams, $extraParams));
	}

	/**
	 * Get the query that orders items by their activity
	 */
	public function getSort()
	{
		$ret_val = [];
		//Create the user sort parameters
		static::addSortParams($ret_val, [
			'rating' => [\nitm\widgets\models\Rating::tableName() , 'Rating', new \yii\db\Expression('GREATEST(rating.id)')],
			'vote' => [\nitm\widgets\models\Vote::tableName() , 'Vote', new \yii\db\Expression('COUNT(vote.id)')],
		]);
		return array_merge(parent::getSort(), $ret_val);
	}
}
?>
