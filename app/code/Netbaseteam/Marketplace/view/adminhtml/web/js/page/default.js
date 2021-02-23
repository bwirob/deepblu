require([
    'jquery'
], function ($) {
    $(function () {
        if ($('#menu-netbaseteam-marketplace-marketplace').length) {
            $('#menu-netbaseteam-marketplace-marketplace').remove();
        }
        if ($('.netbaseteammp.admin__page-nav-item.item').length) {
            $('.netbaseteammp.admin__page-nav-item.item').remove();
        }
    });
});