<?php

namespace nitm\widgets\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use nitm\widgets\models\Replies as RepliesModel;

/**
 * Replies represents the model behind the search form about `app\models\Replies`.
 */
class Replies extends BaseSearch
{
	use \nitm\widgets\traits\relations\Replies;
	
	protected $link = [
		'parent_type' => 'parent_type',
		'parent_id' => 'parent_id'
	];
}
