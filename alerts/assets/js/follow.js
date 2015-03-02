function Follow(items)
{	
	var self = this;
	this.id = 'follow';
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
		
		if(result.success)
		{
			switch(result.action)
			{
				case 'create':
				case 'follow':
				var button = $(elem).closest('div').find("[role~='followButton']");
				button.first().data('type', 'callback');
				button.first().data('run-once', true);
				button.first().data('url', '/alerts/un-follow/'+result.id);
				button.first().data('callback', function (_result, _elem) {$nitm.module('follow').afterAction(_result, _elem)});
				button.first().data('run-times', 0);
				button.first().one('click', function (event) {
					$nitm.module('tools').dynamicValue(this);
				});
				button.last().addClass('disabled');
				var removeClass = 'btn-default';
				break;
				
				case 'delete':
				case 'un-follow':
				var button = $(elem).parent().find("[role~='followButton']");
				button.last().removeClass('disabled');
				button.first().removeClass('disabled');
				button.first().removeData('type').removeData('url').removeData('callback');
				button.first().off('change');
				button.last().off('click');
				var removeClass = 'btn-success';
				break;
			}
			button.first().html(result.actionHtml);
			button.each(function (key, elem) {
				$(elem).removeClass(removeClass).addClass(result.class);
			});
		}
		else
		{
			var button = $($(elem).data('parent'));
			try {
				button.tooltip('destroy');
			} catch (error) {}
			button.tooltip({
				title: result.message
			});
			button.tooltip('show');
			button.addClass('btn-danger');
		}
	}
}

$nitm.initModule(new Follow());