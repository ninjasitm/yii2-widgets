'use strict';

class Polling extends NitmEntity
{
	constructor() {
		super('polling');
		this.polling = {};
	}

	initPolling(name, options, callback) {
		this.polling[name] = options;
		this.initActivity(name, options.container, callback);
	}

	getAjaxMethod(name) {
		if(this.polling[name].hasOwnProperty('method'))
			return this.polling[name].method;
		else
			return 'get';
	};

	initActivity(name, containerId, callback) {
		if(this.polling[name].enabled == true)
		{
			var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
			setInterval(() => {
				$.ajax({
					url: this.polling[name].url,
					dataType: 'json',
					method: this.getAjaxMethod(name)
				}).done((result) => {
					if((result != false)) {
						switch(typeof callback)
						{
							case 'function':
							callback(true, result, containerId);
							break;

							case 'object':
							callback.object.call(callback.method, [true, result, containerId]);
							break;
						}
					}
				});
			}, this.polling[name].interval);
		}
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Polling());
});
