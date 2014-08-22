function Follow(items)
{	
	var self = this;
	this.polling = {
		enabled: false
	};
	this.views = {
		containers: {
			followList: "[role='followList']",
		},
		roles: {
			followTab: "[role='followTab']",
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
	
	this.afterAction = function (xhr, elem) {
		var result = xhr.responseJSON;
		
		switch(result.action)
		{
			case 'create':
			var button = $(elem).closest('div').find("[role~='followButton']");
			button.first().data('type', 'callback');
			button.first().data('run-once', true);
			button.first().data('url', '/alerts/un-follow/'+result.id);
			button.first().data('callback', function (_result, _elem) {$nitm.module('follow').afterAction(_result, _elem)});
			button.first().off('click');
			button.last().addClass('disabled');
			$nitm.module('tools').dynamicValue(button.first());
			var removeClass = 'btn-default';
			break;
			
			case 'delete':
			var button = $(elem).parent().find("[role~='followButton']");
			button.last().removeClass('disabled');
			button.first().data('');
			button.off('click');
			var removeClass = 'btn-success';
			break;
		}
		button.first().html(result.actionHtml);
		button.each(function (key, elem) {
			$(elem).removeClass(removeClass).addClass(result.class);
		});
	}
}

$nitm.initModule('follow', new Follow());