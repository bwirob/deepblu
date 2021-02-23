define([
    'jquery',
    'jquery/ui',
    'jquery/jquery.tabs'
], function ($) {
    'use strict';

    $.widget('mage.collapsable', {
        options: {
            parent: null,
            openedClass: 'opened',
            wrapper: '.fieldset-wrapper'
        },

        _create: function () {
            this._events();
        },

        _events: function () {
            var self = this;

            this.element
                .on('show', function (e) {
                    var fieldsetWrapper = $(this).closest(self.options.wrapper);

                    fieldsetWrapper.addClass(self.options.openedClass);
                    e.stopPropagation();
                })
                .on('hide', function (e) {
                    var fieldsetWrapper = $(this).closest(self.options.wrapper);

                    fieldsetWrapper.removeClass(self.options.openedClass);
                    e.stopPropagation();
                });
        }
    });

    return $.mage.collapsable;
});