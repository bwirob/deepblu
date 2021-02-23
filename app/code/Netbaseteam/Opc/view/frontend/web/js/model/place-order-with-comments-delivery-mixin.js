/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (placeOrderAction) {
        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, redirectOnSuccess, messageContainer) {
            // adding order comments
            var order_comments = jQuery('[name="comment-code"]').val();
            var order_delivery = jQuery('[name="delivery_date"]').val();
            var order_message_to = jQuery('[name="gift-message-whole-to"]').val();
            var order_message_from = jQuery('[name="gift-message-whole-from"]').val();
            var order_gift_message = jQuery('[name="gift-message-whole-message"]').val();

            if (typeof(paymentData.additional_data) === 'undefined'
                || paymentData.additional_data === null
            ) {
                paymentData.additional_data = {
                    comments: order_comments,
                    delivery: order_delivery,
                    messageTo: order_message_to,
                    messageFrom : order_message_from,
                    message : order_gift_message
                };
            } else {
                paymentData.additional_data.comments = order_comments;
                paymentData.additional_data.delivery = order_delivery;
                paymentData.additional_data.messageTo = order_message_to;
                paymentData.additional_data.messageFrom = order_message_from;
                paymentData.additional_data.message = order_gift_message;
            }

            return originalAction(paymentData, redirectOnSuccess, messageContainer);
        });
    };
});