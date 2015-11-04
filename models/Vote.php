<?php

namespace nitm\widgets\models;

use nitm\models\User;
use nitm\helpers\ArrayHelper;

/**
 * This is the model class for table "vote".
 *
 * @property integer $id
 * @property integer $author_id
 * @property string $created_at
 * @property string $parent_type
 * @property integer $parent_id
 */
class Vote extends BaseWidget
{
	public $_up;
	public $_down;

	protected $_rating;

	protected static $maxVotes;

	public function init()
	{
		parent::init();
		$this->initConfig(static::isWhat());
		static::$allowMultiple = isset(static::$allowMultiple) ? static::$allowMultiple : \Yii::$app->getModule('nitm-widgets')->voteOptions['allowMultiple'];
		static::$usePercentages = isset(static::$usePercentages) ? static::$usePercentages : \Yii::$app->getModule('nitm-widgets')->voteOptions['usePercentages'];
		static::$individualCounts = isset(static::$individualCounts) ? static::$individualCounts : \Yii::$app->getModule('nitm-widgets')->voteOptions['individualCounts'];
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author_id', 'parent_type', 'parent_id'], 'required', 'on' => ['update', 'create']],
			[['value'], 'required', 'on' => ['update']],
            [['author_id', 'parent_id'], 'integer'],
            [['created_at'], 'safe'],
            [['parent_type'], 'string', 'max' => 64],
            [['author_id', 'parent_type', 'parent_id'], 'unique', 'targetAttribute' => ['author_id', 'parent_type', 'parent_id'], 'message' => 'The combination of User ID, parent Type and parent ID has already been taken.', 'on' =>['create']]
        ];
    }

	public function scenarios()
	{
		$scenarios = [
			'default' => ['author_id', 'parent_type', 'parent_id', 'value'],
			'update' => ['author_id', 'parent_type', 'parent_id', 'value'],
			'create' => ['author_id', 'parent_type', 'parent_id'],
		];

		return array_merge(parent::scenarios(), $scenarios);
	}

	public function fields()
	{
		return array_merge(parent::fields(), ['_up', '_down']);
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'User ID',
            'created_at' => 'Created At',
            'parent_type' => 'parent Type',
            'parent_id' => 'parent ID',
        ];
    }

	public function allowMultiple()
	{
		return static::$allowMultiple;
	}

	public function individualCounts()
	{
		return static::$individualCounts;
	}

	public function usePrecentages()
	{
		return static::$usePrecentages;
	}

	/**
	 * Get rating parameters
	 * @method rating
	 * @param  string|null $get         Get a specific part of the rating
	 * @param  boolean $reCalculate Force a recalculation
	 * @return mixed              The rating value being sought
	 */
	public function rating($get=null, $reCalculate=false)
	{
		if(isset($this->_rating) && $reCalculate === false)
			$ret_val = $this->_rating;
		else {
			$ret_val = ['positive' => 0, 'negative' => 0, 'ratio' => 0];
			static::$allowMultiple = false;
			$max = static::getMax($this);
			$ret_val = [
				'positive' => round(((int)$this->fetchedValue('_up')/$max) * 100),
				'negative' => round(((int)$this->fetchedValue('_down')/$max) * 100)
			];
			$ret_val['ratio'] = round(abs($this->fetchedValue('_up') - abs($this->fetchedValue('_down')))/$max, 2);
			$ret_val['max'] = $max;
			$this->_rating = $ret_val;
		}
		return ArrayHelper::getValue($ret_val, $get, $ret_val);
	}

	/**
	 * Get the rating, percentage out of 100%
	 * @return int
	 */
	public static function getMax($vote=null)
	{
		switch(isset(static::$maxVotes))
		{
			case false:
			if(!static::$allowMultiple)
				$ret_val = User::find()->where(['disabled' => 0])->count();
			else
				$ret_val = $vote instanceof static ? $vote->fetchedValue('_up')+$vote->fetchedValue('_down') : 1000;
			$ret_val = !$ret_val ? 1 : $ret_val;
			static::$maxVotes = $ret_val;
			break;

			default:
			$ret_val = static::$maxVotes;
			break;
		}
		return (int)$ret_val;
	}

	public function getCurrentUserVoted()
	{
		$primaryKey = $this->primaryKey()[0];
		return $this->hasOne(static::className(), $this->link)
			->select(['*', '_down' => "SUM(value=-1)", "_up" => "SUM(value=1)"])
			->andWhere(['author_id' => static::currentUser()->getId()])
			->groupBy(array_keys($this->link));
	}

	/**
	 *
	 */
	public function currentUserVoted($direction)
	{
		$ret_val = false;
		$model = \nitm\helpers\Relations::getRelatedRecord('currentUserVoted', $this, static::className(), [
			'_up' => 0,
			'_down' => 0
		]);
		//If we don't multiple votes then we will check, otherwise let the user vote!
		switch(static::$allowMultiple)
		{
			case true:
			switch(1)
			{
				case ($model['_down'] >= 1) && $direction == 'down':
				case ($model['_up'] >= 1) && $direction == 'up':
				$ret_val = true;
				break;
			}
			break;

			default:
			switch(1)
			{
				case ($model['value'] == -1) && $direction == 'down':
				case ($model['value'] == 1) && $direction == 'up':
				$ret_val = true;
				break;
			}
			break;
		}
		return $ret_val;
	}

	/**
	 * Get the Color indicator for this vote
	 * @method getIndicator
	 * @return string       the RGB color
	 */
	public function getIndicator()
	{
		if($this->rating('positive') > $this->rating('negative'))
			$color = '51, 192, 0';
		else
			$color = '192, 51, 0';
		return "rgba(".$color.",".$this->rating('ratio').")";
	}

	public function getIndicators()
	{
		return [
			'up' => 'text-success',
			'down' =>  'text-danger',
		];
	}
}
