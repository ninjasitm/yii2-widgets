
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
			unique: 'replies-unique',
			pour: 'replies-for',
			reply_to: 'replies-reply_to',
			message: 'replies-message'
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
	
	this.initEditor = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.elements.allowEditor.map(function (v) {
			$(container).find("[role='"+v+"']").map(function() {
				$(this).off('click');
				$(this).on('click', function (e) {
					e.preventDefault();
					$(this).addClass('hidden');
					self.startEditor($(this).data('container'), '', this);
				});
			})
		});
	}
	
	this.initCreating = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowCreate.map(function (v) {
			$(container).find("form[role='"+v+"']").map(function() {
				$(this).off('submit');
				$(this).on('submit', function (e) {
					e.preventDefault();
					$(this).find('textarea').val(self.getEditorValue($(this).find('textarea').attr('id'), self.editor));
					self.operation(this);
				});
			})
		});
	}
	
	this.initHiding = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowHiding.map(function (v) {
			$(container).find("[role='"+v+"']").map(function() {
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
	
	this.initReplying = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowReplying.map(function (v) {
			$(container).find("[role='"+v+"']").map(function() {
				$(this).off('click');
				$(this).on('click', function (e) {
					e.preventDefault();
					self.startEditor($(this).data('container'));
					var form = $('#'+self.views.containers.replyForm+$(this).data('parent'));
					form.find("[id='"+self.forms.inputs.reply_to+"']").val($(this).data('reply-to'));
					form.find("[id='"+self.forms.inputs.message+"']").val('').focus();
					self.setEditorValue(self.forms.inputs.message+$(this).data('parent'), '', false, self.editor);
					self.setEditorFocus(self.forms.inputs.message+$(this).data('parent'), self.editor);
				});
			});
		});
	}
	
	this.initQuoting = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowQuoting.map(function (v) {
			$(container).find("[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					self.startEditor($(this).data('container'));
					var form = $('#'+self.views.containers.replyForm+$(this).data('parent'));
					form.find("[id="+self.forms.inputs.reply_to+"]").val($(this).data('reply-to'));
					var quote = {
						author: $(this).data('author'),
						parent: $(this).data('parent'),
						reply_to: $(this).data('reply_to'),
						message: $('#'+self.views.containers.message+$(this).data('reply-to')).find("div[id='messageBody"+$(this).data('reply-to')+"']").html()
					};
					var quoteString = "<blockquote>";
					quoteString += quote.author+" said:<br>"+quote.message;
					quoteString += "</blockquote><br>";
					self.setEditorValue(self.forms.inputs.message+quote.parent, quoteString, true, self.editor);
					self.setEditorFocus(self.forms.inputs.message+quote.parent, self.editor);
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
			ret_val = false;
			var _form = $(form);
			$nitm.place({append:true, index:0}, result.data, _form.data('parent'));
			self.initHiding('#'+result.unique_id);
			self.initQuoting('#'+result.unique_id);
			self.initReplying('#'+result.unique_id);
			_form.find('#'+self.forms.inputs.reply_to).val('');
			self.setEditorValue(_form.find('textarea').attr('id'), '', false, self.editor);
			break;
			
			default:
			alert('Unable to add reply');
			break;
		}
	}
	
	this.afterHide = function (result) {
		if(result.success)
		{
			switch(result.value)
			{
				case true:
				$nitm.getObj('#'+self.views.containers.message+result.id).addClass(self.classes.hidden);
				break;
				
				default:  
				$nitm.getObj('#'+self.views.containers.message+result.id).removeClass(self.classes.hidden);
				break;
			}
			$nitm.getObj('#'+self.actions.ids.hide+result.id).html(result.action);
		}
	}
	
	this.startEditor = function (containerId, value, button) {
		var activator = $(button);
		var container = $('#'+containerId);
		var textarea = $("<textarea id='"+containerId+"editor' role='editor' class='form-control' name='Replies[message]' rows=10>");
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
				self.closeEditor(containerId);
			});
			break;
			
			default:
			textarea.insertBefore(actions);
			break;
		}
		$('#'+textarea.prop('id')).redactor({
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
		self.initCreating('#'+containerId);
	}
	
	this.closeEditor = function (containerId) {
		var containerId = (containerId == undefined) ? 'body' : '#'+containerId;
		var content = $(containerId).find('.modal-dialog');
		switch(content.get(0) == undefined) 
		{
			//if there is a modal
			case false:
			//destroy the editor
			var container = $(containerId);
			var textarea = content.find("textarea"); 
			$('#'+textarea.prop('id')).redactor('getObject').destroyEditor();
			//container.find('.redactor_box').remove();
			//unhide the actions
			var actions = container.find("[role='"+self.elements.replyActions+"']");
			actions.addClass('hidden');
			//reset the html
			container.html(content.html());
			break;
		}
		this.elements.allowEditor.map(function (v) {
			$(containerId+" "+"[role='"+v+"']").map(function() {
				$(this).removeClass('hidden');
			});
		});
		//need to reinit click to reply button since not doing so casues form to submit
		self.initEditor(containerId);
	}
	
	this.setEditorValue = function (field, value, quote, type) {
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
			ret_val = $nitm.getObj(field).redactor('getObject').set(value, false);
			break;
			
			default:
			var msgField = $nitm.getObj(field);
			msgField.val(value);
			msgField.get(0).focus();
			break;
		}
	}
	
	this.getEditorValue = function (field, type) {
		var ret_val = '';
		switch(type)
		{
			case 'ckeditor':
			var editor = CKEDITOR.instances[field];
			ret_val = editor.getData();
			break;
			
			case 'redactor':
			ret_val = $('#'+$nitm.getObj(field).attr('id')).redactor('getObject').get();
			break;
			
			default:
			ret_val = $nitm.getObj(field).val();
			break;
		}
		return ret_val;
	}
	
	this.setEditorFocus = function (field) {
		switch(type)
		{
			case 'ckeditor':
			var editor = CKEDITOR.instances[field];
			editor.focus();
			break;
			
			case 'redactor':
			$nitm.getObj(field).redactor('focus');
			break;
			
			default:
			ret_val = $nitm.getObj(field).get(0).focus();
			break;
		}
	}
}

$nitm.replies = new Replies();
$nitm.replies.editor = 'redactor';
$nitm.replies.init();