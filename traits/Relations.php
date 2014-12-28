<?php
namespace nitm\widgets\traits;

use nitm\helpers\Cache;
use nitm\traits\Relations as NitmRelations;

/**
 * Traits defined for expanding active relation scopes until yii2 resolves traits issue
 */

trait Relations {
	
	/**
	 * Widget based relations
	 */
	protected function getWidgetRelationQuery($className, $link=null, $options=[], $many=false)
	{
		$link = !is_array($link) ? ['parent_id' => 'id'] : $link;
		$options = is_array($options) ? $options : (array)$options;
		$options['select'] = isset($options['select']) ? $options['select'] : ['id', 'parent_id', 'parent_type'];
		$options['with'] = isset($options['with']) ? $options['with'] : [];
		$options['andWhere'] = isset($options['andWhere']) ? $options['andWhere'] : ['parent_type' => $this->isWhat()];
		return $this->getRelationQuery($className, $link, $options, $many);
	}
	
	/**
	 * Widget based relations
	 */
	protected function getWidgetRelationModelQuery($className, $link=null, $options=[])
	{
		$link = !is_array($link) ? ['parent_id' => 'id'] : $link;
		$options['select'] = isset($options['select']) ? $options['select'] : ['id', 'parent_id', 'parent_type'];
		$options['with'] = isset($options['with']) ? $options['with'] : [];
		return $this->getRelationQuery($className, $link, $options);
	}
	
	protected function getCachedWidgetModel($className, $idKey=null, $many=false, $options=[])
	{
		$relation = \nitm\helpers\Helper::getCallerName();
		$options['construct'] = isset($options['construct']) ? $options['construct'] : [
			'parent_id' => $this->getId(), 
			'parent_type' => $this->isWhat()
		];
		$idKey = is_null($idKey) ? ['getId', 'isWhat'] : $idKey;
		return $this->getCachedRelation(Cache::cacheKey($this, $idKey, $relation, $many), $className, $options, $many, $relation);
	}
	
	public function replyModel()
	{
		return $this->getCachedWidgetModel(\nitm\widgets\models\Replies::className());
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReplyModel()
    {
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Replies::className());
    }

    /**
	 * Get replies relation
	 * @param array $options Options for the relation
     * @return \yii\db\ActiveQuery
     */
    public function getReplies($options=[])
    {
		$params = [
			"parent_type" => $this->isWhat()
		];
		switch(\Yii::$app->user->identity->isAdmin())
		{
			case false:
			$params['hidden'] = 0;
			break;
		}
		$options = array_merge([
			'orderBy' => ['id' => SORT_DESC],
			'with' => ['replyTo'],
			'andWhere' => $params
		], $options);
       	return $this->getWidgetRelationQuery(\nitm\widgets\models\Replies::className(), null, $options);
    }
	
	public function replies()
	{
		return $this->getCachedRelation(Cache::cacheKey($this, 'id', 'replies', true), \nitm\widgets\models\Replies::className(), [], true);
	}
	
	public function issueModel()
	{
		return $this->getCachedWidgetModel(\nitm\widgets\models\Issues::className());
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIssueModel()
    {
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Issues::className());
    }

    /**
	 * Get issues relation
	 * @param array $options Options for the relation
     * @return \yii\db\ActiveQuery
     */
    public function getIssues($options=[])
    {
		$options = array_merge([
			'orderBy' => ['id' => SORT_DESC],
		], $options);
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Issues::className(), null, $options, true);
    }
	
	public function issues()
	{
		return $this->getCachedRelation(Relations::cacheKey($this, 'id', 'issues', true), \nitm\widgets\models\Issues::className(), [], true);
	}

    /**
	 * Get revisions relation
	 * @param array $options Options for the relation
     * @return \yii\db\ActiveQuery
     */
    public function getRevisions($options=[])
    {
		$options = array_merge([
			'orderBy' => ['id' => SORT_DESC],
		], $options);
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Revisions::className(), null, $options, true);
    }
	
	public function revisions()
	{
		return $this->getCachedRelation(Relations::cacheKey($this, 'id', 'revisions', true), \nitm\widgets\models\Revisions::className(), [], true);
	}
	
	public function revisionModel()
	{
		return $this->getCachedWidgetModel(\nitm\widgets\models\Revisions::className());
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionModel()
    {
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Revisions::className());
    }

    /**
	 * Get votes relation
	 * @param array $options Options for the relation
     * @return \yii\db\ActiveQuery
     */
    public function getVotes($options=[])
    {
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Issues::className(), null, $options, true);
    }
	
	public function votes()
	{
		return $this->getCachedRelation(Relations::cacheKey($this, 'id', 'votes', true), \nitm\widgets\models\Vote::className(), [], true);
	}
	
	public function voteModel()
	{
		return $this->getCachedWidgetModel(\nitm\widgets\models\Vote::className());
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVoteModel()
    {
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Vote::className());
    }

    /**
	 * Get rating relation
	 * @param array $options Options for the relation
     * @return \yii\db\ActiveQuery
     */
    public function getRating($options=[])
    {
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Rating::className(), ['parent_id' => 'id'], [], true);
    }
	
	public function rating()
	{
		$options = array_merge([
			'orderBy' => ['id' => SORT_DESC],
		], $options);
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Issues::className(), [
			'remote_id' => $this->getId(), 
			'remote_type' => $this->isWhat()
		], $options, true);
	}
	
	public function ratingModel()
	{
		return $this->getCachedWidgetModel(\nitm\widgets\models\Rating::className());
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRatingModel()
    {
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Rating::className());
    }
	
	public function followModel()
	{
		return $this->getCachedWidgetModel(\nitm\widgets\models\Alerts::className(), null, false, [
			'select' => ['id', 'remote_id', 'remote_type'],
			'construct' => [
				'remote_id' => $this->getId(), 
				'remote_type' => $this->isWhat()
			]
		]);
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowModel()
    {
        return $this->getWidgetRelationModelQuery(\nitm\widgets\models\Alerts::className(), ['remote_id' => 'id'], [
			//Disabled due to Yii framework inability to return statistical relations
			//'with' => ['currentUserVoted', 'fetchedValue']
			'select' => ['id', 'user_id', 'remote_id', 'remote_type'],
			'where' => [
				'remote_type' => $this->isWhat(),
				'user_id' => \Yii::$app->user->getId()
			]
		]);
    }
 }
?>
