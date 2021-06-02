var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'MageBig_WidgetPlus/js/swatch-renderer': true
            }
        }
    },
    map: {
        "*": {
            'owlWidget': 'MageBig_WidgetPlus/js/owl.carousel-set',
            'owlCarousel': 'MageBig_WidgetPlus/js/owl.carousel'
        }
    },
    shim: {
        'owlWidget': {
            deps: ['jquery', 'owlCarousel']
        },
        'owlCarousel': {
            deps: ['jquery']
        }
    }
};
