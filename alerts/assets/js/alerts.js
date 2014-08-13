function Alerts () {
	var self = this;
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
	
	this.init = function (containerId) {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
				self[method](containerId);
		});
	}
	
	this.initForms = function (containerId) {
		var containerId = (containerId == undefined) ? self.views.listFormContainer : containerId;
		var container = $nitm.getObj(containerId);
		$.map(self.forms.roles, function(role, key) {
			container.find("form[role='"+role+"']").map(function() {
				$(this).off('submit');
				$(this).on('submit', function (e) {
					e.preventDefault();
					self.operation(this, undefined, 'alerts');
					return false;
				});
			});
		});
	}
	
	this.initAlerts = function(containerId) {
		var containerId = (containerId == undefined) ? self.views.listFormContainer : containerId;
		var container = $nitm.getObj(containerId);
		
		//Initializing the remove button here
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
	
	this.operation = function (form) {
		/*
		 * This is to support yii active form validation and prevent multiple submitssions
		 */
		/*try {
			$data = $(form).data('yiiActiveForm');
			if(!$data.validated)
				return false;
		} catch (error) {}*/
		var _form = $(form);
		data = _form.serializeArray();
		data.push({'name':'__format', 'value':'json'});
		data.push({'name':'getHtml', 'value':true});
		data.push({'name':'do', 'value':true});
		data.push({'name':'ajax', 'value':true});
		switch(!_form.attr('action'))
		{
			case false:
			$($nitm).trigger('nitm-animate-submit-start', [form]);
			var request = $nitm.doRequest(_form.attr('action'), 
				data,
				function (result) {
					switch(result.action)
					{		
						case 'create':
						self.afterCreate(result, form);
						break;
							
						case 'update':
						self.afterUpdate(result, form);
						break;
							
						case 'delete':
						self.afterDelete(result, form);
						break;
					}
				},
				function () {
					$nitm.notify('Error Could not perform Alert action. Please try again', $nitm.classes.error, '#'+parent.attr('id')+' '+self.views.issuesAlerts);
				}
			);
			request.done(function () {
				$($nitm).trigger('nitm-animate-submit-stop', [form]);
			});
			break;
		}
	}
	
	this.afterCreate = function (result, form) {
		if(result.success)
		{
			$(form).get(0).reset();
			$(form).find('select').each(function () {
				try {
					$('#'+this.id).select2('val', '');
				} catch (error) {
				}
			});
			$nitm.notify("Success! You can add another or view the newly added one", $nitm.classes.success, $(form).parents('div').find('#alert').last());
			if(result.data)
			{
				var element = $(result.data);
				var list = $nitm.getObj(self.views.container);
				list.prepend(element);
				element.addClass($nitm.classes.success).delay(5000).queue(function () {
					$(this).removeClass($nitm.classes.success, 5000);
				});
			}
			self.init(self.views.itemId+result.id);
			/*$nitm.getObj(self.views.itemId+result.id).addClass('list-group-item-success', 400, 'easeInBack').delay(1000).queue(function(next){
				 $(this).removeClass('list-group-item-success', 400, 'easeOutBack');
				 next();
			});*/
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
			break
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
$nitm.addOnLoadEvent(function () {
	$nitm.initModule('alerts', new Alerts());
});