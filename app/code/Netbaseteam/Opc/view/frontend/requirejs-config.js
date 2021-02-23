var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Netbaseteam_Opc/js/model/agreements/place-order-mixin': true,
                'Netbaseteam_Opc/js/model/place-order-with-comments-delivery-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Netbaseteam_Opc/js/model/payment/place-order-mixin': true
            }
        }
    },
    map: {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default": "Netbaseteam_Opc/js/model/shipping-save-processor/default"
        }
    }
};