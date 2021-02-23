define(
    [
        'jquery',
        'ko',
        'uiComponent'
    ],
    function ($, ko, Component) {
        'use strict';
        var show_hide_comment_blockConfig = window.checkoutConfig.show_hide_comment_block;
        return Component.extend({
            defaults: {
                template: 'Netbaseteam_Opc/checkout/comment'
            },
            canVisibleBlock: show_hide_comment_blockConfig
        });
    });
