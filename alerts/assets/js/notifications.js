function Notification(items)
{	
	var self = this;
	this.polling = {
		enabled: false
	};
	this.views = {
		containers: {
			notificationList: "[role='notificationList']",
		},
		roles: {
			notificationTab: "[role='notificationTab']",
		}
	};
	this.defaultInit = [
	];

	this.init = function (containerId) {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method](containerId);
			}
		});
	}
	
	this.initPolling = function (options) {
		self.polling = options;
		self.initActivity(options.container);
	}
	
	this.initActivity = function(containerId) {
		if(self.polling.enabled == true)
		{
			var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
			setInterval(function () {
				$.post(self.polling.url, 
					function (result) {
						switch((result != false))
						{
							case true:
							self.notificationStatus(true, result, containerId);
							break;
						}
					}, 'json');
			}, self.polling.interval);
		}
	}
	
	this.notificationStatus = function (update, result, container){
		container.find(self.views.notificationTab).each(function(index, element) {
			var tab = $(element);
			switch(update)
			{
				case false:
				tab.find('[class="badge"]').html(0);
				tab.removeClass('bg-success');
				break;
				
				default:
				var badge = tab.find('[class="badge"]');
				tab.addClass('bg-success');
				if(badge.get(0) != undefined){
					badge.html(result.count);
				}
				else {
					tab.append("<span class='badge'>"+result.count+"</span>");
				}
				if(tab.parent().hasClass('active'))
				{
					tab.parent().prepend(result.data);
				}
				break;
			}
		});
	}
}

$nitm.initModule('notifications', new Notification());