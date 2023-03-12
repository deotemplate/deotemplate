/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutShippingParser.posteitaliane = {
    init_once: function (elements) {
    },

    on_ready: function() {
        setTimeout(function(){
            if ('function' == typeof checkSelectedShippingMethod) {
                $('#js-delivery').on('change', 'input[name^=delivery_option]', function(e) {
                    checkSelectedShippingMethod();
                });
                checkSelectedShippingMethod();
            }
        },300)
    },

    delivery_option: function (element) {
        element.append(' \
        <script> \
          $(document).ready( \
             checkoutShippingParser.posteitaliane.on_ready \
          ); \
        </script> \
    ');

    },

    extra_content: function (element) {
    }

}
