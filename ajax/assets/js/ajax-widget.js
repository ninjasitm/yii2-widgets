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
		var $elem = $(elem);
		$elem.scrollable({direction: 'vertical', in: function () {}});
		var position = $elem.scrollable('position');
		switch(position.inside)
		{
			case true:
			self.now(elem);
			break;
			
			default:
			$elem.on('scrollin', function () {
				console.log("Scrolled In...");
				self.now(this)
			})
			.on('scrollout', function () {
				$(this).off('scrollin');
				$(this).off('scrollout');
			})
			.scrollable();
			console.log($elem.onscroll);
			break;
		}
	}
	
	this.now = function (elem) {
		var $elem = $(elem);
		$(document).ready(function () {
			$elem.load($elem.data('url'), $elem.data('query-params'));
		});
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new AjaxWidget());
});