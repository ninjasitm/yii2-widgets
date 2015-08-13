<?php

namespace nitm\widgets\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use nitm\widgets\models\Revisions as RevisionsModel;

/**
 * Revisions represents the model behind the search form about `nitm\widgets\models\Revisions`.
 */
class Revisions extends BaseSearch
{
	use \nitm\widgets\traits\relations\Revisions;
}
