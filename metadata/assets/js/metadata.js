'use strict';

// JavaScript Document

class Metadata extends NitmEntity {
    constructor() {
        super('metadata');
        this.buttons = {
            roles: {
                create: 'createMetadata',
                udpate: 'updateMetadata',
                remove: 'deleteMetadata',
                disable: 'disableParent'
            }
        };
        this.inputs = {
            roles: {
                id: 'metadataId',
            }
        };

        Object.assign(this.views, {
            itemId: 'data',
            containerId: '[role~="metadata"]',
            roles: {
                item: 'metadataItem',
                template: 'metadataTemplate'
            }
        });

        this.defaultInit = [
            'initCreating',
            'initRemoving'
        ];
    }

    initCreating(containerId, currentIndex) {
        var $container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
        $container.find("[role~='" + this.buttons.roles.create + "']").map((i, elem) => {
            let $elem = $(elem);
            $elem.off('click');
            $elem.on('click', (e) => {
                e.preventDefault();
                var template = $elem.parents().siblings("[role='" + this.views.roles.template + "']");
                var $clone = template.clone();
                $clone.removeClass('hidden').attr('role', this.views.roles.item);
                var deleteButton = $clone.find("[role='" + this.buttons.roles.remove + "']");
                deleteButton.attr('id', 'delete-metadata' + Date.now());
                deleteButton.off('click');
                deleteButton.on('click', function(e) {
                    e.preventDefault();
                    $nitm.module('tools').removeParent(e.currentTarget);
                });
                $clone.find("[role='" + this.buttons.roles.disable + "']").replaceWith(deleteButton);
                $clone.attr('id', $clone.attr('id')+Date.now());
                template.before($clone).slideDown();
                this.initForms('#'+$clone.attr('id'));
                $nitm.m('tools').initDefaults($clone.attr('id'));
            });
        });
    }

    initRemoving(containerId, currentIndex) {
        $nitm.module('tools').initDisableParent(containerId);
    }
}

$nitm.onModuleLoad('entity', function(module) {
    module.initModule(new Metadata());
});
