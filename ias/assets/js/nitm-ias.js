/**
 * NITM Javascript Tools
 * Custom IAS handler, as Yii2-scroll-pager creates too many IAS obejcts
 * © NITM 2014
 */

function NitmIas ()
{
	var self = this;
	this.defaultInit = [
		'initIas'
	];
	
	this.init = function (containerId) {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
				self[method](containerId);
		});
	}
	this.initIas = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		container.find("[role~='iasContainer']").each(function() {
			var element = $nitm.getObj(this);
			var data = element.data('ias');
			var options = data.ias;
			delete data.ias;
			
			var defaultOptions = {
				pagination: options.container+' .pagination',
				next: '.next a'
			}
			options = $.extend(options, defaultOptions);
			var overFlowContainer = data.hasOwnProperty('container') ? $(data.container) : $;
			var ias = overFlowContainer.ias(options);
			for(var extension in data.extensions)
			{
				switch(data.extensions.hasOwnProperty(extension))
				{
					case true:
					ias.extension( new window[extension](data.extensions[extension]));
					break;
				}
			}
			for(var event in data.events)
			{
				try {
					eval("var func = "+data.events[event]);
					ias.on(event, func);
				} catch (error) {
				};
			}
		});
	}
}

$nitm.initModule('nitm-ias', new NitmIas());