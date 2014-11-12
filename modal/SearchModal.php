<?php
/**
* @link http://www.yiiframework.com/
* @copyright Copyright (c) 2008 Yii Software LLC
* @license http://www.yiiframework.com/license/
*/

namespace nitm\widgets\modal;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;
use kartik\widgets\ActiveForm;
use kartik\widgets\ActiveField;

class SearchModal extends Modal
{	
	public $options = [
		'id' => 'search-modal'
	];
	
	/*
	 * The size of the widget [large, mediaum, small, normal]
	 */
	public $size = 'large';
	
	public function init()
	{
		parent::init();
		SearchModalAsset::register($this->getView());
	}
	
	public function run()
	{
		$this->header = !isset($this->header) ? $this->getDefaultHeader() : $this->header;
		$this->content = !($this->content) ? $this->getDefaultContent() : $this->content;
		return parent::run().Html::script("\$nitm.onModuleLoad('search-modal', function () {
			\$nitm.module('search-modal').initSearch('#".$this->options['id']."');
		});");
	}
	
	protected function getDefaultHeader()
	{
		return '<br><form action="/search" method="get">
			<div class="row"><br>
			  <div class="col-lg-12 col-sm-12 col-md-12">
				<div class="input-group">
				  <input onFocus="this.value = this.value;" type="text" name="q" class="form-control" id="search-field">
				  <span class="input-group-btn">
					<button class="btn btn-default" type="submit">Search</button>
				  </span>
				</div><!-- /input-group -->
			  </div><!-- /.col-lg-6 -->
			</div><!-- /.row -->
		</form>';
	}
	
	protected function getDefaultContent()
	{
		return '<div id="search-results" style="color: #000"></div>';
	}
}
?>