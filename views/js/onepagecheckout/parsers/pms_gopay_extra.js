/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

checkoutPaymentParser.pms_gopay_extra = {


    init_once: function (content, triggerElementName) {

    },

    container: function (element) {

    },

    all_hooks_content: function (content) {

    },

    form: function (element) {
        thisForm = element.find('form');
        newAction = thisForm.attr("action");
        serializedForm = thisForm.serialize();
        thisForm.attr('action', "javascript:inlineFunction('"+newAction+"', '"+serializedForm+"');");

    },

    on_ready: function () {

    },

    additionalInformation: function (element) {

    }

}