define([
        'jquery',
        'Magento_Ui/js/modal/modal',
        'jquery/ui',
        'jquery/validate',
        'Netbaseteam_Opc/js/cmsmart/plugins/jquery.nicescroll.min'
    ],
    function ($, modal) {
        'use strict';
        $.widget('mage.cmsmartOpc', {
            popup: null,
            init: function () {
                this.showModal();
                this.inputText();
                this.cvvText();
                this.sendForm();
                this.newModal();
            },

            inputText: function () {
                $(document).off('blur', '#authorizenet_directpost_cc_number');
                $(document).on('blur', '#authorizenet_directpost_cc_number', function (e) {

                    if ($('#authorizenet_directpost_cc_number').val() == 0) {
                        $(this).closest('div.number').find('label').removeClass('focus');
                    }
                });

                $(document).off('focus', '#authorizenet_directpost_cc_number');
                $(document).on('focus', '#authorizenet_directpost_cc_number', function (e) {

                    $(this).closest('div.number').find('label').addClass('focus');


                });
            },
            cvvText: function () {
                $(document).off('blur', '#authorizenet_directpost_cc_cid');
                $(document).on('blur', '#authorizenet_directpost_cc_cid', function (e) {

                    if ($('#authorizenet_directpost_cc_cid').val() == 0) {
                        $(this).closest('div.cvv').find('label').removeClass('focus');
                    }
                });

                $(document).off('focus', '#authorizenet_directpost_cc_cid');
                $(document).on('focus', '#authorizenet_directpost_cc_cid', function (e) {

                    $(this).closest('div.cvv').find('label').addClass('focus');


                });
            },
            showModal: function () {
                var _self = this;
                $(document).off('click touchstart', '.actions-toolbar .remind');
                $(document).on('click touchstart', '.actions-toolbar .remind', function (e) {
                    e.preventDefault();
                    $('.cmsmart-opc-forgot-response-message').hide();
                    _self.displayModal();
                });
            },

            newModal: function(){
                var _self = this;
                $(document).on('click touchstart', '.actions-toolbar .remind', function (e) {
                    e.preventDefault();
                    _self.reopenModal();
                });
            },

            reopenModal: function () {
                $(".cmsmart-opc-forgot-main-wrapper").modal('openModal');
            },

            displayModal: function () {
                var modalContent = $(".cmsmart-opc-forgot-main-wrapper");
                this.popup = modalContent.modal({
                    autoOpen: true,
                    type: 'popup',
                    modalClass: 'cmsmart-opc-forgot-wrapper',
                    title: '',
                    buttons: [{
                        class: "cmsmart-hidden-button-for-popup",
                        text: 'Back to Login',
                        click: function () {
                            $('.cmsmart-opc-forgot-response-message').hide();
                            this.closeModal();
                        }
                    }]
                });
            },

            sendForm: function(){
                $('.cmsmart-forgot-password-submit').click(function(e){
                    e.preventDefault();
                    var email = $('.cmsmart-opc-forgot-email').val();
                    var validator = $(".cmsmart-opc-forgot-form").validate();
                    var status = validator.form();
                    if (!status) {
                        return;
                    }
                    if (typeof(postUrl) != "undefined") {
                        var sendUrl = postUrl;
                    } else {
                        console.log("Netbaseteam post url error.");
                    }
                    $.ajax({
                        type: "POST",
                        data: {email: email},
                        url: sendUrl,
                        showLoader: true
                    }).done(function (response) {
                        if(typeof(response.message != "undefined")){
                            $('.cmsmart-opc-forgot-response-message').html(response.message);
                            $('.cmsmart-opc-forgot-email').val('');
                            $('.cmsmart-opc-forgot-response-message').show();
                        }
                    });
                });
            }
        });

        return $.mage.cmsmartOpc;

    });