<?php
namespace nitm\widgets\traits\relations;

use nitm\widgets\models\Category as CategoryModel;

/**
 * Traits defined for expanding active relation scopes until yii2 resolves traits issue
 */

trait Request {
	public $requestModel;
	
	protected static $urgency = [
		'normal',
		'important',
		'critical'
	];
	
	public function getStatus()
	{
		return isset(static::$urgency[$this->status]) ? static::$urgency[$this->status] : 'normal';
	}
	
	public function getUrgency()
	{
		return ucfirst($this->getStatus());
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestFor()
    {
        return $this->hasOne(CategoryModel::className(), ['id' => 'request_for_id']);
    }
	
	public function requestFor()
	{
		return \nitm\helpers\Relations::getRelatedRecord('requestFor', $this, CategoryModel::className());
	}
}
?>
