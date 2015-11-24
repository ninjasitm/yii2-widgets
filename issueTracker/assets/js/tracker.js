
function IssueTracker(items)
{
	NitmEntity.call(this, arguments);

	var self = this;
	var editor;
	this.id = 'issue-tracker';
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
		itemId: 'issue',
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
		containerId: "[role='entityIssues']"
	};
	this.forms = {
		roles: ['createIssue', 'updateIssue'],
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
	this.actionRoles = {
		updateIssue: 'updateIssue',
		resolveIssue: 'resolveIssue',
		closeIssue: 'closeIssue',
		duplicateIssue: 'duplicateIssue',
	};
	this.defaultInit = [
		'initCreateUpdateTrigger',
		'initForms',
		'initMetaActions'
	];

	this.initCreateUpdate = function () {};

	this.initCreateUpdateTrigger = function (containerId) {
		var container = $nitm.getObj((containerId === undefined) ? 'body' : containerId).parents(self.views.containerId);
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
								self.initForms(tabContent.attr('id'));
								tab.removeClass('hidden');
								tab.find('a').tab('show');
							}, 'html');
						});
					});
					break;
				}
			});
		});
	};

	this.afterCreate = function(result, currentIndex, form) {
		var _form = $(form);
		var parent = _form.parents(self.views.containerId);
		if(result.success)
		{
			_form.get(0).reset();
			$nitm.notify("Added new issue. You can add another alert or view the newly added one", form);
			var open = parent.find(self.views.issuesOpenTab).find('.badge');
			var openValue = Number(open.html())+1;
			open.html(openValue);
		} else {
			$nitm.notify("Couldn't create new issue", self.classes.alerts.error, form);
		}
	};

	this.afterUpdate = function (result, currentIndex, form) {
		var _form = $(form);
		var parent = _form.parents(self.views.containerId);
		if(result.success)
		{
			$nitm.notify("Updated issue sucessfully", self.classes.alerts.success, form);
			parent.find(self.views.issueUpdateFormTab).addClass('hidden');
		}
		else
		{
			$nitm.notify("Couldn't update the issue", self.classes.alerts.error, form);
		}
	};

	this.afterClose = function (result, currentIndex, elem) {
		$nitm.module('entity').afterClose(result, currentIndex, elem);
		if(result.success)
		{
			var container = $nitm.getObj(self.views.containerId);
			self.updateCounter(container, self.views.issuesOpenTab, result.data === 0);
			self.updateCounter(container, self.views.issuesClosedTab, result.data === 1);
			$nitm.getObj('[id~="'+self.views.itemId+result.id+'"]').remove();
		}
	};

	this.afterResolve = function (result, currentIndex, elem) {
		$nitm.module('entity').afterResolve(result, currentIndex, elem);
		if(result.success)
		{
			var container = $nitm.getObj(self.views.containerId);
			self.updateCounter(container, self.views.issuesResolvedTab, result.data === 1);
			self.updateCounter(container, self.views.issuesUnresolvedTab, result.data === 0);
		}
	};

	this.afterDuplicate = function (result, currentIndex, actionElem) {
		if(result.success)
		{
			var container = $nitm.getObj(self.views.containerId);
			var element = $("[role~='"+self.views.statusIndicator+result.id+"']");
			element.removeClass().addClass(result.class);
			$(actionElem).attr('title', result.title);
			$(actionElem).find(':first-child').replaceWith(result.actionHtml);
			self.updateCounter(container, self.views.issuesDuplicateTab, result.data == 1);
		}
	};
	this.updateCounter = function (parent, tab, increase) {
		var counter = $nitm.getObj(parent).find(tab).find('.badge');
		var counterValue = (increase === true) ? Number(counter.html())+1 : Number(counter.html())-1;
		counter.html(counterValue);
	};
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new IssueTracker());
});
