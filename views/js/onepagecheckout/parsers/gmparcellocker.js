/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

deo_confirmOrderValidations['gmparcellocker'] = function () {
    if (
        $('.chosen-parcel:visible').length &&
        "---" == $('.chosen-parcel:visible').html()
    ) {
        var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
        shippingErrorMsg.text(shippingErrorMsg.text() + ' (InPost)');
        shippingErrorMsg.show();
        scrollToElement(shippingErrorMsg);
        return false;
    } else {
        return true;
    }
}