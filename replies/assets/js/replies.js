
function Replies(items)
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
		},
		roles: {
			replyToIndicator: "[role='replyToIndicator']",
		}
	};
	this.elements = {
		allowEditor: ['startEditor'],
		replyActions: 'replyActions'
	};
	this.forms = {
		allowCreate: ['replyForm', 'chatForm'],
		allowQuoting: ['quoteReply'],
		allowHiding: ['hideReply'],
		allowReplying: ['replyTo'],
		actions : {
			add: '/reply/new',
			replyTo: '/reply/to',
			hide: '/reply/hide',
		},
		inputs : {
			unique: 'unique',
			pour: 'replyFor',
			reply_to: 'replyTo',
			message: 'message'
		},
	};
	this.actions = {
		ids: {
			hide: 'hideMessage',
			reply: 'replyToMessage',
			quote: 'quoteMessage',
		}
	};
	this.defaultInit = [
		'initCreating',
		'initEditor',
		'initHiding',
		'initReplying',
		'initQuoting'
	];

	this.init = function () {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method]();
			}
		});
	}
	
	this.initEditor = function (containerId) {
		var containers = $nitm.getObj((containerId == undefined) ? 'body' : "[id='"+containerId+"']");
		this.elements.allowEditor.map(function (v) {
			containers.each(function(index, element) {
				var container = $(element);
				container.find("[role='"+v+"']").map(function() {
					$(this).off('click');
					$(this).on('click', function (e) {
						e.preventDefault();
						$(this).addClass('hidden');
						self.startEditor($(this).data('container'), '', this);
					});
				});
			});
		});
	}
	
	this.initCreating = function (containerId) {
		var containers = $nitm.getObj((containerId == undefined) ? 'body' : "[id='"+containerId+"']");
		this.forms.allowCreate.map(function (v) {
			containers.each(function(index, element) {
				var container = $(element);
				container.find("form[role='"+v+"']").map(function() {
					$(this).find("[data-toggle='buttons'] .btn").button();
					$(this).off('submit');
					$(this).on('submit', function (e) {
						e.preventDefault();
						$(this).find('textarea').val(self.getEditorValue($(this).find('textarea').attr('id'), self.editor));
						self.operation(this);
					});
					$(this).on('reset', function (e) {
						this.reset();
						var msgField = $(this).find("textarea");
						self.setEditorValue(msgField.get(0), '', false, self.editor);
						$(this).find(self.views.roles.replyToIndicator).html("");
					});
				})
			});
		});
	}
	
	this.initHiding = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		this.forms.allowHiding.map(function (v) {
			container.find("[role='"+v+"']").map(function() {
				$(this).off('click');
				$(this).on('click', function (e) {
					e.preventDefault();
					$.post($(this).attr('href'), 
						function (result) { 
							self.afterHide(result);
						}, 'json');
				});
			});
		});
	}
	
	this.initReplying = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		this.forms.allowReplying.map(function (v) {
			container.find("[role='"+v+"']").map(function() {
				$(this).off('click');
				$(this).on('click', function (e) {
					e.preventDefault();
					self.startEditor($(this).data('container'));
					var form = $('#'+self.views.containers.replyForm+$(this).data('parent'));
					form.find("[role~='"+self.forms.inputs.reply_to+"']").val($(this).data('reply-to'));
					var msgField = form.find("textarea");
					msgField.val('').focus();
					self.setEditorValue(msgField.get(0), '', false, self.editor);
					self.setEditorFocus(msgField.get(0), self.editor);
					container.find(self.views.roles.replyToIndicator).html("Replying to "+$(this).data('author'));
				});
			});
		});
	}
	
	this.initQuoting = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		this.forms.allowQuoting.map(function (v) {
			container.find("[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					self.startEditor($(this).data('container'));
					var form = $('#'+self.views.containers.replyForm+$(this).data('parent'));
					form.find("[role~="+self.forms.inputs.reply_to+"]").val($(this).data('reply-to'));
					var quote = {
						author: $(this).data('author'),
						parent: $(this).data('parent'),
						reply_to: $(this).data('reply_to'),
						message: $('#'+self.views.containers.message+$(this).data('reply-to')).find("div[id='messageBody"+$(this).data('reply-to')+"']").html()
					};
					var quoteString = "<blockquote>";
					quoteString += quote.author+" said:<br>"+quote.message;
					quoteString += "</blockquote><br>";
					var msgField = form.find("textarea");
					self.setEditorValue(msgField.get(0), quoteString, true, self.editor);
					self.setEditorFocus(msgField.get(0), self.editor);
					container.find(self.views.roles.replyToIndicator).html("Replying to "+$(this).data('author'));
				});
			});
		});
	}
	
	this.initChatActivity = function(containerId, url, interval) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		setInterval(function () {
			$.post(url, 
				function (result) {
					switch((result != false))
					{
						case true:
						self.chatStatus(true, result, container);
						break;
					}
				}, 'json');
		}, interval);
	}
	
	this.initChatTabs = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		$nitm.getObj(containerId).find('[data-toggle="tab"]').map(function() {
			$(this).on('click', function (e) {
				var tab = $(this);
				self.chatStatus(false, null, container);
				if(tab.parent('li').hasClass('active')){
					window.setTimeout(function(){
						$(".tab-pane").toggleClass('active', false, 500, 'linear');
						tab.parent('li').toggleClass('active', false, 500, 'linear');
					}, 1);
				}
			});
		});
	}
	
	this.chatStatus = function (update, result, container){
		container.find('[id="chat\-messages-nav"]').each(function(index, element) {
			var tab = $(element);
			switch(update)
			{
				case false:
				container.find('[id="chat\-messages\-nav"]').find('[class="badge"]').remove();
				container.find('[id="chat\-updates"]').html('');
				tab.removeClass('bg-success');
				break;
				
				default:
				var badge = tab.find('[class="badge"]');
				tab.addClass('bg-success');
				if(badge.get(0) != undefined){
					badge.html(result.count);
				}
				else {
					tab.append("<span class='badge'>"+result.count+"</span>");
				}
				container.find('[id="chat\-info\-pane"]').html(result.message);
				if(tab.parent().hasClass('active'))
				{
					self.afterCreate(result, 'form\[role="chatForm"\]');
				}
				break;
			}
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
							case 'hide':
							self.afterHide(result);
							break;
								
							case 'create':
							case 'quote':
							self.afterCreate(result, form);
							break;
						}
					},
					function () {
						$nitm.notify('Error Could not perform Replies action. Please try again', 'alert '+self.classes.error, false);
					}
				);
				break;
		}
	}
	
	this.afterCreate = function(result, form) {
		switch(result.success)
		{
			case true:
			var ret_val = false;
			var _form = $(form);
			_form.find(".empty").remove();
			$nitm.place({append:true, index:-1}, result.data, _form.data('parent'));
			self.initHiding('#'+result.unique_id);
			self.initQuoting('#'+result.unique_id);
			self.initReplying('#'+result.unique_id);
			_form.find('[role~="'+self.forms.inputs.reply_to+'"]').val('');
			self.setEditorValue(_form.find('textarea').attr('id'), '', false, self.editor);
			_form.find(self.views.roles.replyToIndicator).html("");
			break;
			
			default:
			var notifyPane = $(form).find("#alert");
			switch(notifyPane != undefined)
			{
				case true:
				$nitm.notify('Unable to add message', 'alert '+self.classes.error, "#alert").delay(5000).fadeOut();
				break;
				
				default:
				alert('Unable to add message');
				break;
			}
			break;
		}
	}
	
	this.afterHide = function (result) {
		if(result.success)
		{
			switch(result.value)
			{
				case true:
				$nitm.getObj("[id='"+self.views.containers.message+result.id+"']").each(function(index, element) {
					$(element).addClass(self.classes.hidden);
				});
				break;
				
				default:  
				$nitm.getObj("[id='"+self.views.containers.message+result.id+"']").each(function(index, element) {
					$(element).removeClass(self.classes.hidden);
				});
			}
			/*$nitm.getObj("[id='"+self.views.containers.message+result.id+"']").each(function(index, element) {
					$(element).html(result.action);
				});;*/
		}
	}
	
	this.startEditor = function (containerId, value, button) {
		var activator = $(button);
		var containers = $("[id='"+containerId+"']");
		containers.each(function(index, element) {
			var container = $(element);
			var textarea = $("<textarea id='"+container.attr('id')+"editor' role='editor' class='form-control' name='Replies[message]' rows=10>");
			var actions = container.find("[role='"+self.elements.replyActions+"']");
			actions.removeClass('hidden');
			switch(activator.data('use-modal'))
			{
				case true:
				if(container.find('.modal').get(0) == undefined)
				{
					var content = $("<div class='modal fade in' role='dialog' aria-hidden='true'>");
					var modalDialog = $("<div class='modal-dialog'>");
					var modalContent = $("<div class='modal-content'>");
					var modalBody = $("<div class='modal-body'>");
					var modalTitle = $("<div class='modal-title'>").html("<h3>Your message:</h3>");
					var modalHeader = $("<div class='modal-header'>");
					var modalClose = $('<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>');
					modalHeader.append(modalClose);
					modalBody.append(modalHeader);
					modalBody.append(modalTitle);
					modalBody.append(textarea);
					modalBody.append(actions);
					$('body').append(content.append(modalDialog.append(modalContent.append(modalBody))));
				}
				else
				{
					content = container;
				}
				content.modal({
					keyboard: true
				});
				content.on('hidden.bs.modal', function () {
					self.closeEditor(container.attr('id'));
				});
				break;
				
				default:
				textarea.insertBefore(actions);
				break;
			}
			var type = textarea.parent('form').data('editor');
			switch(type)
			{
				case 'redactor':
				$nitm.getObj("[id='"+textarea.prop('id')+"']").each(function (index, element) {
						$(element).redactor({
						air: true,
						airButtons: ['bold', 'italic', 'deleted', 'link'],
						focus: true,
						autoresize: true,
						initCallback: function(){
							if(value != undefined)
							{
								this.set(value);
							}
						},
						setCode: function(html){
							html = this.preformater(html);
							this.$editor.html(html).focus();
							this.syncCode();
						}
					});
				});
				break;
			}
		});
		self.initCreating(containerId);
	}
	
	this.closeEditor = function (containerId) {
		var containers = $("[id='"+containerId+"']");
		containers.each(function(index, element) {
			var container = $(element);
			var content = $(container.attr('id')).find('.modal-dialog');
			switch(content.get(0) == undefined) 
			{
				//if there is a modal
				case false:
				//destroy the editor
				var textarea = content.find("textarea"); 
				var type = $nitm.getObj(field).parents('form').data('editor');
				switch(type)
				{
					case 'redactor':
					$('#'+textarea.prop('id')).redactor('getObject').destroyEditor();
					break;
				}
				//container.find('.redactor_box').remove();
				//unhide the actions
				var actions = container.find("[role='"+self.elements.replyActions+"']");
				actions.addClass('hidden');
				//reset the html
				container.html(content.html());
				break;
			}
			this.elements.allowEditor.map(function (v) {
				$nitm.getObj(container.attr('id')+" "+"[role='"+v+"']").map(function() {
					$(this).removeClass('hidden');
				});
			});
		});
		//need to reinit click to reply button since not doing so casues form to submit
		self.initEditor(containerId);
	}
	
	this.setEditorValue = function (field, value, quote) {
		var type = $nitm.getObj(field).parents('form').data('editor');
		switch(type)
		{
			case 'ckeditor':
			var editor = CKEDITOR.instances[field];
			switch(quote)
			{
				case true:
				editor.editable().setHtml(value);
				break;
				
				default:
				editor.data(value);
				break;
			}
			editor.resize("100%", editor.config.height, true);
			editor.focus();
			break;
			
			case 'redactor':
			$nitm.getObj(field).redactor('getObject').set(value, false);
			break;
			
			default:
			var msgField = $nitm.getObj(field);
			msgField.val(value);
			msgField.get(0).focus();
			break;
		}
	}
	
	this.getEditorValue = function (field) {
		var ret_val = '';
		var type = $nitm.getObj(field).parent('form').data('editor');
		switch(type)
		{
			case 'ckeditor':
			var editor = CKEDITOR.instances[field];
			ret_val = editor.getData();
			break;
			
			case 'redactor':
			ret_val = $nitm.getObj(field).redactor('getObject').get();
			break;
			
			default:
			ret_val = $nitm.getObj(field).val();
			break;
		}
		return ret_val;
	}
	
	this.setEditorFocus = function (field) {
		var type = $nitm.getObj(field).parent('form').data('editor');
		switch(type)
		{
			case 'ckeditor':
			var editor = CKEDITOR.instances[field];
			editor.focus();
			break;
			
			case 'redactor':
			$nitm.getObj(field).redactor('getObject').focus();
			break;
			
			default:
			$nitm.getObj(field).get(0).focus();
			break;
		}
	}
}

$nitm.addOnLoadEvent(function () {
	$nitm.replies = new Replies();
	$nitm.replies.init();
	$nitm.moduleLoaded('replies');
});