define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'mage/template',
    'text!Netbaseteam_Marketplace/templates/grid/cells/seller/commission.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, editPreviewTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        gethtml: function (row) {
            return row[this.index + '_html'];
        },
        getFormaction: function (row) {
            return row[this.index + '_formaction'];
        },
        getSellerid: function (row) {
            return row[this.index + '_sellerid'];
        },
        getLabel: function (row) {
            return row[this.index + '_html']
        },
        getCommissionAmount: function (row) {
            return row[this.index + '_commissionAmount']
        },
        getCommissionOption: function (row) {
            return row[this.index + '_commissionOption']
        },
        getCommissionType: function (row) {
            return row[this.index + '_commissionType']
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
            var modalHtml = mageTemplate(
                editPreviewTemplate,
                {
                    html: this.gethtml(row),
                    title: this.getTitle(row),
                    label: this.getLabel(row),
                    formaction: this.getFormaction(row),
                    sellerid: this.getSellerid(row),
                    commission_amount: this.getCommissionAmount(row),
                    commission_option: this.getCommissionOption(row),
                    commission_type: this.getCommissionType(row),
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
        },


        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});