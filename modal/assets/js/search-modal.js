// JavaScript Document

function SearchModal () {
	NitmEntity.call(this, arguments);
	
	var self = this;
	this.id = 'search-modal';
	this.selfInit = true;
	this.modal;
	this.isActive = false;
	this.modalOptions = {
		'show': false
	};
	this.events = [
		'keypress'
	];
	this.modalId = '#search-modal';
	this.searchField = '#search-field';
	this.resultContainer = '#search-results';
	this.defaultInit = [
	];

	this.init = function (containerId) {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method](containerId);
			}
		});
	}
	
	this.initSearch = function (modalId) {
		var modalId = modalId == undefined  ? self.modalId : modalId;
		$.map(self.events, function (event) {
			$(document).on(event, function (e) {
				if(self.isActive)
					return;
				//If any special jeys were hit then ignore this
				switch(true)
				{
					case $(e.target).is('input, textarea, .redactor-editor'):
					case e.ctrlKey || e.shiftkey || e.altKey || e.metaKey:
					case Array(
						'Esc', 'Escape', 'Backspace',
						'F1', 'F2', 'F3', 'F4', 
						'F5', 'F6', 'F7', 'F8', 
						'F7', 'F10', 'F11', 'F12'
					).indexOf(e.key) != -1:
					return;
					break;
				}
				
				if(self.modal == undefined)
				{
					self.modal = $(modalId);
					var $form = self.modal.find('form');
					$form.find(self.searchField).focus().val(e.key);
					$form.on('submit', function (event) {
						event.preventDefault();
						(new NitmEntity).operation(this, function (result, form) {
							self.modal.find(self.resultContainer).html(result.data);
							$(form).find(self.searchField).val(result.query);
						});
					});
					self.modal.on('hidden.bs.modal', function (e) {
						self.isActive = false;
						self.modal.modal('hide');
						e.stopPropagation();
					});
					self.modal.on('shown.bs.modal', function () {
						self.isActive = true;
						var $modal = $(this);
						var $form = $(this).find('form');
						var $input = $form.find(self.searchField);
						$input.focus().val(e.key).get(0).setSelectionRange($input.val().length*2, $input.val().length*2);
					});
					self.modal.modal(self.modalOptions);
				}
				if(!self.isActive)
				{
					self.modal.modal('show');
				}
			});
		});
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new SearchModal());
});