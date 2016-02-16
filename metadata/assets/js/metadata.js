'use strict';

// JavaScript Document

class Metadata extends NitmEntity
{
	construct() {
		super('entity:metadata');
		this.buttons = {
			roles: {
				'create': 'createMetadata',
				'remove': 'deleteMetadata',
				'disable': 'disableParent'
			}
		};
		Object.assign(this.forms, {
			roles: {
				'template': 'metadataTemplate',
			}
		});
		this.inputs = {
			roles: {
				'id': 'metadataId',
			}
		};

		Object.assign(this.views, {
			itemId : 'data',
			containerId: 'metadata',
		});

		this.defaultInit = [
			'initCreating',
			'initRemoving'
		];
	}

	initCreating(containerId, currentIndex) {
		var $container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		$container.find("[role~='"+this.buttons.roles.create+"']").map((elem) => {
			let $elem = $(elem);
			$elem.off('click');
			$elem.on('click', function (e) {
				e.preventDefault();
				var template = $elem.parents('tr').siblings("[role='"+this.forms.roles.template+"']");
				var $clone = template.clone();
				$clone.removeClass('hidden').attr('role', 'data');
				var deleteButton = $clone.find("[role='"+this.buttons.roles.remove+"']");
				deleteButton.attr('id', 'delete-metadata'+Date.now());
				deleteButton.off('click');
				deleteButton.on('click', function (e) {
					e.preventDefault();
					$nitm.module('tools').removeParentelem;
				});
				$clone.find("[role='"+this.buttons.roles.disable+"']").replaceWith(deleteButton);
				template.before($clone);
			});
		});
	}

	initRemoving(containerId, currentIndex) {
		$nitm.module('tools').initDisableParent(containerId);
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Metadata());
});
