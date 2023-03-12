/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


jQuery.fn.putCursorAtEnd = function () {

  return this.each(function () {

    // Cache references
    var $el = $(this),
      el = this;

    // Only focus if input isn't already
    if (!$el.is(":focus")) {
      $el.focus();
    }

    // If this function exists... (IE 9+)
    if (el.setSelectionRange) {

      // Double the length because Opera is inconsistent about whether a carriage return is one character or two.
      var len = $el.val().length * 2;

      // Timeout seems to be required for Blink
      setTimeout(function () {
        el.setSelectionRange(len, len);
      }, 1);

    } else {

      // As a fallback, replace the contents with itself
      // Doesn't work in Chrome, but Chrome supports setSelectionRange
      $el.val($el.val());

    }

    // Scroll to the bottom, in case we're in a tall textarea
    // (Necessary for Firefox and Chrome)
    this.scrollTop = 999999;

  });

};

String.prototype.toCapitalize = function () {
  return this.toLowerCase().replace(/^.|\s\S/g, function (a) {
    return a.toUpperCase();
  });
}

function insertUrlParam(key, value) {
  key = encodeURI(key);
  if (typeof value !== 'undefined') {
    value = encodeURI(value);
  }

  var kvp = document.location.search.substr(1).split('&');

  var i = kvp.length;
  var x;
  while (i--) {
    x = kvp[i].split('=');

    if (x[0] == key) {
      x[1] = value;
      if (typeof value !== 'undefined') {
        kvp[i] = x.join('=');
      }
      break;
    }
  }

  if (i < 0) {
    var new_index = kvp.length;
    kvp[new_index] = key;
    if (typeof value !== 'undefined') {
      kvp[new_index] += '=' + value;
    }
  }

  //this will reload the page, it's likely better to store this until finished
  return '?' + kvp.join('&').replace(/^&/, '');
}

function deo_helper_validateEmail(email) {
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}
