<?php

namespace nitm\widgets\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use nitm\widgets\models\Request as RequestModel;

/**
 * Request represents the model behind the search form about `nitm\widgets\models\Request`.
 */
class Request extends BaseSearch
{
	use \nitm\widgets\traits\relations\Request;
}
