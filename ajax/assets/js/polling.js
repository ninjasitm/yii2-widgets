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

	getValue(name, key, defaultValue) {
		if(this.polling[name].hasOwnProperty(key))
			return this.polling[name][key];
		else
			return defaultValue;
	};

	initActivity(name, containerId, callback) {
		if(this.polling[name].enabled == true)
		{
			var container = $nitm.getObj(containerId || 'body');
			this.poll(name, containerId, callback);
			setInterval(() => {
				this.poll(name, containerId, callback);
			}, this.polling[name].interval);
		}
	}

	poll(name, containerId, callback) {
		$.ajax({
			url: this.polling[name].url,
			dataType: this.getValue(name, 'dataType', 'json'),
			method: this.getValue(name, 'method', 'get')
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
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Polling());
});
