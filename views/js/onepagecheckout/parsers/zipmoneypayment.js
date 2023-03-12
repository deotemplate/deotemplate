/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutPaymentParser.zipmoneypayment = {

    form: function (element) {
        let form = element.find('form');
        form.attr('action', 'javascript:checkoutPaymentParser.zipmoneypayment.confirm()');
    },

    additionalInformation: function (element) {
        var zip_pay_button = '<button id="zip-pay" style="display: none" />';
        element.append(zip_pay_button); 
    },

    confirm: function () {
        Zip.Checkout.attachButton('#zip-pay', {
            redirect: true,
            checkoutUri: "index.php?fc=module&module=zipmoneypayment&controller=payment",
            redirectUri: "index.php?fc=module&module=zipmoneypayment&controller=validation"
        });
        $("#zip-pay").click();
    }

}


