'use strict';

class Alerts extends NitmEntity
{
	constructor() {
		super('alerts');
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
		this.defaultInit = [
			'initForms',
			'initAlerts'
		];
	}

	initAlerts(containerId) {
		let containerId = (containerId == undefined) ? this.views.listFormContainer : containerId;
		let $container = $nitm.getObj(containerId);

		//Initializing the remove button here
		$container.find("[role~='"+this.buttons.remove+"']").map((button) => {
			let $elem = $(button);
			$elem.on('click', (event) => {
				event.preventDefault();
				if(confirm("Are you sure you want to delete this alert?")) {
					$.post($elem.data('action'), (result) => {
						this.afterDelete(result, button);
					});
				}
			});
		});
	}

	afterCreate (result, form) {
		if(result.success)
		{
			let $form = $(form);
			$form.get(0).reset();
			$form.find('select').each(function () {
				try {
					$('#'+this.id).select2('val', '');
				} catch (error) {
				}
			});
			if(result.data)
			{
				let element = $(result.data);
				let list = $nitm.getObj(this.views.container);
				list.prepend(element);
				element.addClass(this.classes.success).delay(5000).queue(function () {
					$elem.removeClass(this.classes.success, 5000);
				});
				this.init(this.views.itemId+result.id);
			}
			return true;
		}
		else
		{
			this.notify(!result.message ? "Couldn't create new alert" : result.message, this.classes.error, $form.parents('div').find('#alert').last());
			return false;
		}
	}

	afterUpdate (result, form) {
		if(result.success) {
			this.notify("Success! Your alert was updated properly", this.classes.success, $form.parents('div').find('#alert').last());
			/*let alert = $nitm.getObj(this.views.itemId+result.id);
			alert.addClass('list-group-item-success', 400, 'easeInBack').delay(1000).queue(function(next){
				 $elem.removeClass('list-group-item-success', 400, 'easeOutBack');
				 next();
			});*/
		} else {
			this.notify(!result.message ? "Couldnt update your alert" : result.message, this.classes.success, $form.parents('div').find('#alert').last());
		}
	}

	afterDelete (result, elem) {
		switch(result.success)
		{
			case true:
			try {
				$nitm.module('tools').removeParent(elem);
			} catch (error) {
				let $container = $nitm.getObj(this.views.itemId+result.id);
				$container.hide('slow').remove();
			}
			break;

			default:
			this.notify("Couldn't delete alert", this.classes.error, $(elem).parents('div').find('#alert').last());
			return false;
		}
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Alerts());
});
