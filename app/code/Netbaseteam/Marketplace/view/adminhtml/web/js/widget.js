define([
    "jquery"
],function($) {
    'use strict';
    var MarketplaceWidgetChooser = new function(){
        this._init = function () {
            return this;
        },
            this.hideEntityChooser = function (container) {
                if ($(container)) {
                    $(container).addClass('no-display').hide();
                }
            },
            this.displayEntityChooser = function (container,url) {
                var params = {};
                this.url = url;
                params.url = this.url;
                if (container && params.url) {
                    container = $(container);
                    params.data = {
                        id: container.attr('id'),
                        selected: container.closest('div.admin__field-control').find('input[type="text"].entities').first().val()
                    };
                    this.displayChooser(params, container);
                }
            },
            this.displayChooser = function (params, container) {
                container = $(container);
                if (params.url && container) {
                    if (container.html() == '') {
                        $.ajax({
                            url: params.url,
                            method: 'post',
                            showLoader: true,
                            data: params.data,
                            success: function (transport) {
                                try {
                                    if (transport) {
                                        container.prepend(transport);
                                        container.removeClass('no-display').show();
                                    }
                                } catch (e) {
                                    alert('Error occurs during loading chooser.');
                                }
                            }
                        });
                    } else {
                        container.removeClass('no-display').show();
                    }
                }
            },
            this.checkSeller = function (event) {
                var input = event.memo.element,
                    container = event.target.closest('div.admin__field-control'),
                    elm = $(container).find('input[type="text"].entities').first();
                if (!isNaN($(input).val())) this.updateEntityValue($(input).val(), elm, $(input).is(':checked'));
            },
            this.updateEntityValue = function (value, elm, isAdd) {
                var values = $(elm).val().strip();
                if (values) values = values.split(',');
                else values = [];
                if (isAdd) {
                    if (-1 === values.indexOf(value)) {
                        values.push(value);
                        $(elm).val(values.join(','));
                    }
                } else {
                    if (-1 != values.indexOf(value)) {
                        values.splice(values.indexOf(value), 1);
                        $(elm).val(values.join(','));
                    }
                }
            },
            this.clearEntityValue = function (container) {
                var elm = $(container).closest('div.admin__field-control').find('input[type="text"].entities').first();
                if (elm) elm.val('');
                var hidden = $(container).find('input[type="hidden"]').first();
                if (hidden) hidden.val('');
                $(container).find('input[type="checkbox"]').each(function () {
                    $(this).prop('checked', false);
                });
            }
    };

    return window.MarketplaceWidgetChooser = MarketplaceWidgetChooser;
});