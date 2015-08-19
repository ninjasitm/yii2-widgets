
function Revisions(items)
{	
	NitmEntity.call(this, arguments);
	
	var self = this;
	this.id = 'revisions';
	this.interval = 3000; //In seconds
	this.classes = {
		success: 'bg-success',
		error: 'bg-danger',
	};
	this.events = [
		'blur',
	];
	this.roles = {
		create: ['createRevision'],
		checkStatus: 'revisionStatus'
	};
	this.defaultInit = [
		'initMetaActions'
	];
	
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
				if($(this).attr('revisionRecentActivity'))
					self.operation(this);
			})
		});
	}
	
	this.initActivity = function(container)
	{
		var container = (container == undefined) ? 'body' : container;
		$.map(this.roles.create, function (role, k) {
			var object = $(container+" "+"[role='"+role+"']");
			switch(true)
			{
				case self.useRedactor == true:
				case object.data('enable-redactor') == true:
				var callbacks = {
					autosaveCallback: function (name, result) {
						self.afterCreate(result, container);
					}
				};
				var redactorObject = $('#'+object.prop('id'));
				self.events.map(function (e, i) {
					callbacks[e+'Callback'] = function () {
						$(this).attr('revisionRecentActivity', true);
						object.on(e, self.operation(self.getData(object, function (){
							return redactorObject.redactor('code.get');
						}), null, container));
					};
				});
				redactorObject.redactor(callbacks);
				break;
				
				default:
				self.events.map(function (e, i) {
					object.on(e, function () {
						$(this).attr('revisionRecentActivity', true);
					});
					object.on(e, self.operation(self.getData(this), this, container));
				});
				break;
			}
		});
	}
	
	this.getData = function (elem, valueCallback) {
		var matches = $(elem).attr('name').match(/\[(.*?)\]/);
		if(matches)
			var attrName = matches[1];
		else
			var attrName = self.attributeName;
		
		var data = {
			attribute : attrName,
		};
		if(typeof valueCallback == 'function')
			data[attrName] = valueCallback(elem);
		else
			data[attrName] = $(elem).val();
		return data;
	}
	
	this.operation = function (data, element, container) {
		data['__format'] = 'json';
		data['getHtml'] = true;
		data['do'] = true;
		data['ajax'] = true;
		var url = $(element).data('save-path') || self.saveUrl;
		if(url) {
			var request = $nitm.doRequest(url, 
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
					$nitm.notify('Error Could not perform Revisions action. Please try again', self.classes.error, false);
				}
			);
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
			$nitm.notify('Unable to save revision', self.classes.error);
			break;
		}
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Revisions());
});