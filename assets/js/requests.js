'use strict';

// JavaScript Document

class Requests extends NitmEnity
{
	constructor() {
		super('entity:request');
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
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Requests());
});
