<?php
namespace nitm\widgets\traits\relations;

use nitm\widgets\models\Category as CategoryModel;

trait Revisions {
	
	public function isOutsideInterval()
	{
		return (abs(strtotime('now') - strtotime($this->created_at))/3600) >= $this->interval;
	}
}
?>
