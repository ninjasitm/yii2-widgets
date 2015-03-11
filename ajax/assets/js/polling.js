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
	
	this.initActivity = function(name, containerId, callback) {
		if(this.polling[name].enabled == true)
		{
			var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
			setInterval(function () {
				$.post(self.polling[name].url, 
					function (result) {
						switch((result != false))
						{
							case true:
							switch(typeof callback)
							{
								case 'function':
								callback(true, result, containerId);
								break;
								
								case 'object':
								callback.object.call(callback.method, [true, result, containerId]);
								break;
							}
							break;
						}
					}, 'json');
			}, this.polling[name].interval);
		}
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Polling());
});