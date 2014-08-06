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
	
	this.initForms = function (containerId) {
		var containerId = (containerId == undefined) ? self.views.listFormContainer : containerId;
		var container = $nitm.getObj(containerId);
		$.map(self.forms.roles, function(role, key) {
			container.find("form[role='"+role+"']").map(function() {
				$(this).unbind('submit');
				$(this).on('submit', function (e) {
					e.preventDefault();
					$lab1.operation(this, undefined, 'alerts');
					return false;
				});
			});
		});
	}
	
	this.initAlerts = function(containerId) {
		var containerId = (containerId == undefined) ? self.views.listFormContainer : containerId;
		var container = $nitm.getObj(containerId);
		
		//Initializing the remove button here
		container.find("[role='"+self.buttons.remove+"']").each(function () {
			$(this).off('click');
			$(this).on('click',function(event) {
				event.preventDefault();
				var button = this;
				alert("Deleting");
				$.post($(this).data('action'), function(result) {
					self.afterDelete(result, button);
				});
			});
		});
		
		//Initializing the disable button here
		container.find("[role='"+self.buttons.disable+"']").each(function () {
			$(this).off('click');
			$(this).on('click',function(event) {
				event.preventDefault();
				var button = this;
				$.post($(this).data('action'), function(result) {
					self.afterDisable(result, button);
				});
			});
		});
	}
	
	this.afterDisable = function (result, form) {
		var alert = $nitm.getObj(self.views.itemId+result.id);
		var list = alert.parents(self.views.container);
		switch(result.data)
		{
			//Item is disabled
			case 1:
			alert.addClass('disabled');
			//Move item to the end of the list
			alert.insertAfter(list.children('li:last-child'));
			alert.find(":input").not("[role='"+self.buttons.disable+"']").attr('disabled', true);
			alert.find("[role='"+self.buttons.disable+"']").removeClass('btn-warning').addClass('btn-success');	
			break;
			
			default:
			alert.removeClass('disabled');
			alert.insertAfter(list.children('li:nth-child('+result.priority+')'));
			alert.find(":input").not("[role='"+self.buttons.disable+"']").attr('disabled', false);
			alert.find("[role='"+self.buttons.disable+"']").removeClass('btn-success').addClass('btn-warning');
			break;
		}
		self.sortElems(list.attr('id'));
	}
	
	this.afterCreate = function (result, form) {
		if(result.success)
		{
			$(form).get(0).reset();
			$nitm.notify("Success! You can add another or view the newly added one", $nitm.classes.success);
			if(result.data)
			{
				var list = $(form).parents(self.views.listFormContainer).find(self.views.container).first();
				var firstDisabled = list.find(".disabled").first().index();
				var after = !firstDisabled ? (list.find("li:last-child").index())+1 : (firstDisabled - 1);
				$nitm.place({append:true, index:after}, result.data, list.attr('id'));
			}
			self.initForms(self.views.itemId+result.id);
			self.initAlerts(self.views.itemId+result.id);
			alert.addClass('list-group-item-success', 400, 'easeInBack').delay(1000).queue(function(next){
				 $(this).removeClass('list-group-item-success', 400, 'easeOutBack');
				 next();
			});
			return true;
		}
		else
		{
			$nitm.notify("Couldn't create new alert", $nitm.classes.error);
			return false;
		}
	}
	
	this.afterUpdate = function (result, form) {
		switch($lab1.afterUpdate(result, form))
		{
			case true:
			self.initForms(self.views.itemId+result.id);
			self.initAlerts(self.views.itemId+result.id);
			$nitm.tools.initVisibility(self.views.itemId+result.id);
			var alert = $nitm.getObj(self.views.itemId+result.id);
			alert.addClass('list-group-item-success', 400, 'easeInBack').delay(1000).queue(function(next){
				 $(this).removeClass('list-group-item-success', 400, 'easeOutBack');
				 next();
			});
			break;
			
			default:
			alert.addClass('list-group-item-danger', 400, 'easeInBack').delay(1000).queue(function(next){
				 $(this).removeClass('list-group-item-danger', 400, 'easeOutBack');
				 next();
			});
			break
		}
	}
	
	this.afterDelete = function (result, form) {
		switch(result.success)
		{
			case true:
			var list = $(form).parents(self.views.container);
			list.find('#'+self.views.itemId+result.id).remove();
			break;
			
			default:
			$nitm.notify("Couldn't delete alert", $nitm.classes.error);
			return false;
		}
	}
}

$nitm.initModule('alerts', new Alerts());