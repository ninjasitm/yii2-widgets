// JavaScript Document

function SearchModal () {
	var self = this;
	this.selfInit = true;
	this.modal;
	this.isActive = false;
	this.modalOptions = {
		'show': false
	};
	this.modalId = '#search-modal';
	this.events = [
		'keypress'
	];
	this.id = '#search-modal';
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
					case e.ctrlKey:
					case e.shiftkey:
					case e.altKey:
					case e.metaKey:
					case e.key == 'Esc':
					case e.key == 'Escape':
					return;
					break;
				}
				switch($(e.target).is('input', 'textarea'))
				{
					case false:
					if(self.modal == undefined)
					{
						self.modal = $(modalId);
						var $form = self.modal.find('form');
						$form.find('#search-field').focus().val(e.key);
						$form.on('submit', function (event) {
							event.preventDefault();
							$nitm.module('entity').operation(this, function (result, form) {
								$(form).find('#search-field').val(result.query);
								self.modal.find('#search-results').html(result.data);
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
							$form.find('#search-field').focus().val(e.key);
						});
						self.modal.modal(self.modalOptions);
					}
					if(!self.isActive)
					{
						self.modal.modal('show');
					}
					break;
				}
			});
		});
	}
}

$nitm.addOnLoadEvent(function () {
	$nitm.initModule('search-modal', new SearchModal());
});