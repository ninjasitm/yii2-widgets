
function Replies(items)
{
	NitmEntity.call(this, arguments);
	
	var self = this;
	var editor;
	this.id = 'replies';
	this.polling = {
		enabled: false
	};
	this.classes = {
		warning: 'bg-warning',
		success: 'bg-success',
		information: 'bg-info',
		error: 'bg-danger',
		hidden: 'message-hidden',
	};
	this.views = {
		containers: {
			replyForm: 'reply-form',
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
			hide: 'hide-message',
			reply: 'reply-to-message',
			quote: 'quote-message',
		}
	};
	this.defaultInit = [
		'initEditor',
		'initCreating'
	];
	
	this.initEditor = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : "[id='"+containerId+"']");
		this.elements.allowEditor.map(function (v) {
			container.find("[role='"+v+"']").map(function() {
				$(this).off('click');
				$(this).on('click', function (e) {
					e.preventDefault();
					$(this).addClass('hidden');
					self.startEditor($(this).data('container'), '', this);
				});
			});
		});
	}
	
	this.resetForm = function (event) {
		event.target.reset();
		self.setEditorValue($(event.target).find("textarea").get(0), '', false, self.editor);
		$(event.target).find(self.views.roles.replyToIndicator).html("");
	}
	
	this.reply = function (event) {
		event.preventDefault();
		var $form = $(event.target);		
		$form.find('textarea').val(self.getEditorValue($form.find('textarea').attr('id'), self.editor));
		self.operation(event.target);
		return false;
	}
	
	this.initCreating = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : "[id='"+containerId+"']");
		this.forms.allowCreate.map(function (v) {
			container.find("form[role='"+v+"']").map(function() {
				$(this).off('submit');
				$(this).on('submit', self.reply);
				$(this).on('reset', self.resetForm);
			})
		});
	}
	
	this.initHiding = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		this.forms.allowHiding.map(function (v) {
			container.find("[role='"+v+"']").map(function() {
				$(this).off('click');
				$(this).on('click', function (e) {
					e.preventDefault();
					self.hide(this);
				});
			});
		});
	}
	
	this.hide = function (event) {
		event.preventDefault();
		var elem = event.target;
		var parent = $(elem).parents("[class~='message']:first");
		$.post($(elem).attr('href'), 
			function (result) {
				self.afterHide(result, parent.get(0), elem);
			}, 'json');
	}
	
	this.initReplying = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		this.forms.allowReplying.map(function (v) {
			container.find("[role='"+v+"']").map(function() {
				$(this).off('click');
				$(this).on('click', function (e) {
					self.replyTo(e);
				});
			});
		});
	}
	
	this.replyTo = function (event)
	{
		event.preventDefault();
		var $elem = $(event.target);
		self.startEditor(!$elem.data('container') ? $elem.data('parent') : $elem.data('container'));
		var form = $($elem.data('parent'));
		form.find("[role~='"+self.forms.inputs.reply_to+"']").val($elem.data('reply-to'));
		var msgField = form.find("textarea");
		msgField.val('').focus();
		self.setEditorValue(msgField.get(0), '', false, self.editor);
		$(self.views.roles.replyToIndicator).html("Replying to "+$elem.data('author'));
	}
	
	this.initQuoting = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		this.forms.allowQuoting.map(function (v) {
			container.find("[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					self.quote(e);
				});
			});
		});
	}
	
	this.quote = function (event){
		event.preventDefault();
		var $elem = $(event.target);
		self.startEditor(!$elem.data('container') ? $elem.data('parent') : $elem.data('container'));
		var form = $($elem.data('parent'));
		form.find("[role~="+self.forms.inputs.reply_to+"]").val($elem.data('reply-to'));
		var quote = {
			author: $elem.data('author'),
			parent: $elem.data('parent'),
			reply_to: $elem.data('reply-to-id'),
			message: $($elem.data('reply-to-message')).html()
		};
		var quoteString = "<blockquote>";
		quoteString += quote.author+" said:<br>"+quote.message;
		quoteString += "</blockquote><br>";
		var msgField = form.find("textarea");
		self.setEditorValue(msgField.get(0), quoteString, true, self.editor);
		$(self.views.roles.replyToIndicator).html("Replying to "+$elem.data('author'));
	}
	
	this.chatStatus = function (update, result, container) {
		$nitm.getObj(container).find('[id="chat\-messages-nav"]').each(function(index, element) {
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
	
	this.afterCreate = function(result, index, form, element) {
		switch(result.success)
		{
			case true:
			var ret_val = false;
			var $form = $(form);
			$form.find(".empty").remove();
			$nitm.place({append:true, index:-1}, result.data, $form.data('parent'));
			//self.initHiding('#'+result.unique_id);
			//self.initQuoting('#'+result.unique_id);
			//self.initReplying('#'+result.unique_id);
			$form.find('[role~="'+self.forms.inputs.reply_to+'"]').val('');
			self.setEditorValue($form.find('textarea').get(0), '', false, self.editor);
			$form.find(self.views.roles.replyToIndicator).html("");
			break;
			
			default:
			$nitm.notify('Unable to add message', 'alert '+self.classes.error, form);
			break;
		}
	}
	
	this.afterHide = function (result, element, activator) {
		if(result.success)
		{
			switch(result.value)
			{
				case true:
				$(element).addClass(self.classes.hidden);
				break;
				
				default:  
				$(element).removeClass(self.classes.hidden);
				break;
			}
			$(activator).text(result.action);
			/*$nitm.getObj("[id='"+self.views.containers.message+result.id+"']").each(function(index, element) {
					$(element).html(result.action);
				});;*/
		}
	}
	
	this.startEditor = function (containerId, value, button) {
		var activator = $(button);
		var containers = $nitm.getObj(containerId);
		containers.each(function(index, element) {
			var container = $(element);
			var textareaId = container.find('textarea').attr('id');
			var textarea = $('#'+textareaId);
			if(!textarea.get(0))
			{
				textarea = $("<textarea id='"+textareaId+"' role='editor' class='form-control' name='Replies[message]' rows=10>");
				var actions = container.find("[role='"+self.elements.replyActions+"']");
				actions.removeClass('hidden');
				switch(activator.data('use-modal'))
				{
					case true:
					if(container.find('.modal').get(0) == undefined)
					{
						$nitm.dialog(textarea, {
							title: "<h3>Your message:</h3>",
							actions: actions
						});
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
			}
			var type = textarea.parents('form').data('editor');
			switch(type)
			{
				case 'redactor':
				$nitm.getObj("#"+textarea.attr('id')).each(function () {
					var textarea = $(this);
					try {
						var instance = textarea.redactor('core.getObject');
						instance.code.sync();
						instance.focus.setEnd();
					} catch (error) {
						textarea.redactor({
							air: false,
							focus: true,
							autoresize: true,
							initCallback: function(value){
								if(value != undefined) {
									this.insert.set(value);
								}
							},
							setCode: function(html){
								html = this.preformater(html);
								this.$editor.html(html).focus();
								this.syncCode();
							}
						});
					}
				});
				break;
			}
			textarea.parents('form').find("[role='startEditor']").remove();
		});
		self.initCreating(containerId);
	}
	
	this.closeEditor = function (containerId) {
		var containers = $(containerId);
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
					$('#'+textarea.prop('id')).redactor('core.destroy');
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
			break;
			
			case 'redactor':
			$nitm.getObj(field).redactor('code.set', value, false);
			break;
			
			default:
			var msgField = $nitm.getObj(field);
			msgField.val(value);
			break;
		}
		this.setEditorFocus(field);
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
			ret_val = $nitm.getObj(field).redactor('get');
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
			$nitm.getObj(field).redactor('focus.setEnd');
			break;
			
			default:
			$nitm.getObj(field).get(0).focus();
			break;
		}
	}
	
	this.charsLeft = function(field, cntfield, maxlimit) 
	{
		field = this.getObj(field).get(0);
		cntfield = this.getObj(cntfield).get(0);
		switch(field.value.length >= maxlimit+1)
		{
			case true:
			field.value = field.value.substring(0, maxlimit);
			cntfield.innerHTML = maxlimit - field.value.length;
			alert("You've maxed out the "+maxlimit+" character limit\n\nPlease shorten your message. :-).");
			break;
				
			default:
			cntfield.innerHTML = maxlimit - field.value.length;
			break;
		}
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Replies());
});