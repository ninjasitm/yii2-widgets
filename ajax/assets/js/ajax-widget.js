// JavaScript Document

function AjaxWidget () {
	NitmEntity.call(this, arguments);
	var self = this;
	
	this.views = {
		roles: {
			widget : 'AjaxWidget'
		}
	}
	this.defaultInit = [
		'initWidgets',
	];
	
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

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new AjaxWidget());
});