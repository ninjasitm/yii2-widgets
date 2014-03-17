
function Replies(items)
{	
	var self = this;
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
		allowAdd: ['replyForm'],
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
			hide: 'hide_message',
			reply: 'reply_to_message',
			quote: 'quote_message',
		}
	};
	this.defaultInit = [
					'initAdding',
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
			$(container+" "+"[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					$(this).addClass('hidden');
					self.startEditor($(this).data('container'));
				});
			})
		});
	}
	
	this.initAdding = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowAdd.map(function (v) {
			$(container+" "+"form[role='"+v+"']").map(function() {
				$(this).off('submit');
				$(this).on('submit', function (e) {
					e.preventDefault();
					$(this).find('textarea').val(self.getEditorValue($(this).find('textarea').attr('id'), this));
					self.operation(this);
				});
			})
		});
	}
	
	this.initHiding = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowHiding.map(function (v) {
			$(container+" "+"[role='"+v+"']").map(function() {
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
			$(container+" "+"[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					var form = $('#'+self.views.containers.replyForm+$(this).data('parent'));
					form.find("[id='"+self.forms.inputs.reply_to+"']").val($(this).data('reply-to'));
					form.find("[id='"+self.forms.inputs.message+"']").val('').focus();
					self.setEditorValue(self.forms.inputs.message+$(this).data('parent'), '', true);
					self.setEditorFocus(self.forms.inputs.message+$(this).data('parent'));
				});
			});
		});
	}
	
	this.initQuoting = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.forms.allowQuoting.map(function (v) {
			$(container+" "+"[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
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
					self.setEditorValue(self.forms.inputs.message+quote.parent, quoteString, true);
					self.setEditorFocus(self.forms.inputs.message+quote.parent);
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
							case 'hide':
							self.afterHide(result);
							break;
								
							case 'add':
							case 'quote':
							self.afterAdd(result, form);
							break;
						}
					},
					function () {
						notify('Error Could not perform Replies action. Please try again', self.classes.error, false);
					}
				);
				break;
		}
	}
	
	this.afterAdd = function(result, form) {
		switch(result.success)
		{
			case true:
			ret_val = false;
			var _form = $(form);
			$('#'+_form.data('parent')).append($(result.data));
			self.initHiding('#'+result.unique_id);
			self.initQuoting('#'+result.unique_id);
			self.initReplying('#'+result.unique_id);
			_form.find('#'+self.forms.inputs.reply_to).val('');
			self.setEditorValue(_form.find('textarea').attr('id'), '');
			break;
			
			default:
			alert('Unable to add reply');
			break;
		}
	}
	
	this.afterHide = function (result) {
		if(result.success)
		{
			switch(result.action)
			{
				case 'unhide':
				getObj('#'+self.views.containers.message+result.id).addClass(self.classes.hidden);
				break;
				
				default:
				getObj('#'+self.views.containers.message+result.id).removeClass(self.classes.hidden);
				break;
			}
			getObj('#'+self.actions.ids.hide+result.id).html(result.action);
		}
	}
	
	this.startEditor = function (containerId, value) {
		$(function()
		{
			var container = $(containerId);
			var textarea = $("<textarea id='"+containerId+"editor' role='editor' class='form-control' name='Replies[message]'>");
			var actions = container.find("[role='"+self.elements.replyActions+"']");
			actions.removeClass('hidden');
			textarea.insertBefore(actions);
			$(textarea).redactor({
				focus: true,
				autoresize: false,
				initCallback: function()
				{
					if(value)
					{
						this.set(value);
					}
				}
			});
			if(container.find('.modal').get(0) == undefined)
			{
				var content = $("<div class='modal col-md-5 col-lg-5 center-block' role='dialog' aria-hidden='true'>");
				var modalContent = $("<div class='modal-content'>");
				modalContent.append(container.html());
				container.html('').append(content.append(modalContent));
			}
			else
			{
				content = container;
			}
			content.modal({
				keyboard: false
			});
			content.on('hidden.bs.modal', function () {
				self.closeEditor(containerId);
			});
		});
	}
	
	this.closeEditor = function (containerId) {
		var container = (containerId == undefined) ? 'body' : containerId;
		var content = $(container).find('.modal-content');
		switch(content.get(0) == undefined) 
		{
			//if there is a modal
			case false:
			//destroy the editor
			content.find("textarea").redactor('destroy');
			container.html(content.html());
			break;
		}
		this.elements.allowEditor.map(function (v) {
			$(container+" "+"[role='"+v+"']").map(function() {
				$(this).removeClass('hidden');
			})
		});
	}
	
	this.setEditorValue = function (field, value, quote) {
		try {
			editor = CKEDITOR.instances[field];
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
		}
		catch (error)
		{
			var msgField = getObj(field);
			msgField.val(value);
			msgField.get(0).focus();
		}
	}
	
	this.setEditorFocus = function (e) {
		obj = getObj(e);
		switch(typeof obj)
		{
			case 'object':
			try {
				editor = CKEDITOR.instances[obj.get(0).id];
				editor.focus();
			}
			catch (error)
			{
				msgField.get(0).focus();
			}
			break;
		}
	}
	
	this.getEditorValue = function (field, _form) {
		try 
		{
			switch(typeof _form)
			{
				case 'string':
				case 'object':
				case 'number':
				try {
					var ret_val = getObj('#'+getObj(_form).attr('id')+' [name='+field+']', null, false, false).val();
				} catch(error) {};
				break;
			}
			switch((typeof ret_val == undefined) || !ret_val)
			{
				case true:
				try {
					editor = CKEDITOR.instances[field];
					var ret_val = editor.getData();
					ret_val = (!ret_val) ? getObj(field).val() : ret_val;
				} catch(error) {
					var ret_val = getObj(field).val();
				}
				if(ret_val == undefined)
				{
					try {
					} catch(error) {
					}
				}
				break;
				
				default:
				var ret_val = getObj(field).val();
				break;
			}
			return ret_val;
		} catch(error) {}
	}

}

addOnLoadEvent(function () {
	var r = new Replies();
	r.init();
});