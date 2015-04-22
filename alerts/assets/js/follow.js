function Follow(items)
{	
	NitmEntity.call(this, arguments);
	
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
	
	this.afterAction = function (result, elem) {
		
		if(typeof result != 'object')
			return $nitm.indicate(result, elem, 'btn-danger');
		
		if(result.success)
		{
			switch(result.action)
			{
				case 'create':
				case 'follow':
				var button = $(elem).closest('div').find("[role~='followButton']");
				button.first().data('type', 'callback');
				button.first().data('url', '/alerts/un-follow/'+result.id);
				button.first().data('callback', function (_result, _elem) {$nitm.module('follow').afterAction(_result, _elem)});
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
			$nitm.indicate(result.message, elem);
		}
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Follow());
});