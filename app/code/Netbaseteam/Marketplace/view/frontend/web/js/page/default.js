require([
    'jquery'
], function ($) {
    $(function () {
        if ($('.netbaseteam-marketplace.block.block-collapsible-nav').length) {
            $('.netbaseteam-marketplace.block.block-collapsible-nav').remove();
        }
        if ($('.page-actions-buttons.add-new-product').length) {
            $('.page-actions-buttons.add-new-product').remove();
        }
    });
});