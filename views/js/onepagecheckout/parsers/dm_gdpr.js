/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */


if (!$('.form-group.password:visible').length) {
    $('#deoonepagecheckout-account .form-group.dm_gdpr_active').hide();
}

$('.form-group.dm_gdpr_active label').addClass('required');

deo_confirmOrderValidations['dm_gdpr'] = function() {
  $('.form-group.dm_gdpr_active .error-msg').remove();
  if (
      $('.form-group.dm_gdpr_active input[type=checkbox]:visible').length  &&
      !$('.form-group.dm_gdpr_active input[type=checkbox]').is(':checked')
  ) {
    $('.form-group.dm_gdpr_active label').after('<div class="field error-msg">'+i18_requiredField+'</div>');
    scrollToElement($('.form-group.dm_gdpr_active'));
    return false;
  } else {
    return true;
  }
}


 