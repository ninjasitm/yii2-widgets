<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\replies;

use Yii;
use nitm\widgets\models\BaseWidget;
use nitm\models\Replies as RepliesModel;

class RepliesMessage extends BaseWidget
{	
	public function init()
	{
		switch(1)
		{
			case !($this->model instanceof RepliesModel) && (($this->parentType == null) || ($this->parentId == null) || ($this->parentKey == null)):
			$this->model = null;
			break;
			
			default:
			$this->model = ($this->model instanceof RepliesModel) ? $this->model : RepliesModel::findModel([$this->parentId, $this->parentType, $this->parentKey]);
			break;
		}
		parent::init();
	}
	
	public function run()
	{
		$message = '';
		switch(($this->model instanceof RepliesModel))
		{
			case true:
			$message = $this>render('@nitm/views/replies/view',['model' => $this->model])
			break;
		}
		return $message;
	}
}
?>