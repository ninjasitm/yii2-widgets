// JavaScript Document

function AjaxWidget () {
	var self = this;
	
	this.views = {
		roles: {
			widget : 'AjaxWidget'
		}
	}
	this.defaultInit = [
		'initWidgets',
	];

	this.init = function (container) {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method](container);
			}
		});
	}
	
	this.initWidgets = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		container.find("[role~='"+self.views.roles.widget+"']").map(function() {
			var $elem = $(this);
			switch($elem.data('type'))
			{
				case 'inViewport':
				self.inViewPort(this);
				break;
				
				default:
				self.now(this);
				break;
			}
		});
	}
	
	this.inViewPort = function (elem) {
		var element = $(elem);
		element.scrollable({direction: 'vertical', in: function () {}});
		var position = element.scrollable('position');
		switch(position.inside)
		{
			case true:
			element.load(element.data('url'), element.data('query-params'));
			break;
			
			default:
			element.scrollable({
				in: function () {element.load(element.data('url'), element.data('query-params'))},
				out: function () {element.off('scrollin');element.off('scrollout');}
			});
			break;
		}
	}
	
	this.now = function (elem) {
		var $element = $(elem);
		$(document).ready(function () {
			$element.load($element.data('url'), $element.data('query-params'));
		});
	}
}

$nitm.onModuleLoad('lab1', function () {
	$nitm.initModule('lab1:ajax-widget', new AjaxWidget());
});