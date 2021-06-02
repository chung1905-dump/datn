define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.SwatchRenderer', widget, {
            /**
             * @private
             */
            _create: function () {
                var options = this.options,
                    gallery = $(options.mediaGallerySelector, '.column.main'),
                    productData = this._determineProductData(),
                    $main = productData.isInProductView ?
                        this.element.parents('.column.main') :
                        this.element.parents('.product-item-info');

                if (productData.isInProductView) {
                    gallery.data('gallery') ?
                        this._onGalleryLoaded(gallery) :
                        gallery.on('gallery:loaded', this._onGalleryLoaded.bind(this, gallery));
                } else {
                    options.mediaGalleryInitial = [{
                        'img': $main.find('.product-image-photo').attr('src')
                    }];
                }

                this.productForm = this.element.parents(this.options.selectorProductTile).find('form:first');
                this.inProductList = this.productForm.length > 0;
            },

            /**
             * Get new prices for selected options
             *
             * @returns {*}
             * @private
             */
            _getNewPrices: function () {
                var $widget = this,
                    optionPriceDiff = 0,
                    allowedProduct = this._getAllowedProductWithMinPrice(this._CalcProducts()),
                    optionPrices = this.options.jsonConfig.optionPrices,
                    basePrice = parseFloat(this.options.jsonConfig.prices.basePrice.amount),
                    optionFinalPrice,
                    newPrices;

                if (!_.isEmpty(allowedProduct)) {
                    optionFinalPrice = parseFloat(optionPrices[allowedProduct].finalPrice.amount);
                    optionPriceDiff = optionFinalPrice - basePrice;
                }

                if (optionPriceDiff !== 0) {
                    newPrices  = this.options.jsonConfig.optionPrices[allowedProduct];
                } else {
                    newPrices = $widget.options.jsonConfig.prices;
                }

                return newPrices;
            },

            /**
             * Update total price
             *
             * @private
             */
            _UpdatePrice: function () {
                var $widget = this,
                    $product = $widget.element.parents($widget.options.selectorProduct),
                    $productPrice = $product.find(this.options.selectorProductPrice),
                    result = $widget._getNewPrices(),
                    tierPriceHtml,
                    isShow;

                $productPrice.trigger(
                    'updatePrice',
                    {
                        'prices': $widget._getPrices(result, $productPrice.priceBox('option').prices)
                    }
                );

                var product_list_info = $widget.element.parents('.product-item-info');
                var product_view_info = $widget.element.parents('.catalog-product-view');
                var discount_elm = product_list_info.find('.discount-percent');
                var discount_view_elm = product_view_info.find('.product.media .discount-percent');

                if (typeof result != 'undefined' && result.oldPrice.amount !== result.finalPrice.amount) {
                    var discount_percent = (result.finalPrice.amount-result.oldPrice.amount)*100/result.oldPrice.amount;
                    var discount_text = discount_percent.toFixed(0)+'%';

                    if (product_list_info.length) {
                        if (discount_elm.length) {
                            discount_elm.show();
                            discount_elm.text(discount_text);
                        } else {
                            product_list_info.find('.product-item-photo').append('<span class="discount-percent">'+discount_text+'</span>');
                        }
                    }

                    if (product_view_info.length) {
                        if (discount_view_elm.length) {
                            discount_view_elm.show();
                            discount_view_elm.text(discount_text);
                        } else {
                            product_view_info.find('.product.media').append('<span class="discount-percent">'+discount_text+'</span>');
                        }
                    }

                    if (product_list_info.length) {
                        product_list_info.find(this.options.slyOldPriceSelector).show();
                    }

                    if (product_view_info.length) {
                        product_view_info.find(this.options.slyOldPriceSelector).show();
                    }

                } else {
                    if (product_list_info.length) {
                        product_list_info.find(this.options.slyOldPriceSelector).hide();
                    }

                    if (product_view_info.length) {
                        product_view_info.find(this.options.slyOldPriceSelector).hide();
                    }

                    $(this.options.slyOldPriceSelector).hide();

                    if (discount_elm.length) {
                        discount_elm.hide();
                    }

                    if (discount_view_elm.length) {
                        discount_view_elm.hide();
                    }
                }

                isShow = typeof result != 'undefined' && result.oldPrice.amount !== result.finalPrice.amount;

                $product.find(this.options.slyOldPriceSelector)[isShow ? 'show' : 'hide']();

                if (typeof result != 'undefined' && result.tierPrices && result.tierPrices.length) {
                    if (this.options.tierPriceTemplate) {
                        tierPriceHtml = mageTemplate(
                            this.options.tierPriceTemplate,
                            {
                                'tierPrices': result.tierPrices,
                                '$t': $t,
                                'currencyFormat': this.options.jsonConfig.currencyFormat,
                                'priceUtils': priceUtils
                            }
                        );
                        $(this.options.tierPriceBlockSelector).html(tierPriceHtml).show();
                    }
                } else {
                    $(this.options.tierPriceBlockSelector).hide();
                }

                $(this.options.normalPriceLabelSelector).hide();

                _.each($('.' + this.options.classes.attributeOptionsWrapper), function (attribute) {
                    if ($(attribute).find('.' + this.options.classes.optionClass + '.selected').length === 0) {
                        if ($(attribute).find('.' + this.options.classes.selectClass).length > 0) {
                            _.each($(attribute).find('.' + this.options.classes.selectClass), function (dropdown) {
                                if ($(dropdown).val() === '0') {
                                    $(this.options.normalPriceLabelSelector).show();
                                }
                            }.bind(this));
                        } else {
                            $(this.options.normalPriceLabelSelector).show();
                        }
                    }
                }.bind(this));
            }
        });

        return $.mage.SwatchRenderer;
    }
});
