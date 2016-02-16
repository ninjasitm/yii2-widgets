'use strict';

class IssueTracker extends NitmEntity
{
	constructor() {
		super('issue-tracker');
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
	}

	initCreateUpdate() {};

	initCreateUpdateTrigger(containerId) {
		let $container = $nitm.getObj((containerId === undefined) ? 'body' : containerId).parents(this.views.containerId);
		$.map(this.forms.allowCreateUpdateTrigger, (v) => {
			$container.find("[role~='"+v+"']").map((elem) => {
				let $elem = $(elem);
				switch(v)
				{
					case 'updateIssueTrigger':
					$elem.off('click');
					$elem.on('click', (e) => {
						e.preventDefault();
						$.post($(e.target).attr('href'), (result) => {
							$nitm.module('tools').evalScripts(result, (responseText) => {
								let $tab = $container.find(this.views.issueUpdateFormTab);
								let tabContent = $nitm.getObj(tab.find('a').attr('href'));
								tabContent.html(responseText);
								this.initForms(tabContent.attr('id'));
								$tab.removeClass('hidden');
								$tab.find('a').tab('show');
							}, 'html');
						});
					});
					break;
				}
			});
		});
	};

	afterCreate (result, form) {
		let $form = $(form);
		let $parent = $form.parents(this.views.containerId);
		if(result.success) {
			$form.get(0).reset();
			this.notify("Added new issue. You can add another alert or view the newly added one", form);
			let open = $parent.find(this.views.issuesOpenTab).find('.badge');
			let openValue = Number(open.html())+1;
			open.html(openValue);
		} else {
			this.notify("Couldn't create new issue", this.classes.alerts.error, form);
		}
	};

	afterUpdate(result, form) {
		let $form = $(form);
		let $parent = $form.parents(this.views.containerId);
		if(result.success) {
			this.notify("Updated issue sucessfully", this.classes.alerts.success, form);
			$parent.find(this.views.issueUpdateFormTab).addClass('hidden');
		} else {
			this.notify("Couldn't update the issue", this.classes.alerts.error, form);
		}
	};

	afterClose(result, elem) {
		super.afterClose(result, elem);
		if(result.success) {
			let $container = $nitm.getObj(this.views.containerId);
			this.updateCounter(container, this.views.issuesOpenTab, result.data === 0);
			this.updateCounter(container, this.views.issuesClosedTab, result.data === 1);
			$nitm.getObj('[id~="'+this.views.itemId+result.id+'"]').remove();
		}
	};

	afterResolve(result, elem) {
		super.afterResolve(result, elem);
		if(result.success) {
			let $container = $nitm.getObj(this.views.containerId);
			this.updateCounter(container, this.views.issuesResolvedTab, result.data === 1);
			this.updateCounter(container, this.views.issuesUnresolvedTab, result.data === 0);
		}
	};

	afterDuplicate(result, actionElem) {
		if(result.success) {
			let $container = $nitm.getObj(this.views.containerId);
			let element = $("[role~='"+this.views.statusIndicator+result.id+"']");
			element.removeClass().addClass(result.class);
			$(actionElem).attr('title', result.title);
			$(actionElem).find(':first-child').replaceWith(result.actionHtml);
			this.updateCounter(container, this.views.issuesDuplicateTab, result.data == 1);
		}
	};
	updateCounter(parent, tab, increase) {
		let counter = $nitm.getObj(parent).find(tab).find('.badge');
		let counterValue = (increase === true) ? Number(counter.html())+1 : Number(counter.html())-1;
		counter.html(counterValue);
	};
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new IssueTracker());
});
