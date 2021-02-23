define([
    'jquery',
    'jquery/ui',
    'productListToolbarForm'
], function ($) {
    "use strict";

    $.widget('cmsmart.layer', {

        options: {
            productsListSelector: '#layer-product-list',
            navigationSelector: '#layered-filter-block'
        },

        _create: function () {
            this.initProductListUrl();
            this.initObserve();
            this.initLoading();
            this.showMore();
            this.useDropdown();
        },

        initProductListUrl: function () {
            var self = this;
            $.mage.productListToolbarForm.prototype.changeUrl = function (paramName, paramValue, defaultValue) {
                var urlPaths = this.options.url.split('?'),
                    baseUrl = urlPaths[0],
                    urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                    paramData = {},
                    parameters;
                for (var i = 0; i < urlParams.length; i++) {
                    parameters = urlParams[i].split('=');
                    paramData[parameters[0]] = parameters[1] !== undefined
                        ? window.decodeURIComponent(parameters[1].replace(/\+/g, '%20'))
                        : '';
                }
                paramData[paramName] = paramValue;
                if (paramValue == defaultValue) {
                    delete paramData[paramName];
                }
                paramData = $.param(paramData);

                self.ajaxSubmit(baseUrl + (paramData.length ? '?' + paramData : ''));
            }
        },

        initObserve: function () {
            var self = this;
            var aElements = this.element.find('a');
            aElements.each(function (index) {
                var el = $(this);
                var link = self.checkUrl(el.prop('href'));
                if (!link) return;

                el.bind('click', function (e) {
                    if (el.hasClass('swatch-option-link-layered')) {
                        var childEl = el.find('.swatch-option');
                        childEl.addClass('selected');
                    } else {
                        var checkboxEl = el.find('input[type=checkbox]');
                        checkboxEl.prop('checked', !checkboxEl.prop('checked'));
                    }

                    self.ajaxSubmit(link);
                    e.stopPropagation();
                    e.preventDefault();
                });

                var checkbox = el.find('input[type=checkbox]');
                checkbox.bind('click', function (e) {
                    self.ajaxSubmit(link);
                    e.stopPropagation();
                    e.preventDefault();
                });
            });

            $(".filter-current a").bind('click', function (e) {
                var link = self.checkUrl($(this).prop('href'));
                if (!link) return;

                self.ajaxSubmit(link);
                e.stopPropagation();
                e.preventDefault();
            });

            $(".filter-actions a").bind('click', function (e) {
                var link = self.checkUrl($(this).prop('href'));
                if (!link) return;

                self.ajaxSubmit(link);
                e.stopPropagation();
                e.preventDefault();
            });

            $(".pages a").bind('click', function (e) {
                var link = self.checkUrl($(this).prop('href'));
                if (!link) return;

                self.ajaxSubmit(link);
                e.stopPropagation();
                e.preventDefault();
            });

            $(window).on('popstate', function (e) {
                location.reload();
                //var link = window.location.pathname;
                ////if (!link) return;
                //
                //self.ajaxSubmit(link);
                //e.stopPropagation();
                //e.preventDefault();
            });
        },

        checkUrl: function (url) {
            var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

            return regex.test(url) ? url : null;
        },

        initLoading: function () {

        },


        showMore: function () {
            if (this.options.displayMode == 1) {
                $('.filter-options-item').each(function () {
                    var isLink = $(this).find('a').length;
                    if (isLink === 0) {
                        $(this).css('display', 'none');

                    }
                    if ($('#slider_min').val().length != 0 && $('#slider_min').val() != $('#slider_max').val()) {
                        $('#filter-by-Price').css('display', 'block');
                    } else {
                        $('#filter-by-Price').css('display', 'none');
                    }

                    if($('body').find('.action.tocart.primary').length == 0) {
                        $('.filter-options-item').css('display', 'none');
                    }
                    if($(this).find('a').attr('href') == 'javascript:void();') {
                        $('#filter-by-Color').css('display', 'none');
                        $('#filter-by-Size').css('display', 'none');
                    }

                });
                if (this.options.typeOfShow == 1) {
                    $('ol.cmsmart_navigation_items').each(function () {
                        var LiN = $(this).find('li').length;
                        if (LiN > 10) {
                            $(this).css('height', '220px');
                            $(this).css('overflow', 'scroll');
                            $(this).css('overflow-x', 'hidden');
                        }
                    });
                } else {
                    $('ol.cmsmart_navigation_items').each(function () {
                        var LiN = $(this).find('li').length;
                        if (LiN > 10) {
                            $('li', this).eq(2).nextAll().hide().addClass('toggleable');
                            $(this).append('<a href="javascript:void(0);" class="cmsmart_navigation_more" style="color:#1979c3">More...</a>');
                        }
                    });
                    $('ol.cmsmart_navigation_items').on('click', '.cmsmart_navigation_more', function () {

                        if ($(this).hasClass('less')) {
                            $(this).text('More...').removeClass('less');
                        } else {
                            $(this).text('Less...').addClass('less');
                        }

                        $(this).siblings('li.toggleable').slideToggle();
                    });
                }
            }

        },

        useDropdown: function (e) {
            var self = this;
            $('.cmsmart_dropdown_filter').change(function (e) {
                var link = $(this).val();
                if (!link) return;

                self.ajaxSubmit(link);
                e.stopPropagation();
                e.preventDefault();
            });
        },

        ajaxSubmit: function (submitUrl) {
            var self = this;

            $.ajax({
                url: submitUrl,
                data: {isAjax: 1},
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $('.loading_overlay').show();
                    if (typeof window.history.pushState === 'function') {
                        history.pushState({}, '', submitUrl);
                    }
                },
                success: function (res) {
                    if (res.navigation) {
                        $(self.options.navigationSelector).replaceWith(res.navigation);
                        $(self.options.navigationSelector).trigger('contentUpdated');
                    }
                    if (res.products) {
                        $(self.options.productsListSelector).replaceWith(res.products);
                        $(self.options.productsListSelector).trigger('contentUpdated');
                    }
                    $('.loading_overlay').hide();
                },
                error: function () {
                    window.location.reload();
                }
            });
        }
    });

    return $.cmsmart.layer;
});
