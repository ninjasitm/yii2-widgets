'use strict';

// JavaScript Document

class AjaxWidget extends NitmEntity
{
	constructor() {
		super("ajax-widget");
		this.views = {
			roles: {
				widget : 'AjaxWidget'
			}
		}
		this.defaultInit = [
			'initWidgets',
		];
	}

	initWidgets(containerId) {
		var $container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		$container.find("[role~='"+this.views.roles.widget+"']").map((elem) => {
			var $elem = $(elem);
			switch($elem.data('type'))
			{
				case 'inViewport':
				this.inViewPort(this);
				break;

				default:
				this.now(this);
				break;
			}
		});
	}

	inViewPort(elem) {
		var $elem = $(elem);
		$elem.scrollable({direction: 'vertical', in: function () {}});
		var position = $elem.scrollable('position');
		switch(position.inside)
		{
			case true:
			this.now(elem);
			break;

			default:
			$elem.on('scrollin', function () {
				console.log("Scrolled In...");
				this.now(this)
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

	now(elem) {
		var $elem = $(elem);
		$(document).ready(function () {
			$elem.load($elem.data('url'), $elem.data('query-params'));
		});
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new AjaxWidget());
});
