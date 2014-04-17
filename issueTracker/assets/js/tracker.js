
function IssueTracker(items)
{	
	var self = this;
	var editor;
	this.classes = {
		warning: 'bg-warning',
		success: 'bg-success',
		information: 'bg-info',
		error: 'bg-danger',
		hidden: 'message-hidden',
	};
	this.views = {
		containers: {
				replyForm: 'reply_form',
				messages: 'messages',
				message: 'message',
		}
	};
	this.elements = {
		allowEditor: ['startEditor'],
		replyActions: 'replyActions'
	};
	this.forms = {
		allowCreate: ['replyForm'],
		allowQuoting: ['quoteReply'],
		allowHiding: ['hideReply'],
		allowReplying: ['replyTo'],
		actions : {
			add: '/reply/new',
			replyTo: '/reply/to',
			hide: '/reply/hide',
		},
		inputs : {
			unique: 'issueTracker-unique',
			pour: 'issueTracker-for',
			reply_to: 'issueTracker-reply_to',
			message: 'issueTracker-message'
		},
	};
	this.actions = {
		ids: {
			hide: 'hide_message',
			reply: 'reply_to_message',
			quote: 'quote_message',
		}
	};
	this.defaultInit = [
					'initCreating',
					'initResolving',
					'initClosing',
					'initDuplicating'
				];

	this.init = function () {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method]();
			}
		});
	}
	
	this.initCreating = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowCreate.map(function (v) {
			$(container+" "+"form[role='"+v+"']").map(function() {
				$(this).off('submit');
				$(this).on('submit', function (e) {
					e.preventDefault();
					$(this).find('textarea').val(self.getEditorValue($(this).find('textarea').attr('id'), self.editor));
					self.operation(this);
				});
			})
		});
	}
	
	this.initResolving = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowResolve.map(function (v) {
			$(container+" "+"[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					$.post($(this).attr('href'), 
						function (result) { 
							self.afterResolve(result);
						}, 'json');
				});
			});
		});
	}
	
	this.initClosing = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowClose.map(function (v) {
			$(container+" "+"[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					$.post($(this).attr('href'), 
						function (result) { 
							self.afterClose(result);
						}, 'json');
				});
			});
		});
	}
	
	this.initDuplicating = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowHiding.map(function (v) {
			$(container+" "+"[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					$.post($(this).attr('href'), 
						function (result) { 
							self.afterDuplicate(result);
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
			var request = doRequest($(form).attr('action'), 
					data,
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
								
							case 'create':
							self.afterCreate(result, form);
							break;
						}
					},
					function () {
						notify('Error Could not perform IssueTracker action. Please try again', self.classes.error, false);
					}
				);
				break;
		}
	}
	
	this.afterCreate = function(result, form) {
		switch(result.success)
		{
			case true:
			ret_val = false;
			var _form = $(form);
			$('#'+_form.data('parent')).append($(result.data));
			self.initResolving('#'+result.unique_id);
			self.initClosing('#'+result.unique_id);
			self.initDuplicating('#'+result.unique_id);
			break;
			
			default:
			alert('Unable to add reply');
			break;
		}
	}
	
	this.afterClose = function (result) {
		if(result.success)
		{
			switch(result.action)
			{
				case 'close':
				getObj('#'+self.views.containers.message+result.id).addClass(self.classes.hidden);
				break;
				
				default:
				getObj('#'+self.views.containers.message+result.id).removeClass(self.classes.hidden);
				break;
			}
			getObj('#'+self.actions.ids.hide+result.id).html(result.action);
		}
	}
	
	this.afterResolve = function (result) {
		if(result.success)
		{
			switch(result.action)
			{
				case 'resolve':
				getObj('#'+self.views.containers.message+result.id).addClass(self.classes.hidden);
				break;
				
				default:
				getObj('#'+self.views.containers.message+result.id).removeClass(self.classes.hidden);
				break;
			}
			getObj('#'+self.actions.ids.hide+result.id).html(result.action);
		}
	}
	
	this.afterDuplicate = function (result) {
		if(result.success)
		{
			switch(result.action)
			{
				case 'duplicate':
				getObj('#'+self.views.containers.message+result.id).addClass(self.classes.hidden);
				break;
				
				default:
				getObj('#'+self.views.containers.message+result.id).removeClass(self.classes.hidden);
				break;
			}
			getObj('#'+self.actions.ids.hide+result.id).html(result.action);
		}
	}
}

$nitm.addOnLoadEvent(function () {
	$nitm.issueTracker = new IssueTracker();
	$nitm.issueTracker.init();
});