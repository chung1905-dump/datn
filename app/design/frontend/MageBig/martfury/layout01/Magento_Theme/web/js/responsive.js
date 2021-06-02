/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'matchMedia',
    'magnificpopup',
    'jquery-ui-modules/tooltip',
    'domReady!'
], function ($, mediaCheck) {
    'use strict';

    mediaCheck({
        media: '(min-width: 768px)',

        /**
         * Switch to Desktop Version.
         */
        entry: function () {
            var self = this;

            this._toolTip();

            $('body').on('contentUpdated', function () {
                self._toolTip();
            });
        },

        /**
         * Switch to Mobile Version.
         */
        exit: function () {
            $('.action.toggle.checkout.progress').on('click.gotoCheckoutProgress', function () {
                var myWrapper = '#checkout-progress-wrapper';

                scrollTo(myWrapper + ' .title');
                $(myWrapper + ' .title').addClass('active');
                $(myWrapper + ' .content').show();
            });

            $('body').on('click.checkoutProgress', '#checkout-progress-wrapper .title', function () {
                $(this).toggleClass('active');
                $('#checkout-progress-wrapper .content').toggle();
            });

            $('.btn-search-mobile > i').magnificPopup({
                items: {
                    src: '.top-search-wrap',
                    type: 'inline'
                },
                focus: 'input#search',
                mainClass: 'mfp-move-from-top search-popup',
                fixedContentPos: true,
                removalDelay: 300
            });

            if (!$('.toggle-mobile').find('.title .mbi').length) {
                $('.toggle-mobile').find('.title').append('<i class="mbi mbi-chevron-down"></i>');
            }
            $('.toggle-mobile').each(function () {
                if ($(this).hasClass('open')) {
                    $(this).find('.title .mbi').removeClass('mbi-chevron-down').addClass('mbi-chevron-up');
                    $(this).find('.content-toggle').show();
                }
            });
            $('.toggle-mobile .title .mbi').on('click', function () {
                $(this).parents('.toggle-mobile').removeClass('open');
                if ($(this).hasClass('mbi-chevron-down')) {
                    $(this).parents('.toggle-mobile').find('.content-toggle').slideDown();
                    $(this).removeClass('mbi-chevron-down').addClass('mbi-chevron-up');
                } else {
                    $(this).parents('.toggle-mobile').find('.content-toggle').slideUp();
                    $(this).removeClass('mbi-chevron-up').addClass('mbi-chevron-down');
                }
            });
        },

        _toolTip: function () {
            $('.mb-tooltip').tooltip({
                show: null,
                hide: {
                    delay: 250
                },
                position: {
                    my: "center bottom-30",
                    at: "center top",
                    using: function (position, feedback) {
                        $(this).css(position);
                        $(this).addClass("magebig-tooltip");
                    }
                },
                open: function (event, ui) {
                    ui.tooltip.addClass('in');
                },
                close: function (event, ui) {
                    ui.tooltip.removeClass('in');
                }
            });
        }
    });
});
