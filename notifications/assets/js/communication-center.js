
function CommunicationCenter(items)
{	
	var self = this;
	this.id = 'communication-center';
	this.defaultInit = [
		'initChatTabs',
	];
	
	this.initChatTabs = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		$nitm.getObj(containerId).find('[data-toggle="tab"]').map(function() {
			$(this).on('click', function (e) {
				var tab = $(this);
				self.chatStatus(false, null, container);
				if(tab.parent('li').hasClass('active')){
					window.setTimeout(function(){
						$(".tab-pane").toggleClass('active', false, 500, 'linear');
						tab.parent('li').toggleClass('active', false, 500, 'linear');
					}, 1);
				}
			});
		});
	}
}

$nitm.initModule(new CommunicationCenter());