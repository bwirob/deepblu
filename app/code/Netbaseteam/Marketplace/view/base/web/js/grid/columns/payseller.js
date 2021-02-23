define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'mage/template',
    'text!Netbaseteam_Marketplace/templates/grid/cells/seller/payseller.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, sendmailPreviewTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        getFlag: function (row) {
            return row[this.index + '_flag'];
        },
        gethtml: function (row) {
            return row[this.index + '_html'];
        },
        getFormaction: function (row) {
            return row[this.index + '_formaction'];
        },
        getCustomerid: function (row) {
            return row[this.index + '_id'];
        },
        getSellerAmount: function (row) {
            return row[this.index + '_seller_amount'];
        },
        getCommission: function (row) {
            return row[this.index + '_commission'];
        },
        getLabel: function (row) {
            return row[this.index + '_html']
        },
        getTitle: function (row) {
            return row[this.index + '_title']
        },
        getSubmitlabel: function (row) {
            return row[this.index + '_submitlabel']
        },
        getCancellabel: function (row) {
            return row[this.index + '_cancellabel']
        },
        preview: function (row) {
            if (this.getFlag(row) == 1) {
                var modalHtml = mageTemplate(
                    sendmailPreviewTemplate,
                    {
                        active: this.getFlag(row),
                        html: this.gethtml(row),
                        title: this.getTitle(row),
                        label: this.getLabel(row),
                        formaction: this.getFormaction(row),
                        id: this.getCustomerid(row),
                        seller_amount: this.getSellerAmount(row),
                        commission: this.getCommission(row),
                        submitlabel: this.getSubmitlabel(row),
                        cancellabel: this.getCancellabel(row),
                        linkText: $.mage.__('Go to Details Page')
                    }
                );
                var previewPopup = $('<div/>').html(modalHtml);
                previewPopup.modal({
                    title: this.getTitle(row),
                    innerScroll: true,
                    modalClass: '_image-box',
                    buttons: []
                }).trigger('openModal');
            }
        },


        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});