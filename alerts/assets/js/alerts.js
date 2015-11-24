function Alerts () {

	NitmEntity.call(this, arguments);

	var self = this;
	this.id = 'alerts';
	this.defaultInit = [
		'initForms',
		'initAlerts'
	];
	this.forms = {
		roles: {
			create: 'createAlert',
			update: 'updateAlert'
		}
	};

	this.buttons = {
		create: 'newAlert',
		remove: 'removeAlert',
		disable: 'disableAlert',
	};

	this.views = {
		listFormContainer: "[role~='alertsListForm']",
		container: "[role~='alertsList']",
		itemId: "alert"
	};

	this.initAlerts = function(containerId) {
		var containerId = (containerId == undefined) ? self.views.listFormContainer : containerId;
		var container = $nitm.getObj(containerId);

		//Initializing the remove button here
		console.log("Initing alerts...");
		container.find("[role~='"+self.buttons.remove+"']").each(function () {
			$(this).on('click',function(event) {
				event.preventDefault();
				var button = this;
				if(confirm("Are you sure you want to delete this alert?")) {
					$.post($(this).data('action'), function(result) {
						self.afterDelete(result, button);
					});
				}
			});
		});
	}

	this.afterCreate = function (result, currentIndex, form) {
		if(result.success)
		{
			console.log(form);
			$(form).get(0).reset();
			$(form).find('select').each(function () {
				try {
					$('#'+this.id).select2('val', '');
				} catch (error) {
				}
			});
			if(result.data)
			{
				var element = $(result.data);
				var list = $nitm.getObj(self.views.container);
				list.prepend(element);
				element.addClass($nitm.classes.success).delay(5000).queue(function () {
					$(this).removeClass($nitm.classes.success, 5000);
				});
				self.init(self.views.itemId+result.id);
			}
			return true;
		}
		else
		{
			$nitm.notify(!result.message ? "Couldn't create new alert" : result.message, $nitm.classes.error, $(form).parents('div').find('#alert').last());
			return false;
		}
	}

	this.afterUpdate = function (result, form) {
		switch(result.success)
		{
			case true:
			$nitm.notify("Success! Your alert was updated properly", $nitm.classes.success, $(form).parents('div').find('#alert').last());
			/*var alert = $nitm.getObj(self.views.itemId+result.id);
			alert.addClass('list-group-item-success', 400, 'easeInBack').delay(1000).queue(function(next){
				 $(this).removeClass('list-group-item-success', 400, 'easeOutBack');
				 next();
			});*/
			break;

			default:
			$nitm.notify(!result.message ? "Couldnt update your alert" : result.message, $nitm.classes.success, $(form).parents('div').find('#alert').last());
			break;
		}
	}

	this.afterDelete = function (result, elem) {
		switch(result.success)
		{
			case true:
			try {
				$nitm.module('tools').removeParent(elem);
			} catch (error) {
				var container = $nitm.getObj(self.views.itemId+result.id);
				container.hide('slow').remove();
			}
			break;

			default:
			$nitm.notify("Couldn't delete alert", $nitm.classes.error, $(elem).parents('div').find('#alert').last());
			return false;
		}
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Alerts());
});
