/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'magnificpopup',
    'domReady!'
], function ($) {
    "use strict";
    $.widget('custom.magebig', {
        options: {
            sticky_header: false
        },
        _create: function() {
            if (this.options.sticky_header) {
                this._stickyMenu();
            }

            this._minicartSidebar();
            this._stickyAddCart();
            this._goTop();

            if ($('.header.links').find('a').length > 2) {
                $('.user-topbar > i').on('click', function (event) {
                    event.preventDefault();
                    $.magnificPopup.open({
                        items: {
                            src: '.header.links',
                            type: 'inline'
                        },
                        overflowY: 'auto',
                        fixedContentPos: true,
                        removalDelay: 300,
                        mainClass: 'mfp-slide-right'
                    });
                });
            } else {
                $('.user-topbar > i').on('click', function (event) {
                    event.preventDefault();
                    if ($('.authorization-link a').length) {
                        $('.authorization-link a')[0].click();
                    }
                });
            }

            if ($('.filter-mobile-btn').length) {
                $('#maincontent').append('<button type="button" class="filter-btn d-none"></button>');
                $('.filter-mobile-btn').on('click', function () {
                    $('.filter-btn').trigger('click');
                });
                $('body').on('contentUpdated', function () {
                    $('.filter-mobile-btn').on('click', function () {
                        $('.filter-btn').trigger('click');
                    });
                });
                $('.filter-btn').magnificPopup({
                    preloader: false,
                    mainClass: 'mfp-slide-right',
                    fixedContentPos: true,
                    removalDelay: 300,
                    items: {
                        src: '.sidebar-wrap',
                        type: 'inline'
                    },
                    callbacks: {
                        open: function() {
                            $('.filter-options-item .nano').nanoScroller();
                        }
                    }
                });
            }
        },
        _minicartSidebar: function() {
            $('.action.showcart').magnificPopup({
                preloader: false,
                mainClass: 'mfp-slide-right cart-modal',
                fixedContentPos: true,
                closeOnBgClick: true,
                removalDelay: 300,
                items: {
                    src: '.minicart-slide',
                    type: 'inline'
                }
            });
            $('[data-block="minicart"]').on('contentLoading', function () {
                $(this).on('contentUpdated', function ()  {
                    $('.action.showcart').trigger('click');
                });
            });
        },
        _goTop: function() {
            var $goTop = $('#go-top');
            if ($goTop.length) {
                $goTop.hide();
                $(window).scroll(function() {
                    if ($(this).scrollTop() > 100) {
                        $goTop.fadeIn();
                    } else {
                        $goTop.fadeOut();
                    }
                });
                $goTop.click(function() {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);
                    return false;
                });
            }
        },
        _stickyMenu: function() {
            var $stickyMenu = $('.sticky-menu');
            if ($stickyMenu.length > 0) {
                $stickyMenu.wrap('<div class="sticky-wrap"></div>');
                $stickyMenu.parent().css('min-height', $stickyMenu.outerHeight());
                var threshold = $stickyMenu.height() + $stickyMenu.offset().top;
                $(window).scroll(function() {
                    var $win = $(this);
                    var curWinTop = $win.scrollTop();
                    if (curWinTop > threshold) {
                        $stickyMenu.addClass('active');
                    } else {
                        $stickyMenu.removeClass('active');
                    }
                });

                var timer = false;
                $(window).resize(function () {
                    if (timer) clearTimeout(timer);
                    timer = setTimeout(function(){
                        $stickyMenu.parent().css('min-height', $stickyMenu.outerHeight());
                    }, 1000);
                });
            }
        },
        _stickyAddCart: function() {
            var $stickyAddCart = $('#product_addtocart_form .box-tocart');

            if ($stickyAddCart.length > 0) {
                $stickyAddCart.wrap('<div class="sticky-addcart-wrap"><div class="sticky-addcart"></div></div>');
                $('.sticky-addcart-wrap').css('min-height', $stickyAddCart.outerHeight());
                var threshold = $stickyAddCart.outerHeight() + $stickyAddCart.offset().top;
                var pagetitle = $('.page-title-wrapper.product').clone();

                var desc = $('#tab-label-description-title').clone().attr('id', 'stick-info-1');
                var addi = $('#tab-label-additional-title').clone().attr('id', 'stick-info-2');
                var review = $('#tab-label-reviews-title').clone().attr('id', 'stick-info-3');

                $(window).scroll(function() {
                    var $win = $(this);
                    var curWinTop = $win.scrollTop();
                    if (curWinTop > threshold) {
                        $('.sticky-addcart').addClass('active');
                        $stickyAddCart.addClass('container');

                        if (!$stickyAddCart.find('.page-title-wrapper').length) {
                            $stickyAddCart.prepend(pagetitle);
                            if (!$('.stick-info').length) {
                                pagetitle.append('<div class="stick-info"></div>');
                                $('.stick-info').append(desc).append(addi).append(review);

                                $('#stick-info-1').on('click', function (e) {
                                    e.preventDefault();
                                    $('#tab-label-description-title').trigger('click');
                                    $('html,body').animate({
                                        scrollTop: $('#tab-label-description-title').offset().top - 70
                                    }, 'slow');
                                })
                                $('#stick-info-2').on('click', function (e) {
                                    e.preventDefault();
                                    $('#tab-label-additional-title').trigger('click');
                                    $('html,body').animate({
                                        scrollTop: $('#tab-label-additional-title').offset().top - 70
                                    }, 'slow');
                                })
                                $('#stick-info-3').on('click', function (e) {
                                    e.preventDefault();
                                    $('#tab-label-reviews-title').trigger('click');
                                    $('html,body').animate({
                                        scrollTop: $('#tab-label-reviews-title').offset().top - 70
                                    }, 'slow');
                                })
                            }
                        }
                    } else {
                        $('.sticky-addcart').removeClass('active');
                        $stickyAddCart.removeClass('container');
                    }
                });

                var timer = false;
                $(window).resize(function () {
                    if (timer) clearTimeout(timer);
                    timer = setTimeout(function(){
                        $('.sticky-addcart-wrap').css('min-height', $stickyAddCart.outerHeight());
                    }, 1000);
                });
            }

            $('.box-tocart .tocart').on('click', function () {
                if ($('#product-options-wrapper').length) {
                    $('html,body').animate({
                        scrollTop: $('#product-options-wrapper').offset().top - 80
                    }, 'slow');
                }
            });
        }
    });
    return $.custom.magebig;
});
