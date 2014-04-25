
function IssueTracker(items)
{	
	var self = this;
	var editor;
	this.classes = {
		warning: 'bg-warning',
		success: 'bg-success',
		information: 'bg-info',
		error: 'bg-danger',
		hidden: 'hidden',
	};
	this.views = {
		issue: 'issue',
		issues: 'issues',
		issueForm: 'issues-form',
	};
	this.forms = {
		allowCreateUpdate: ['createIssue', 'updateIssue'],
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
	};
	this.roles = {
		updateIssue: 'updateIssue',
		resolveIssue: 'resolveIssue',
		closeIssue: 'closeIssue',
		duplicateIssue: 'duplicateIssue',
	};
	this.defaultInit = [
		'initCreateUpdate',
		'initMeta',
	];

	this.init = function () {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method]();
			}
		});
	}
	
	this.initCreateUpdate = function (container) {
		var container = $((container == undefined) ? 'body' : container);
		this.forms.allowCreateUpdate.map(function (v) {
			container.find("form[role='"+v+"']").map(function() {
				$(this).off('submit');
				$(this).on('submit', function (e) {
					e.preventDefault();
					self.operation(this);
				});
			})
		});
	}
	
	this.initMeta = function (container) {
		var container = $((container == undefined) ? 'body' : container);
		this.actions.allowMeta.map(function (v) {
			container.find("[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					$.post($(this).attr('href'), 
						function (result) { 
							switch(result.action)
							{
								case 'close':
								self.afterClose(result);
								break;
								
								case 'resolve':
								self.afterResolve(result);
								break;
								
								case 'duplicate':
								self.afterDuplicate(result);
								break;
							}
						}, 'json');
				});
			});
		});
	}
	
	this.operation = function (form) {
		data = $(form).serializeArray();
		data.push({'name':'__format', 'value':'json'});
		data.push({'name':'getHtml', 'value':true});
		data.push({'name':'do', 'value':true});
		data.push({'name':'ajax', 'value':true});
		switch(!$(form).attr('action'))
		{
			case false:
			var request = $nitm.doRequest($(form).attr('action'), 
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
						}
					},
					function () {
						$nitm.notify('Error Could not perform IssueTracker action. Please try again', self.classes.error, false);
					}
				);
				break;
		}
	}
	
	this.afterCreate = function(result, form) {
		if(result.success)
		{
			$(form).get(0).reset();
			$nitm.notify("Added new issue. You can add another or view the newly added one", $nitm.classes.success, self.views.issueForm);
			if(result.data)
			{
				$nitm.place({append:true, index:0}, result.data, self.views.issues);
			}
		}
		else
		{
			$nitm.notify("Couldn't create new issue", $nitm.classes.error, self.views.issueForm);
		}
	}
	
	this.afterUpdate = function (result) {
		if(result.success)
		{
			$nitm.notify("Updated issue sucessfully", $nitm.classes.success, self.views.issueForm);
			if(result.data)
			{
				$nitm.getObj('#'+self.views.issue+result.id).replaceWith(result.data);
			}
		}
		else
		{
			$nitm.notify("Couldn't update the issue", $nitm.classes.error, self.views.issueForm);
		}
	}
	
	this.afterClose = function (result) {
		if(result.success)
		{
			var container = $nitm.getObj('#'+self.views.issue+result.id);
			container.find("[role='"+self.roles.updateIssue+"']").toggleClass(self.classes.hidden, result.data);
			var actionElem = container.find("[role='"+self.roles.closeIssue+"']");
			actionElem.attr('title', result.title);
			actionElem.find(':first-child').replaceWith(result.actionHtml);
			container.removeClass().addClass(result.class);
		}
	}
	
	this.afterResolve = function (result) {
		if(result.success)
		{
			var container = $nitm.getObj('#'+self.views.issue+result.id);
			container.removeClass().addClass(result.class);
			var actionElem = container.find("[role='"+self.roles.resolveIssue+"']");
			actionElem.attr('title', result.title);
			actionElem.find(':first-child').replaceWith(result.actionHtml);
		}
	}
	
	this.afterDuplicate = function (result) {
		if(result.success)
		{
			var container = $nitm.getObj('#'+self.views.issue+result.id);
			container.removeClass().addClass(result.class);
			var actionElem = container.find("[role='"+self.roles.duplicateIssue+"']");
			actionElem.attr('title', result.title);
			actionElem.find(':first-child').replaceWith(result.actionHtml);
		}
	}
}

$nitm.addOnLoadEvent(function () {
	$nitm.issueTracker = new IssueTracker();
	$nitm.issueTracker.init();
});