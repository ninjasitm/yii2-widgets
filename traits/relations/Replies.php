<?php
namespace nitm\widgets\traits\relations;

/**
 * Traits defined for expanding active relation scopes until yii2 resolves traits issue
 */

trait Replies {
	
	/**
	 * Return the reply author_id information
	 * @param string $what The property to return
	 */
	public function getReplyTo()
	{
		return $this->hasOne(\nitm\widgets\models\Replies::className(), ['id' => 'reply_to'])->with('author');
	}
	
	public function replyTo()
	{	
		return \nitm\helpers\Relations::getRelatedRecord('replyTo', $this, null);
	}
}
?>
