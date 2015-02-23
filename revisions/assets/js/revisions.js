
function Revisions(items)
{	
	var self = this;
	this.id = 'revisions';
	var saveInterval = 10; //In seconds
	this.classes = {
		success: 'bg-success',
		error: 'bg-danger',
	};
	this.events = [
		'blur',
	];
	this.roles = {
		create: 'createRevision',
		checkStatus: 'revisionStatus'
	};
	this.defaultInit = [
		'initActivity',
		'initInterval',
	];

	this.init = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method](container);
			}
		});
	}
	
	this.initInterval = function (container) {
		var container = (container == undefined) ? 'body' : container;
		setInterval(
			function () {self.checkActivity(container)},
			self.interval
		);	
	}
	
	this.checkActivity = function (container) {
		$.map(this.roles, function (role, k) {
			$(container+" "+"[role='"+role+"']").map(function() {
				switch($(this).attr('revisionRecentActivity'))
				{
					case true:
					self.operation(this);
					break;
				}
			})
		});
	}
	
	this.initActivity = function(container)
	{
		var container = (container == undefined) ? 'body' : container;
		$.map(this.roles, function (role, k) {
			var object = $(container+" "+"[role='"+role+"']");
			switch(1)
			{
				case object.data('use-redactor') == true:
				var callbacks = {
					autosaveCallback: function (result) {
						self.afterCreate(result, container);
					}
				};
				var redactorObject = $('#'+object.prop('id'));
				self.events.map(function (e, i) {
					callbacks[e+'Callback'] = function () {
						$(this).attr('revisionRecentActivity', true);
						var data = {};
						data[$(this).attr('name')] = redactorObject.redactor('get');
						//object.on(e, self.operation(data, null, container));
					};
				});
				redactorObject.redactor(callbacks);
				break;
				
				case self.UseCke == true:
				CKEDITOR.on('instanceCreated', function(i) {
					switch($.inArray(i.editor.name, ids) != -1)
					{
						case true:
						i.editor.on('contentDom', function() {
							events.map(function (e, idx) {
								i.editor.document.on(e, function(event) {
									getObj(i.editor.name).attr('recentActivity', true);
								});
								switch(event_handler)
								{
									case true:
									var f = (typeof functions[idx] == 'function') ? functions[idx] : functions[0];
									getObj(i.editor.name).on(e, f);
									break;
								}
							});
						});
						break;
					}
				});
				break;
				
				default:
				self.events.map(function (e, i) {
					object.on(e, function () {
						$(this).attr('revisionRecentActivity', true);
					});
					var data = {};
					data[$(this).attr('name')] = $(this).val();
					object.on(e, self.operation(data, this, container));
				});
				break;
			}
		});
	}
	
	this.operation = function (data, element, container) {
		data.push({'name':'__format', 'value':'json'});
		data.push({'name':'getHtml', 'value':true});
		data.push({'name':'do', 'value':true});
		data.push({'name':'ajax', 'value':true});
		switch(!self.saveUrl)
		{
			case false:
			var request = $nitm.doRequest($(element).data('save-path'), 
				data,
				function (result) {
					switch(result.action)
					{
						case 'create':
						self.afterAdd(result, element, container);
						break;
					}
				},
				function () {
					notify('Error Could not perform Revisions action. Please try again', self.classes.error, false);
				}
			);
			break;
		}
	}
	
	this.afterCreate = function(result, element, container) {
		switch(result.success)
		{
			case true:
			ret_val = false;
			$(element).attr('revisionRecentActivity', false);
			$(container).find("[role='"+self.revisionStatus+"']").val(result.message);
			break;
			
			default:
			alert('Unable to add revision');
			break;
		}
	}
}

$nitm.addOnLoadEvent(function () {
	$nitm.initModule(new Revisions());
});