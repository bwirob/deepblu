define(
    [
        'jquery',
        "underscore",
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Netbaseteam_Opc/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Netbaseteam_Opc/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'mage/translate',
        'Netbaseteam_Opc/js/model/shipping-rate-service',
        'Netbaseteam_Opc/js/model/google-autocomplete-address'
    ],
    function(
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        $t,
        GoogleAutocompleteAddress
    ) {
        'use strict';
        var popUp = null;
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
                template: 'Netbaseteam_Opc/shipping'
            },
            visible: ko.observable(!quote.isVirtual()),
            errorValidationMessage: ko.observable(false),
            isCustomerLoggedIn: customer.isLoggedIn,
            isFormPopUpVisible: formPopUpState.isVisible,
            isFormInline: addressList().length == 0,
            isNewAddressAdded: ko.observable(false),
            saveInAddressBook: 1,
            quoteIsVirtual: quote.isVirtual(),

            initialize: function () {
                var self = this;
                this._super();
                this.initGoogleAddress();

                $('body').addClass('cmsmart-onepage-checkout').addClass(window.checkoutConfig.onestepcheckoutLayout);
                if (quote.isVirtual()) {
                    $('body').addClass('cmsmart-onepage-checkout').addClass('cmsmart-opc-virtual');
                }
                checkoutDataResolver.resolveShippingAddress();

                var hasNewAddress = addressList.some(function (address) {
                    return address.getType() == 'new-customer-address';
                });

                this.isNewAddressAdded(hasNewAddress);

                this.isFormPopUpVisible.subscribe(function (value) {
                    if (value) {
                        self.getPopUp().openModal();
                    }
                });

                quote.shippingMethod.subscribe(function (value) {
                    self.errorValidationMessage(false);
                });

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var shippingAddressData = checkoutData.getShippingAddressFromData();
                    if (shippingAddressData) {
                        checkoutProvider.set(
                            'shippingAddress',
                            $.extend({}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                        );
                    }
                    checkoutProvider.on('shippingAddress', function (shippingAddressData) {
                        checkoutData.setShippingAddressFromData(shippingAddressData);
                    });
                });

                return this;
            },

            navigate: function () {
                //load data from server for shipping step
            },

            initElement: function(element) {
                if (element.index === 'shipping-address-fieldset') {
                    shippingRatesValidator.bindChangeHandlers(element.elems(), false);
                }
            },

            getPopUp: function() {
                var self = this;
                if (!popUp) {
                    var buttons = this.popUpForm.options.buttons;
                    this.popUpForm.options.buttons = [
                        {
                            text: buttons.save.text ? buttons.save.text : $t('Save Address'),
                            class: buttons.save.class ? buttons.save.class : 'action primary action-save-address',
                            click: self.saveNewAddress.bind(self)
                        },
                        {
                            text: buttons.cancel.text ? buttons.cancel.text: $t('Cancel'),
                            class: buttons.cancel.class ? buttons.cancel.class : 'action secondary action-hide-popup',
                            click: function() {
                                this.closeModal();
                            }
                        }
                    ];
                    this.popUpForm.options.closed = function() {
                        self.isFormPopUpVisible(false);
                    };
                    popUp = modal(this.popUpForm.options, $(this.popUpForm.element));
                }
                return popUp;
            },

            /** Show address form popup */
            showFormPopUp: function() {
                this.isFormPopUpVisible(true);
            },


            /** Save new shipping address */
            saveNewAddress: function() {
                this.source.set('params.invalid', false);
                this.source.trigger('shippingAddress.data.validate');

                if (!this.source.get('params.invalid')) {
                    var addressData = this.source.get('shippingAddress');
                    addressData.save_in_address_book = this.saveInAddressBook ? 1 : 0;

                    // New address must be selected as a shipping address
                    var newShippingAddress = createShippingAddress(addressData);
                    selectShippingAddress(newShippingAddress);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                    checkoutData.setNewCustomerShippingAddress(addressData);
                    this.getPopUp().closeModal();
                    this.isNewAddressAdded(true);
                }
            },

            /** Shipping Method View **/
            rates: shippingService.getShippingRates(),
            isLoading: shippingService.isLoading,

            carriers: shippingService.getShippingCarriers(),
            ratesLength: ko.computed(function () {
                return shippingService.getShippingRates().length;
            }),

            isSelected: ko.computed(function () {
                    return quote.shippingMethod()
                        ? quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code
                        : null;
                }
            ),

            selectShippingMethod: function(shippingMethod) {
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(shippingMethod.carrier_code + '_' + shippingMethod.method_code);
                $('#co-shipping-method-form').submit();
                return true;
            },

            setShippingInformation: function () {
                //skip load shipping data
                if (this.validateShippingInformation()) {
                    setShippingInformationAction().done(
						function() {
                            stepNavigator.next();
                        }
                    );
                }
                //this.validateShippingInformation();
            },

            validateShippingInformation: function () {
                var shippingAddress,
                    addressData,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn();

                if (!quote.shippingMethod()) {
                    this.errorValidationMessage('Please specify a shipping method');
                    return false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();
                }

                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate');
                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    };
                    if (this.source.get('params.invalid')
                        || !quote.shippingMethod().method_code
                        || !quote.shippingMethod().carrier_code
                        || !emailValidationResult
                    ) {
                        return false;
                    }
                    shippingAddress = quote.shippingAddress();
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                    //Copy form data to quote shipping address object
                    for (var field in addressData) {
                        if (addressData.hasOwnProperty(field)
                            && shippingAddress.hasOwnProperty(field)
                            && typeof addressData[field] != 'function'
                        ) {
                            shippingAddress[field] = addressData[field];
                        }
                    }

                    if (customer.isLoggedIn()) {
                        shippingAddress.save_in_address_book = 1;
                    }
                    selectShippingAddress(shippingAddress);
                }
                return true;
            },
            initGoogleAddress: function () {
                if (window.checkoutConfig.suggest_address == true && window.checkoutConfig.google_api_key) {
                    setTimeout(function () {
                        initAutocomplete('co-shipping-form', 'shipping');
                    }, 2000);
                }
            }
        });
    }
);
