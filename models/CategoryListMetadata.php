<?php

namespace nitm\widgets\models;

use Yii;

/**
 * This is the model class for table "category_list_metadata".
 *
 * @property integer $id
 * @property integer $content_id
 * @property string $key
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
 *
 * @property CategoryList $content
 */
class CategoryListMetadata extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_list_metadata';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_id', 'key', 'value'], 'required'],
            [['content_id'], 'integer'],
            [['value'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['key'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'content_id' => Yii::t('app', 'Content ID'),
            'key' => Yii::t('app', 'Key'),
            'value' => Yii::t('app', 'Value'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(CategoryList::className(), ['id' => 'content_id']);
    }
}
