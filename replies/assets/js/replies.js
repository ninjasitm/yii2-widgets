'use strict';

class Replies extends NitmEntity
{
	constructor() {
		super('replies');
		this.polling = {
			enabled: false
		};
		this.elements = {
			allowEditor: ['startEditor'],
			replyActions: 'replyActions'
		};
		this.actions = {
			ids: {
				hide: 'hide-message',
				reply: 'reply-to-message',
				quote: 'quote-message',
			}
		};

		//Extensible
		Object.assign(this.classes, {
			hidden: 'message-hidden',
		});

		Object.assign(this.views, {
			containers: {
				replyForm: 'reply-form',
				messages: 'messages',
				message: 'message',
			},
			roles: {
				replyToIndicator: "[role='replyToIndicator']",
			}
		});
		Object.assign(this.forms, {
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
		});

		this.defaultInit = [
			'initEditor',
			'initIas',
			'initCreating'
		];
	}

	initIas(containerId) {
		$nitm.oml('nitm-ias', function (module) {
			module.initIas(containerId || '#messages');
		});
	}

	initEditor(containerId) {
		let $container = $nitm.getObj((containerId === undefined) ? 'body' : "[id='"+containerId+"']");
		this.elements.allowEditor.map((v) => {
			$container.find("[role='"+v+"']").map((elem) => {
				$(elem).off('click').on('click', function (e) {
					e.preventDefault();
					$(e.target).addClass('hidden');
					this.startEditor($elem.data('container'), '', e.target);
				});
			});
		});
	};

	initCreating(containerId) {
		let $container = $nitm.getObj((containerId === undefined) ? 'body' : "[id='"+containerId+"']");
		this.forms.allowCreate.map((v) => {
			$container.find("form[role='"+v+"']").map((elem) => {
				$(elem).off('submit').on('submit', this.reply).on('reset', this.resetForm);
			});
		});
	};

	initHiding(containerId) {
		let $container = $nitm.getObj((containerId === undefined) ? 'body' : containerId);
		this.forms.allowHiding.map((v) => {
			$container.find("[role='"+v+"']").map((elem) => {
				$(elem).off('click').on('click', function (e) {
					e.preventDefault();
					this.hide(e.target);
				});
			});
		});
	};

	initReplying(containerId) {
		let $container = $nitm.getObj((containerId === undefined) ? 'body' : containerId);
		this.forms.allowReplying.map((v) => {
			$container.find("[role='"+v+"']").map((elem) => {
				$(elem).off('click').on('click', function (e) {
					this.replyTo(e);
				});
			});
		});
	};

	initQuoting(containerId) {
		let $container = $nitm.getObj((containerId === undefined) ? 'body' : containerId);
		this.forms.allowQuoting.map((v) => {
			$container.find("[role='"+v+"']").map((elem) => {
				$(elem).on('click', function (e) {
					this.quote(e);
				});
			});
		});
	};

	resetForm(event) {
		event.target.reset();
		this.setEditorValue($(event.target).find("textarea").get(0), '', false, this.editor);
		$(event.target).find(this.views.roles.replyToIndicator).html("");
	};

	reply(event) {
		event.preventDefault();
		let $form = $(event.target);
		$form.find('textarea').val(this.getEditorValue($form.find('textarea').attr('id'), this.editor));
		this.operation(event.target);
		return false;
	};

	hide(event) {
		event.preventDefault();
		let elem = event.target;
		let parent = $(elem).parents("[class~='message']:first");
		$.post($(elem).attr('href'), (result) => {
			this.afterHide(result, parent.get(0), elem);
		}, 'json');
	};

	/**
	 * [replyTo description]
	 * @param  Event event [description]
	 * @param boolean 	Are we quoting someone?
	 * @return {[type]}       [description]
	 */
	replyTo(event, isQuoting)
	{
		event.preventDefault();
		let $elem = $(event.target);
		this.startEditor($elem.data('container') || $elem.data('parent'));
		let form = $($elem.data('parent'));
		form.find("[role~='"+this.forms.inputs.reply_to+"']").val($elem.data('reply-to'));
		let msgField = form.find("textarea");
		let value = '';
		if(isQuoting) {
			let quote = {
				author: $elem.data('author'),
				parent: $elem.data('parent'),
				reply_to: $elem.data('reply-to-id'),
				message: $($elem.data('reply-to-message')).html()
			};
			value = "<blockquote>";
			value += quote.author+" said:<br>"+quote.message;
			value += "</blockquote><br>";
		}
		msgField.val(value).focus();
		this.setEditorValue(msgField.get(0), value, false, this.editor);
		$(this.views.roles.replyToIndicator).html("Replying to "+$elem.data('author'));
	};

	/**
	 * Reply by quoting from a user's message
	 * @param  Event event [description]
	 * @return null       [description]
	 */
	quote(event){
		this.replyTo(event, true);
	};

	/**
	 * Update the chat status
	 * @param  boolean update    Are we updating?
	 * @param  object result    [description]
	 * @param  string|$|HTMLElement container [description]
	 * @return null           [description]
	 */
	chatStatus(update, result, $container) {
		$nitm.getObj(container).find('[id="chat\-messages-nav"]').each(function(index, element) {
			let $tab = $(element);
			switch(update)
			{
				case false:
				$container.find('[id="chat\-messages\-nav"]').find('[class="badge"]').remove();
				$container.find('[id="chat\-updates"]').html('');
				$tab.removeClass('bg-success');
				break;

				default:
				let badge = $tab.find('[class="badge"]');
				$tab.addClass('bg-success');
				if(badge.get(0) !== undefined)
					badge.html(result.count);
				else
					$tab.append("<span class='badge'>"+result.count+"</span>");
				$container.find('[id="chat\-info\-pane"]').html(result.message);
				if($tab.parent().hasClass('active'))
					this.afterCreate(result, 'form\[role="chatForm"\]');
				break;
			}
		});
	};

	afterCreate(result, index, form, element) {
		if(result.success) {
			let $form = $(form);
			$form.find(".empty").remove();
			$nitm.place({append:true, index:-1}, result.data, $form.data('parent'));
			//this.initHiding('#'+result.unique_id);
			//this.initQuoting('#'+result.unique_id);
			//this.initReplying('#'+result.unique_id);
			$form.find('[role~="'+this.forms.inputs.reply_to+'"]').val('');
			this.setEditorValue($form.find('textarea').get(0), '', false, this.editor);
			$form.find(this.views.roles.replyToIndicator).html("");
		} else
			$nitm.notify('Unable to add message', 'alert '+this.classes.error, form);
	};

	afterHide(result, element, activator) {
		if(result.success)
		{
			if(result.value)
				$(element).addClass(this.classes.hidden);
			else
				$(element).removeClass(this.classes.hidden);
			$(activator).text(result.action);
			/*$nitm.getObj("[id='"+this.views.containers.message+result.id+"']").each(function(index, element) {
					$(element).html(result.action);
				});;*/
		}
	};

	startEditor(containerId, value, button) {
		let activator = $(button);
		let $containers = $nitm.getObj(containerId);
		$containers.each((index, element) => {
			let $container = $(element);
			let $textarea = $container.find('textarea');
			if(!$textarea.get(0))
			{
				let textareaId = $container.attr('id')+'-textarea';
				$textarea = $("<textarea id='"+textareaId+"' role='editor' class='form-control' name='Replies[message]' rows=10>");
				let actions = $container.find("[role='"+this.elements.replyActions+"']");
				actions.removeClass('hidden');
				if(activator.data('use-modal')) {
					let $content = $container.find('.modal');
					if(!$content.length) {
						$nitm.dialog($textarea, {
							title: "<h3>Your message:</h3>",
							actions: actions
						});
					} else {
						$content.modal({
							keyboard: true
						});
						$content.on('hidden.bs.modal', (event) => {
							this.closeEditor($container.attr('id'));
						});
					}
				} else
					$textarea.insertBefore(actions);
			}
			let type = $textarea.parents('form').data('editor');
			switch(type)
			{
				case 'redactor':
				$nitm.getObj("#"+$textarea.attr('id')).map((elem) => {
					let $ta = $(elem);
					try {
						let instance = $ta.redactor('core.getObject');
						instance.code.sync();
						instance.focus.setEnd();
					} catch (error) {
						$ta.redactor({
							air: false,
							focus: true,
							autoresize: true,
							initCallback: function(value){
								if(value !== undefined) {
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
			$textarea.parents('form').find("[role='startEditor']").remove();
		});
		this.initCreating(containerId);
	};

	closeEditor(containerId) {
		let $containers = $(containerId);
		$containers.each(function(index, element) {
			let $container = $(element);
			let $content = $($container.attr('id')).find('.modal-dialog');
			switch($content.get(0) === undefined)
			{
				//if there is a modal
				case false:
				//destroy the editor
				let $textarea = content.find("textarea");
				let type = $nitm.getObj(field).parents('form').data('editor');
				switch(type)
				{
					case 'redactor':
					$('#'+$textarea.prop('id')).redactor('core.destroy');
					break;
				}
				//$container.find('.redactor_box').remove();
				//unhide the actions
				let $actions = $container.find("[role='"+this.elements.replyActions+"']");
				$actions.addClass('hidden');
				//reset the html
				$container.html($content.html());
				break;
			}
			this.elements.allowEditor.map((v) => {
				$nitm.getObj($container.attr('id')+" "+"[role='"+v+"']").map((elem) => {
					$(elem).removeClass('hidden');
				});
			});
		});
		//need to reinit click to reply button since not doing so casues form to submit
		this.initEditor(containerId);
	};

	setEditorValue(field, value, quote) {
		let type = $nitm.getObj(field).parents('form').data('editor');
		switch(type)
		{
			case 'ckeditor':
			let editor = CKEDITOR.instances[field];
			switch(quote)
			{
				case true:
				editor.edi$table().setHtml(value);
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
			let msgField = $nitm.getObj(field);
			msgField.val(value);
			break;
		}
		this.setEditorFocus(field);
	};

	getEditorValue(field) {
		let ret_val = '';
		let type = $nitm.getObj(field).parent('form').data('editor');
		switch(type)
		{
			case 'ckeditor':
			let editor = CKEDITOR.instances[field];
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
	};

	setEditorFocus(field) {
		let type = $nitm.getObj(field).parent('form').data('editor');
		switch(type)
		{
			case 'ckeditor':
			let editor = CKEDITOR.instances[field];
			editor.focus();
			break;

			case 'redactor':
			$nitm.getObj(field).redactor('focus.setEnd');
			break;

			default:
			$nitm.getObj(field).get(0).focus();
			break;
		}
	};

	charsLeft(field, cntfield, maxlimit)
	{
		field = this.getObj(field).get(0);
		cntfield = this.getObj(cntfield).get(0);
		if(field.value.length >= maxlimit+1) {
			field.value = field.value.substring(0, maxlimit);
			cntfield.innerHTML = maxlimit - field.value.length;
			alert("You've maxed out the "+maxlimit+" character limit\n\nPlease shorten your message. :-).");
		} else
			cntfield.innerHTML = maxlimit - field.value.length;
	};
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Replies());
});
