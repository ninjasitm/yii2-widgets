// JavaScript Document

function Metadata () {
	var self = this;
	this.buttons = {
		roles: {
			'create': 'createMetadata', 
			'remove': 'deleteMetadata', 
			'disable': 'disableParent'
		}
	};
	this.forms = {
		roles: {
			'template': 'metadataTemplate', 
		}
	};
	this.inputs = {
		roles: {
			'id': 'metadataId', 
		}
	};
	
	this.views = {
		itemId : 'data',
		containerId: 'metadata',
	}
	this.defaultInit = [
		'initCreating',
		'initRemoving'
	];

	this.init = function (container) {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method](container);
			}
		});
		var $lab1 = $nitm.module('lab1');
	}
	
	this.initCreating = function (containerId, currentIndex) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		container.find("[role~='"+self.buttons.roles.create+"']").map(function() {
			$(this).off('click');
			$(this).on('click', function (e) {
				e.preventDefault();
				var template = $(this).parents('tr').siblings("[role='"+self.forms.roles.template+"']");
				var clone = template.clone();
				clone.removeClass('hidden').attr('role', 'data');
				var deleteButton = clone.find("[role='"+self.buttons.roles.remove+"']");
				deleteButton.attr('id', 'delete-metadata'+Date.now());
				deleteButton.off('click');
				deleteButton.on('click', function (e) {
					e.preventDefault();
					$nitm.module('tools').removeParent(this);
				});
				clone.find("[role='"+self.buttons.roles.disable+"']").replaceWith(deleteButton);
				template.before(clone);
			});
		});
	}
	
	this.initRemoving = function (containerId, currentIndex) {
		$nitm.module('tools').initDisableParent(containerId);
	}
}

$nitm.onModuleLoad('lab1', function () {
	$nitm.initModule('lab1:metadata', new Metadata());
});