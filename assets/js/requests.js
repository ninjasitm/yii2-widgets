// JavaScript Document

function Requests () {
	NitmEntity.call(this);
	var self = this;
	this.id = 'entity:request';
	this.forms = {
		roles: {
			create: 'createRequest',
			update: 'updateRequest'
		}
	};
	
	this.buttons = {
		roles: []
	};
	this.views = {
		itemId : 'request',
		containerId: 'requests',
	}
	this.defaultInit = [
		'initMetaActions',
		'initForms'
	];
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Requests());
});