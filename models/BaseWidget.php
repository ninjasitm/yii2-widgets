<?php

namespace nitm\widgets\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\base\Event;
use yii\db\ActiveRecord;
use nitm\widgets\models\Data;
use nitm\widgets\User;
use nitm\widgets\models\security\Fingerprint;
use nitm\interfaces\DataInterface;
use nitm\helpers\Cache;

/**
 * Class BaseWidget
 * @package nitm\widgets\models
 *
 */

class BaseWidget extends \nitm\models\Entity implements DataInterface
{
	use \nitm\traits\Nitm, \nitm\widgets\traits\BaseWidget;
}
?>