/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


deo_confirmOrderValidations['chronopost'] = function() { 
  if (
    typeof CHRONORELAIS_ID !== 'undefined' &&
    $('input[name=chronorelaisSelect]').length && 
    !$('input[name=chronorelaisSelect]:checked').length
    ) {
    var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
    shippingErrorMsg.append('<span class="err-chronopost-point-relais"> (choisir point relais)</span>');
    shippingErrorMsg.show();
    scrollToElement(shippingErrorMsg);
    return false; 
  } else {
    return true;
  }
}

checkoutShippingParser.chronopost = {
    init_once: function (elements) {
    },

    on_ready: function() {
        setTimeout(function(){
            if (typeof CHRONORELAIS_ID === 'undefined' || $('#chronorelais_container.moved:visible').length) { 
                return; 
            }
            $('#js-delivery span.custom-radio > input[type=radio], input[name=id_carrier]').click(function (e) {
                toggleRelaisMap($("#cust_address_clean").val(), $("#cust_codepostal").val(), $("#cust_city").val(), e);

                if (typeof CHRONORELAIS_ID != 'undefined' && parseInt($(this).val()) == CHRONORELAIS_ID) {
                    $('html, body').animate({
                        scrollTop: $('#hook-display-after-carrier').offset().top
                    }, 1500);
                }
            });
            toggleRelaisMap($("#cust_address_clean").val(), $("#cust_codepostal").val(), $("#cust_city").val());
            $('#changeCustCP').off('click').on('click', postcodeChangeEvent);
            $("#relais_codePostal").on('keypress keydown keyup', function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    e.stopPropagation();
                    postcodeChangeEvent();
                    return false;
                }
            });

            $('#chronorelais_container:not(.moved)').insertAfter($('#chronorelais_container').closest('.block-inner'));
            $('#chronorelais_container').addClass('moved');

        },300)
    },

    delivery_option: function (element) {
        element.append(' \
        <div id="checkout-delivery-step" class="-current hidden"></div> \
        <script> \
          $(document).ready( \
             checkoutShippingParser.chronopost.on_ready \
          ); \
        </script> \
    ');

    },

    extra_content: function (element) {
    }

}
