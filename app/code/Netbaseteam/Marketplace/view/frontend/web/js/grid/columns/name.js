define([
    './column',
    'jquery'
], function (Column, $) {
    'use strict';

    return Column.extend({
        defaults: {
            fieldClass: {
                'netbaseteam-marketplace-grid-name-cell': true
            }
        }
    });
});
