
function IssueTracker(items)
{	
	var self = this;
	var editor;
	this.classes = {
		items: {
			warning: 'bg-warning',
			success: 'bg-success',
			information: 'bg-info',
			error: 'bg-danger',
			hidden: 'hidden',
		},
		alerts: {
			warning: 'alert alert-warning',
			success: 'alert alert-success',
			information: 'alert alert-info',
			error: 'alert alert-danger',
			hidden: 'hidden',
		},
	};
	this.views = {
		issue: 'issue',
		issues: 'issues',
		issuesOpenTab: "[id^='open-issues-tab']",
		issuesClosedTab: "[id^='closed-issues-tab']",
		issuesDuplicateTab: "[id^='duplicate-issues-tab']",
		issuesResolvedTab: "[id^='resolved-issues-tab']",
		issuesUnresolvedTab: "[id^='unresolved-issues-tab']",
		issuesOpen: "[id^='open-issues-content']",
		issuesClosed: "[id^='closed-issues-content']",
		issueForm: "[id^='issues-form']",
		issueUpdateForm: "[id^='issues-update-form']",
		issueUpdateFormTab: "[id^='issues-update-form-tab']",
		issuesAlerts: "[id^='alert']",
		roles: {
			issues: "[role='entityIssues']"
		}
	};
	this.forms = {
		allowCreateUpdate: ['createIssue', 'updateIssue'],
		allowCreateUpdateTrigger: ['updateIssueTrigger'],
		actions : {
			create: '/issue/create',
			resolve: '/issue/resolve',
			close: '/issue/close',
			duplicate: '/flag/duplicate',
		},
		inputs : {
			unique: 'issueTracker-unique',
			pour: 'issueTracker-for',
			reply_to: 'issueTracker-reply_to',
			message: 'issueTracker-message'
		},
	};
	this.actions = {
		allowMeta: ['resolveIssue', 'closeIssue', 'duplicateIssue'],
		disabledOnClose: "disabledOnClose",
	};
	this.roles = {
		updateIssue: 'updateIssue',
		resolveIssue: 'resolveIssue',
		closeIssue: 'closeIssue',
		duplicateIssue: 'duplicateIssue',
	};
	this.defaultInit = [
		'initCreateUpdateTrigger',
		'initCreateUpdate',
		'initMeta',
	];

	this.init = function (container) {
		$.map(this.defaultInit, function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method](container);
			}
		});
	}
	
	this.initCreateUpdate = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		this.forms.allowCreateUpdate.map(function (v) {
			container.find("form[role~='"+v+"']").map(function() {
				$(this).off('submit');
				$(this).on('submit', function (e) {
					e.preventDefault();
					self.operation(this);
				});
			})
		});
	}
	
	this.initCreateUpdateTrigger = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId).parents(self.views.roles.issues);
		$.map(this.forms.allowCreateUpdateTrigger, function (v) {
			container.find("[role~='"+v+"']").map(function() {
				switch(v)
				{
					case 'updateIssueTrigger':
					$(this).off('click');
					$(this).on('click', function (e) {
						e.preventDefault();
						$.post($(this).attr('href'), function (result) {
							$nitm.module('tools').evalScripts(result, function (responseText) {
								var tab = container.find(self.views.issueUpdateFormTab);
								var tabContent = $nitm.getObj(tab.find('a').attr('href'));
								tabContent.html(responseText);
								self.initCreateUpdate(tabContent.attr('id'));
								tab.removeClass('hidden');
								tab.find('a').tab('show');
							}, 'html');
						});
					});
					break;
				}
			})
		});
	}
	
	this.initMeta = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		$.map(this.actions.allowMeta, function (v) {
			container.find("[role~='"+v+"']").map(function() {
				$(this).off('click');
				$(this).on('click', function (e) {
					e.preventDefault();
					var elem = $(this);
					$($nitm).trigger('nitm-animate-submit-start', [elem]);
					$.post($(this).attr('href'), 
						function (result) { 
							switch(result.action)
							{
								case 'close':
								self.afterClose(result, containerId);
								break;
								
								case 'resolve':
								self.afterResolve(result, containerId);
								break;
								
								case 'duplicate':
								self.afterDuplicate(result, containerId);
								break;
							}
						}, 'json');
						$($nitm).trigger('nitm-animate-submit-stop', [elem]);
				});
			});
		});
	}
	
	this.operation = function (form) {
		/*
		 * This is to support yii active form validation and prevent multiple submitssions
		 */
		try {
			$data = $(form).data('yiiActiveForm');
			if(!$data.validated)
				return false;
		} catch (error) {}
		var _form = $(form);
		var parent = _form.parents(self.views.roles.issues);
		data = _form.serializeArray();
		data.push({'name':'__format', 'value':'json'});
		data.push({'name':'getHtml', 'value':true});
		data.push({'name':'do', 'value':true});
		data.push({'name':'ajax', 'value':true});
		switch(!_form.attr('action'))
		{
			case false:
			$($nitm).trigger('nitm-animate-submit-start', [form]);
			var request = $nitm.doRequest({
				url: _form.attr('action'), 
				data: data,
				success: function (result) {
					switch(result.action)
					{		
						case 'create':
						self.afterCreate(result, form);
						break;
							
						case 'update':
						self.afterUpdate(result, form);
						break;
					}
				},
				error: function () {
					$nitm.notify('Whoops, something happened. If this keeps happening tell the admin!', form);
					$($nitm).trigger('nitm-animate-submit-stop', [form]);
				}
			});
			request.done(function () {
				$($nitm).trigger('nitm-animate-submit-stop', [form]);
			});
			break;
		}
	}
	
	this.afterCreate = function(result, form) {
		var _form = $(form);
		var parent = _form.parents(self.views.roles.issues);
		if(result.success)
		{
			_form.get(0).reset();
			$nitm.notify("Added new issue. You can add another or view the newly added one", form);
			if(result.data)
			{
				var open = parent.find(self.views.issuesOpenTab).find('.badge');
				var openValue = (result.data == 1) ? Number(open.html())-1 : Number(open.html())+1;
				open.html(openValue);
			}
		}
		else
		{
			$nitm.notify("Couldn't create new issue", self.classes.alerts.error, form);
		}
	}
	
	this.afterUpdate = function (result, form) {
		var _form = $(form);
		var parent = _form.parents(self.views.roles.issues);
		if(result.success)
		{
			$nitm.notify("Updated issue sucessfully", self.classes.alerts.success, form);
			parent.find(self.views.issueUpdateFormTab).addClass('hidden');
		}
		else
		{
			$nitm.notify("Couldn't update the issue", self.classes.alerts.error, form);
		}
	}
	
	this.afterClose = function (result, containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		if(result.success)
		{
			var element = $nitm.getObj("[id~='"+self.views.issue+result.id+"']");
			var parent = element.parents(self.views.roles.issues);
			element.find("[role~='"+self.roles.updateIssue+"']").toggleClass(self.classes.items.hidden, result.data);
			var actionElem = element.find("[role~='"+self.roles.closeIssue+"']");
			actionElem.attr('title', result.title);
			actionElem.find(':first-child').replaceWith(result.actionHtml);
			element.removeClass().addClass(result.class);
			//Move elements between sections and update counters
			self.updateCounter(container, self.views.issuesOpenTab, result.data);
			self.updateCounter(container, self.views.issuesClosedTab, !result.data);
			element.remove();
		}
	}
	
	this.afterResolve = function (result, containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		if(result.success)
		{
			var element = container.find("[id~='"+self.views.issue+result.id+"']");
			element.removeClass().addClass(result.class);
			var actionElem = element.find("[role~='"+self.roles.resolveIssue+"']");
			actionElem.attr('title', result.title);
			actionElem.find(':first-child').replaceWith(result.actionHtml);
			self.updateCounter(container, self.views.issuesResolvedTab, result.data);
			self.updateCounter(container, self.views.issuesUnresolvedTab, !result.data);
		}
	}
	
	this.afterDuplicate = function (result, containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		if(result.success)
		{
			var element = container.find("[id~='"+self.views.issue+result.id+"']");
			element.removeClass().addClass(result.class);
			var actionElem = element.find("[role~='"+self.roles.duplicateIssue+"']");
			actionElem.attr('title', result.title);
			actionElem.find(':first-child').replaceWith(result.actionHtml);
			self.updateCounter(container, self.views.issuesDuplicateTab, result.data);
		}
	}
	
	this.updateCounter = function (parent, tab, increase) {
		var counter = $nitm.getObj(parent).find(tab).find('.badge');
		var counterValue = (increase == 1) ? Number(counter.html())+1 : Number(counter.html())-1;
		counter.html(counterValue);
	}
}

$nitm.initModule('issueTracker', new IssueTracker());