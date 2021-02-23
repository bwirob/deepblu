/*jshint browser:true*/
/*global define*/
define(
    [
        'ko',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-billing-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Ui/js/model/messageList',
        'mage/translate'
    ],
    function (
        ko,
        Component,
        customer,
        addressList,
        quote,
        createBillingAddress,
        selectBillingAddress,
        checkoutData,
        checkoutDataResolver,
        customerData,
        setBillingAddressAction,
        globalMessageList,
        $t
    ) {
        'use strict';

        var lastSelectedBillingAddress = null,
            newAddressOption = {
                getAddressInline: function () {
                    return $t('New Address');
                },
                customerAddressId: null
            },
            countryData = customerData.get('directory-data'),
            addressOptions = addressList().filter(function (address) {
                return address.getType() == 'customer-address';
            });
        addressOptions.push(newAddressOption);

        if (window.checkoutConfig.suggest_address && window.checkoutConfig.google_api_key) {
            var google_maps_loaded_def = null;
            if (!google_maps_loaded_def) {
                google_maps_loaded_def = $.Deferred();
                window.onestep_google_maps_loaded = function () {
                    google_maps_loaded_def.resolve(google.maps);
                }
                require(['https://maps.googleapis.com/maps/api/js?key=' + window.checkoutConfig.google_api_key + '&v=3.exp&libraries=places&language=en'], function () {
                }, function (err) {
                    google_maps_loaded_def.reject();
                });
            }
            google_maps_loaded_def.promise();
        }
        var initAutocomplete = function (formId, type) {
            if (window.checkoutConfig.suggest_address && window.checkoutConfig.google_api_key) {
                var placeSearch, autocomplete, searchElement;
                var componentForm = {
                    street_number: 'short_name',
                    route: 'long_name',
                    locality: 'long_name',
                    administrative_area_level_1: 'short_name',
                    country: 'short_name',
                    postal_code: 'short_name',
                    sublocality_level_1: 'long_name'
                };
                if (type && type == 'billing') {
                    searchElement = document.querySelectorAll("._active ." + formId + " input[name='street[0]']")[0];
                } else {
                    searchElement = document.querySelectorAll("div[name='shippingAddress.street.0'] input[name='street[0]']")[0];
                }
                if (!searchElement) {
                    return false;
                }
                autocomplete = new google.maps.places.Autocomplete((searchElement), {types: ['geocode']});
                var geolocate = function () {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            var geolocation = {lat: position.coords.latitude, lng: position.coords.longitude};
                            var circle = new google.maps.Circle({center: geolocation, radius: position.coords.accuracy});
                            autocomplete.setBounds(circle.getBounds());
                        });
                    }
                };
                if (searchElement) {
                    searchElement.onfocus = geolocate();
                }
                var fillInAddress = function () {
                    var place = autocomplete.getPlace();
                    var info = exportLocationInfo(place);
                    if (type && type == 'billing') {
                        fillAddressBillingForm(info);
                    } else {
                        fillAddressForm(info);
                    }
                };
                var fillAddressForm = function (locationInfo) {
                    //console.log(locationInfo);
                    var $street = $('#' + formId).find('[name$="street[0]"]');
                    if (locationInfo.street.street1) {
                        $street.eq(0).val(locationInfo.street.street1);
                        $street.trigger('change');
                    }
                    $street.eq(1).val(locationInfo.street.street2);
                    var needReloadShipping = false;
                    var triggerElement = false;
                    if (locationInfo.country_id) {
                        $('#' + formId).find('[name$="country_id"]').val(locationInfo.country_id);
                        needReloadShipping = true;
                        triggerElement = $('#' + formId).find('[name$="country_id"]');
                        triggerElement.trigger('change');
                    }
                    if (locationInfo.region) {
                        $('#' + formId).find('[name$="region"]').val(locationInfo.region).trigger('change');
                    }
                    if (locationInfo.region_id) {
                        $('#' + formId).find('[name$="region_id"]').find('*[data-title="' + locationInfo.region_id + '"]').prop('selected', true);
                        needReloadShipping = true;
                        triggerElement = $('#' + formId).find('[name$="region_id"]');
                    }
                    if (locationInfo.city) {
                        $('#' + formId).find('[name$="city"]').val(locationInfo.city).trigger('change');
                    }
                    if (locationInfo.postcode) {
                        $('#' + formId).find('[name$="postcode"]').val(locationInfo.postcode);
                        needReloadShipping = true;
                        triggerElement = $('#' + formId).find('[name$="postcode"]');
                    }
                    if (needReloadShipping == true && triggerElement != false) {
                        triggerElement.trigger('change');
                    }
                }
                var fillAddressBillingForm = function (locationInfo) {
                    var $street = $('._active .' + formId).find('[name$="street[0]"]');
                    if (locationInfo.street.street1) {
                        $street.eq(0).val(locationInfo.street.street1);
                        $street.trigger('change');
                    }
                    $street.eq(1).val(locationInfo.street.street2);
                    if (locationInfo.country_id) {
                        $('._active .' + formId).find('[name$="country_id"]').val(locationInfo.country_id).trigger('change');
                    }
                    if (locationInfo.region) {
                        $('._active .' + formId).find('[name$="region"]').val(locationInfo.region).trigger('change');
                    }
                    if (locationInfo.region_id) {
                        $('._active .' + formId).find('[name$="region_id"]').find('*[data-title="' + locationInfo.region_id + '"]').prop('selected', true).trigger('change');
                    }
                    if (locationInfo.city) {
                        $('._active .' + formId).find('[name$="city"]').val(locationInfo.city).trigger('change');
                    }
                    if (locationInfo.postcode) {
                        $('._active .' + formId).find('[name$="postcode"]').val(locationInfo.postcode).trigger('change');
                    }
                }
                var exportLocationInfo = function (place) {
                    var street, city, region_id, region, country, postcode, sublocality;
                    for (var i = 0; i < place.address_components.length; i++) {
                        var addressType = place.address_components[i].types[0];
                        if (componentForm[addressType]) {
                            if (addressType == 'street_number') {
                                if (street)
                                    street += ' ' + place.address_components[i][componentForm['street_number']]; else
                                    street = place.address_components[i][componentForm['street_number']];
                            }
                            if (addressType == 'route') {
                                if (street)
                                    street += ' ' + place.address_components[i][componentForm['route']]; else
                                    street = place.address_components[i][componentForm['route']];
                            }
                            if (addressType == 'locality')
                                city = place.address_components[i][componentForm['locality']];
                            if (addressType == 'administrative_area_level_1') {
                                region_id = place.address_components[i]['long_name'];
                                region = place.address_components[i]['long_name'];
                            }
                            if (addressType == 'country')
                                country = place.address_components[i][componentForm['country']];
                            if (addressType == 'postal_code')
                                postcode = place.address_components[i][componentForm['postal_code']];
                            if (addressType == 'sublocality_level_1')
                                sublocality = place.address_components[i][componentForm['sublocality_level_1']];
                        }
                    }
                    return {
                        street: {street1: street, street2: sublocality,},
                        city: city,
                        region_id: region_id,
                        region: region,
                        country_id: country,
                        postcode: postcode
                    }
                }
                autocomplete.addListener('place_changed', fillInAddress);
            }
        };


        return Component.extend({
            defaults: {
                template: 'Netbaseteam_Opc/billing-address'
            },
            currentBillingAddress: quote.billingAddress,
            addressOptions: addressOptions,
            customerHasAddresses: addressOptions.length > 1,

            /**
             * Init component
             */
            initialize: function () {
                this._super();
                quote.paymentMethod.subscribe(function () {
                    checkoutDataResolver.resolveBillingAddress();
                }, this);
            },

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this._super()
                    .observe({
                        selectedAddress: null,
                        isAddressDetailsVisible: quote.billingAddress() != null,
                        isAddressFormVisible: !customer.isLoggedIn() || addressOptions.length == 1,
                        isAddressSameAsShipping: false,
                        saveInAddressBook: 1
                    });
                this.initGoogleAddress();

                quote.billingAddress.subscribe(function (newAddress) {
                    if (quote.isVirtual()) {
                        this.isAddressSameAsShipping(false);
                    } else {
                        this.isAddressSameAsShipping(
                            newAddress != null &&
                            newAddress.getCacheKey() == quote.shippingAddress().getCacheKey()
                        );
                    }

                    if (newAddress != null && newAddress.saveInAddressBook !== undefined) {
                        this.saveInAddressBook(newAddress.saveInAddressBook);
                    } else {
                        this.saveInAddressBook(1);
                    }
                    this.isAddressDetailsVisible(true);
                }, this);

                // quote.shippingAddress.subscribe(function (newAddress) {
                //     if (!quote.isVirtual() && this.isAddressSameAsShipping()) {
                //         quote.billingAddress(newAddress);
                //     }
                // }, this);

                return this;
            },

            canUseShippingAddress: ko.computed(function () {
                return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
            }),

            /**
             * @param {Object} address
             * @return {*}
             */
            addressOptionsText: function (address) {
                return address.getAddressInline();
            },

            /**
             * @return {Boolean}
             */
            useShippingAddress: function () {
                if (this.isAddressSameAsShipping()) {
                    selectBillingAddress(quote.shippingAddress());
                    if (window.checkoutConfig.reloadOnBillingAddress) {
                        setBillingAddressAction(globalMessageList);
                    }
                    this.isAddressDetailsVisible(true);
                } else {
                    lastSelectedBillingAddress = quote.billingAddress();
                    quote.billingAddress(null);
                    this.isAddressDetailsVisible(false);
                }
                checkoutData.setSelectedBillingAddress(null);

                return true;
            },

            /**
             * Update address action
             */
            updateAddress: function () {
                if (this.selectedAddress() && this.selectedAddress() != newAddressOption) {
                    selectBillingAddress(this.selectedAddress());
                    checkoutData.setSelectedBillingAddress(this.selectedAddress().getKey());
                    if (window.checkoutConfig.reloadOnBillingAddress) {
                        setBillingAddressAction(globalMessageList);
                    }
                } else {
                    this.source.set('params.invalid', false);
                    this.source.trigger(this.dataScopePrefix + '.data.validate');
                    if (this.source.get(this.dataScopePrefix + '.custom_attributes')) {
                        this.source.trigger(this.dataScopePrefix + '.custom_attributes.data.validate');
                    };

                    if (!this.source.get('params.invalid')) {
                        var addressData = this.source.get(this.dataScopePrefix),
                            newBillingAddress;

                        if (customer.isLoggedIn() && !this.customerHasAddresses) {
                            this.saveInAddressBook(1);
                        }
                        addressData.save_in_address_book = this.saveInAddressBook() ? 1 : 0;
                        newBillingAddress = createBillingAddress(addressData);

                        // New address must be selected as a billing address
                        selectBillingAddress(newBillingAddress);
                        checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                        checkoutData.setNewCustomerBillingAddress(addressData);

                        if (window.checkoutConfig.reloadOnBillingAddress) {
                            setBillingAddressAction(globalMessageList);
                        }
                    }
                }
            },

            /**
             * Edit address action
             */
            editAddress: function () {
                lastSelectedBillingAddress = quote.billingAddress();
                quote.billingAddress(null);
                this.isAddressDetailsVisible(false);
            },

            /**
             * Cancel address edit action
             */
            cancelAddressEdit: function () {
                this.restoreBillingAddress();

                if (quote.billingAddress()) {
                    // restore 'Same As Shipping' checkbox state
                    this.isAddressSameAsShipping(
                        quote.billingAddress() != null &&
                        quote.billingAddress().getCacheKey() == quote.shippingAddress().getCacheKey() &&
                        !quote.isVirtual()
                    );
                    this.isAddressDetailsVisible(true);
                }
            },

            /**
             * Restore billing address
             */
            restoreBillingAddress: function () {
                if (lastSelectedBillingAddress != null) {
                    selectBillingAddress(lastSelectedBillingAddress);
                }
            },

            /**
             * @param {Object} address
             */
            onAddressChange: function (address) {
                this.isAddressFormVisible(address == newAddressOption);
            },

            /**
             * @param {int} countryId
             * @return {*}
             */
            getCountryName: function (countryId) {
                return countryData()[countryId] != undefined ? countryData()[countryId].name : '';
            },

            ///**
            // * @param {int} city
            // * @return {boolean}
            // */
            //hasCity: function (city) {
            //    return city != null && city != undefined && city != '';
            //},

            initGoogleAddress: function () {
                if (window.checkoutConfig.suggest_address == true && window.checkoutConfig.google_api_key) {
                    setTimeout(function () {
                        initAutocomplete('billing-address-form', 'billing');
                    }, 2000);
                }
            }
        });
    }
);
