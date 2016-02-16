'use strict';

class CommunicationCenter extends NitmEntity
{
	constructor() {
		super('communication-center');
		this.defaultInit = [
			'initChatTabs',
		];
	}

	initChatTabs(containerId) {
		var $container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		$container.find('[data-toggle="tab"]').map((elem) => {
			let $elem = $(elem);
			$elem.on('click', function (e) {
				this.chatStatus(false, null, $container);
				if($elem.parent('li').hasClass('active')){
					window.setTimeout(function(){
						$(".tab-pane").toggleClass('active', false, 500, 'linear');
						$elem.parent('li').toggleClass('active', false, 500, 'linear');
					}, 1);
				}
			});
		});
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new CommunicationCenter());
});
