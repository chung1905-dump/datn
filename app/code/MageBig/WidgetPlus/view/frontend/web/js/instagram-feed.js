define([
    'jquery',
    'MageBig_WidgetPlus/js/owl.carousel-set'
], function($, owlWidget) {

    $.widget( "magebig.instagramWidget", {

        _create: function() {
            var self = this,
                config = this._render();

            this._setStream( config );

            if (this.options.owl.enable) {
                setTimeout(function () {
                    $(self.element).owlWidget(self.options.owl);
                }, 0);
            }
        },

        /**
         * Render photo stream
         *
         * @param data
         * @private
         */

        _render: function () {

            var html = '';

            $.each(this.options.data, function () {
                var img = this.thumbnail,
                    url = 'https://www.instagram.com/p/'+ this.link;

                html += '<div class="instagram-item">' +
                    '<a target="_blank" href="' + url +'">' +
                    '<img src="'+ img +'" alt=""/>'+
                    '</a>'+
                    '</div>';
            });

            return html;
        },


        /**
         * @param stream
         * @private
         */

        _setStream: function (stream) {
            $(this.element).find('.block-wrap').append(stream);
        }

    });

    return $.magebig.instagramWidget;

});
