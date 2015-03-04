function Notification(items)
{	
	NitmEntity.call(this, arguments);
	
	var self = this;
	this.id = 'notifications';
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

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Notification());
});