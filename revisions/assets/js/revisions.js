'use strict';

class Revisions extends NitmEntity
{
	constructor() {
		super('revisions');
		this.interval = undefined; //In seconds
		this.events = [
			'blur',
		];
		this.roles = {
			create: ['createRevision'],
			checkStatus: 'revisionStatus'
		};
		this.defaultInit = [
			'initMetaActions',
			'initInterval'
		];
	}

	initInterval(container) {
		if(this.interval != undefined && this.interval >= 1000) {
			let container = (container == undefined) ? 'body' : container;
			setInterval(function () {
				$.map(this.roles.create, (role) => {
					$(container+" "+"[role='"+role+"']").map((elem) => {
						if($(elem).attr('revisionRecentActivity'))
							this.operation(this.getData(elem), elem, container);
					})
				});
			}, this.interval);
		}
	}

	initActivity(container)
	{
		let container = (container == undefined) ? 'body' : container;
		$.map(this.roles.create, (role) => {
			let $object = $(container+" "+"[role='"+role+"']");
			switch(true)
			{
				case this.useRedactor == true:
				case $object.data('enable-redactor') == true:
				let callbacks = {
					autosaveCallback: function (name, result) {
						this.afterCreate(result, container);
					}
				};
				let redactorObject = $('#'+$object.prop('id'));
				this.events.map((e, i) => {
					callbacks[e+'Callback'] () {
						$(this).attr('revisionRecentActivity', true);
						$object.on(e, this.operation(this.getData($object, function (){
							return redactorObject.redactor('code.get');
						}), null, container));
					};
				});
				redactorObject.redactor(callbacks);
				break;

				default:
				this.events.map((e, i) => {
					$object.on(e, function () {
						$(this).attr('revisionRecentActivity', true);
					});
					$object.on(e, this.operation(this.getData(this), this, container));
				});
				break;
			}
		});
	}

	getData(elem, valueCallback) {
		let matches = $(elem).attr('name').match(/\[(.*?)\]/);
		if(matches)
			let attrName = matches[1];
		else
			let attrName = this.attributeName;

		let data = {
			attribute : attrName,
		};
		if(typeof valueCallback == 'function')
			data[attrName] = valueCallback(elem);
		else
			data[attrName] = $(elem).val();
		return data;
	}

	operation(data, element, container) {
		data['__format'] = 'json';
		data['getHtml'] = true;
		data['do'] = true;
		data['ajax'] = true;
		let url = $(element).data('save-path') || this.saveUrl;
		if(url) {
			let request = $nitm.doRequest(url,
				data,
				function (result) {
					switch(result.action)
					{
						case 'create':
						this.afterCreate(result, element, container);
						break;
					}
				},
				function () {
					$nitm.notify('Error Could not perform Revisions action. Please try again', this.classes.error, false);
				}
			);
		}
	}

	afterCreate(result, element, container) {
		if(result.isRevision)
			$(container).find("[role='"+this.revisionStatus+"']").val(result.message);
		else
			$nitm.notify(result.message, 'alert alert-success');

		if(result.success) {
			$(element).attr('revisionRecentActivity', false);
		}
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Revisions());
});
