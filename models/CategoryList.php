<?php

namespace nitm\widgets\models;

use Yii;

/**
 * This is the model class for table "category_list".
 *
 * @property integer $id
 * @property integer $author_id
 * @property string $created_at
 * @property integer $editor_id
 * @property string $updated_at
 * @property integer $remote_id
 * @property string $remote_type
 * @property string $remote_class
 * @property integer $category_id
 * @property integer $priority
 * @property boolean $deleted
 * @property string $deleted_at
 * @property integer $deleted_by
 * @property boolean $disabled
 * @property string $disabled_at
 * @property integer $disabled_by
 *
 * @property CategoryListMetadata[] $categoryListMetadatas
 * @property User $author
 * @property User $editor
 * @property Category $category
 */
class CategoryList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author_id', 'remote_id', 'remote_type', 'remote_class', 'category_id'], 'required'],
            [['author_id', 'editor_id', 'remote_id', 'category_id', 'priority', 'deleted_by', 'disabled_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at', 'disabled_at'], 'safe'],
            [['remote_class'], 'string'],
            [['deleted', 'disabled'], 'boolean'],
            [['remote_type'], 'string', 'max' => 32],
            [['remote_id', 'remote_type', 'category_id'], 'unique', 'targetAttribute' => ['remote_id', 'remote_type', 'category_id'], 'message' => 'The combination of Remote ID, Remote Type and Category ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'author_id' => Yii::t('app', 'Author ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'editor_id' => Yii::t('app', 'Editor ID'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'remote_id' => Yii::t('app', 'Remote ID'),
            'remote_type' => Yii::t('app', 'Remote Type'),
            'remote_class' => Yii::t('app', 'Remote Class'),
            'category_id' => Yii::t('app', 'Category ID'),
            'priority' => Yii::t('app', 'Priority'),
            'deleted' => Yii::t('app', 'Deleted'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'disabled' => Yii::t('app', 'Disabled'),
            'disabled_at' => Yii::t('app', 'Disabled At'),
            'disabled_by' => Yii::t('app', 'Disabled By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryListMetadatas()
    {
        return $this->hasMany(CategoryListMetadata::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEditor()
    {
        return $this->hasOne(User::className(), ['id' => 'editor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}
