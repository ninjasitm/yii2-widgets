<?php

namespace nitm\widgets\traits\relations;

use nitm\traits\Relations;
//use nitm\widgets\models\category\CategoriesMetadata;

trait Category
{

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getCategoriesMetadata()
    {
        return $this->hasMany(Relations::getRelationClass(CategoriesMetadata::className(), get_called_class()), ['category_id' => 'id']);
    }
	
	public function categoriesMetadata()
	{
		return \nitm\helpers\Relations::getRelatedRecord('categoriesMetadata', $this, []);
	}*/
}
