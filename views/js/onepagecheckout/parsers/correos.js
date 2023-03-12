/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


checkoutShippingParser.correos = {
  init_once: function (elements) {

  },

  correos_init: function () {
    // Select the node that will be observed for mutations
    var targetNode = document.getElementsByClassName('carrier-extra-content correos')[0];

    // Options for the observer (which mutations to observe)
    var config = {attributes: true, childList: false, subtree: false};

    var alreadyInit = false;

    // Callback function to execute when mutations are observed
    var callback = function (mutationsList, observer) {
      if (!alreadyInit) {
        for (var i = 0; i < mutationsList.length; i++) {
          var mutation = mutationsList[i];
          if (mutation.type == 'childList') {
            console.log('A child node has been added or removed.');
          } else if (mutation.type == 'attributes' && $(targetNode).is(":visible") && !alreadyInit) {
            console.log('The ' + mutation.attributeName + ' attribute was modified.');
            Correos.checkOfficeContent();
            alreadyInit = true;
            break;
            observer.disconnect();
          }
        }
      }
    };

    // Create an observer instance linked to the callback function
    var observer = new MutationObserver(callback);

    // Start observing the target node for configured mutations
    observer.observe(targetNode, config);
  },

  delivery_option: function (element) {
    // Init Correos map widget
    element.after("<script>checkoutShippingParser.correos.correos_init();</script>");
  },

  extra_content: function (element) {
  }

}
