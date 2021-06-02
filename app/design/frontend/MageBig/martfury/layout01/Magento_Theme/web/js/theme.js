/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 &&
            $('#co-shipping-method-form .fieldset.rates :checked').length === 0
        ) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    $('.cart-summary').mage('sticky', {
        spacingTop: 80
    });

    keyboardHandler.apply();

    _toogleNav();

    function _toogleNav() {
        // button show hide menu mobile
        $('.btn-nav').on('click', function(event) {
            event.preventDefault();
            $('.overlay-contentpush').addClass('open');
            $('.page-wrapper').addClass('overlay-open');
            $('html').addClass('nav-open');
        });
        $('.nav-bar-wrap').on('click', function(event) {
            if (!$(event.target).closest('.nav-bar').length) {
                $('.overlay-contentpush').removeClass('open');
                $('.btn-nav').removeClass('active');
                $('.page-wrapper').removeClass('overlay-open');
                $('html').removeClass('nav-open');
            }
        });

        var toggles = document.querySelectorAll(".mb-toggle-switch");
        for (var i = toggles.length - 1; i >= 0; i--) {
            var toggle = toggles[i];
            toggleHandler(toggle);
        }
    }

    function toggleHandler(toggle) {
        toggle.addEventListener("click", function(e) {
            e.preventDefault();
            (this.classList.contains("active") === true) ? this.classList.remove("active"): this.classList.add("active");
        });
    }
});
