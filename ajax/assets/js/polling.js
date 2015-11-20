function Polling()
{
	NitmEntity.call(this, arguments);
	var self = this;
	this.id = 'polling';
	this.polling = {};

	this.initPolling = function (name, options, callback) {
		this.polling[name] = options;
		this.initActivity(name, options.container, callback);
	}

	this.getAjaxMethod = function(name) {
		if(this.polling[name].hasOwnProperty('method'))
			return this.polling[name].method;
		else
			return 'get';
	};

	this.initActivity = function(name, containerId, callback) {
		if(this.polling[name].enabled == true)
		{
			var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
			setInterval(function () {
				$.ajax({
					url: self.polling[name].url,
					dataType: 'json',
					method: self.getAjaxMethod(name)
				}).done(function (result) {
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
