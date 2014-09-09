<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace nitm\widgets\metadata;

use yii\helpers\Html;
use kartik\icons\Icon;

/**
 * ShortLink widget renders the address of a short link and a modal view button
 */
class ShortLink extends \yii\base\Widget
{
	public $title = 'Short Link';
	public $url = '#';
	public $header = '';
	public $type;
	public $size = 'normal';
	public $useLabel = true;
	public $viewOptions = [];
	public $inputOptions = [];
	public $modalOptions = [];
	
	protected $urlParts = [];
	protected $isIp;
	protected $isForeign;
	protected $isRoute;
	
	public function init()
	{  
		$this->urlParts = parse_url($this->url);
		$this->urlParts['host'] = isset($this->urlParts['host']) ? $this->urlParts['host'] : (isset($this->urlParts['path']) ? $this->urlParts['path'] : null);
		$this->isIp = \nitm\helpers\Network::isValidIp($this->urlParts['host']);
		$this->isForeign = ($this->urlParts['host'] != $_SERVER['SERVER_NAME']);
		$this->isRoute = isset($this->urlParts['host']) && !$this->isIp;
	}
	
	public function run () 
	{
		$shortLinkLabel = $this->useLabel ? Html::tag(
			'span',
			$this->title,
			[
				'class' => 'input-group-addon'
			]
		) : '';
		$shortLinkInput = Html::input(
			'text', 
			$this->title,
			(empty($this->inputOptions['text']) ? $this->url : $this->inputOptions['text']), 
			array_merge([
				'class' => 'form-control',
			],
			$this->inputOptions)
		);
		$shortLinkButton = Html::a(
			"View ".Icon::show('eye'),
			$this->url,
			array_merge([
				'class' => 'input-group-addon',
			],
			$this->viewOptions)
		);
		switch($this->type)
		{
			case 'modal':
			$this->modalOptions = array_merge([
				'size' => $this->size,
				'header' => $this->header,
				'toggleButton' => [
					'label' => Icon::show('eye')." Modal",
					'data-remote' => $this->getUrl('modal'),
					'class' => 'btn btn-default',
					'wrapper' => [
						'tag' => 'span',
						'class' => 'input-group-btn',
						
					]
				],
				'options' => [
					'id' => 'short-link'.uniqid()
				]
			], $this->modalOptions);
			$shortLinkButton = \nitm\widgets\modal\Modal::widget($this->modalOptions);
			break;
			
			default:
			$shortLinkButton = Html::a(
				Icon::show('eye').(!isset($this->viewOptions['label']) ? " In Page" : $this->viewOptions['label']),
				$this->getUrl('page'),
				array_merge([
					'class' => 'input-group-addon',
				],
				$this->viewOptions)
			);
			$this->viewOptions = [];
			break;
		}
		$newWindowLinkButton = Html::a(
			Icon::show('eye')." Window",
			'javascript:void(0)',
			array_merge([
				'class' => 'input-group-addon',
				'onclick' => "javascript:window.open('".$this->getUrl('popup')."', '".uniqid()."', 'width=800, height=900, location=0, scrollbars=1')"
			],
			$this->viewOptions)
		);
		$shortLink = Html::tag(
			'div',
			$shortLinkLabel.$shortLinkInput.$shortLinkButton.$newWindowLinkButton,
			[
				'class' => 'input-group',
				'style' => 'width:100%'
			]
		);
		echo $shortLink;
	}
	
	protected function getUrl($type='page')
	{
		$args = (isset($this->urlParts['query']) && !empty($this->urlParts['query'])) ? parse_str($this->urlParts['query']) : [];
		$args = empty($args) ? [] : $args;
		$url = $this->urlParts['host'];
		$scheme = (isset($this->urlParts['scheme']) && !empty($this->urlParts['scheme'])) ? $this->urlParts['scheme'] : 'http';
		//Path will be uset if using routes otherwise it's a direct link
		switch($this->isIp || $this->isForeign)
		{
			case true:
			$url = $scheme."://".$this->urlParts['host'];
			break;
			
			default:
			switch($type)
			{
				case 'modal':
				$args = array_replace($args, ["__format" => "modal", "__contentOnly" => "1"]);
				break;
				
				case 'popup':
				$args = array_replace($args, ["__format" => "html", "__contentOnly" => "1"]);
				break;
				
				default:
				$args = array_replace($args, ["__format" => "html"]);
				break;
			}
			switch(1)
			{
				case empty($this->urlParts['scheme']):
				case empty($this->urlParts['host']):
				$url = ($this->url=='#') ? $url : \Yii::$app->urlManager->createAbsoluteUrl(array_merge([$url], $args));
				break;
				
				default:
				$url = $this->url.'?'.http_build_query($args);
				break;
			}
			break;
		}
		return $url.@$this->urlParts['fragment'];
	}
}
