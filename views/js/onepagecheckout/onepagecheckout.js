/**
 *  @author    BvpTheme <bvptheme@gmail.com>
 *  @copyright by BvpTheme
 *  @license   http://bvptheme.com - prestashop template provider
 */

// checkoutPaymentParser could be set from other (payment) modules, so let's do not reset it here
if ('undefined' === typeof checkoutPaymentParser) {
	var checkoutPaymentParser = {};
}
var checkoutShippingParser = {};

var deo_confirmOrderValidations = {};

var deo_updatePaymentWithShipping = true;

// markup added to .block-inner of checkout blocks on updateHtml, while waiting for ajax response
var deo_loaderHtml = '\
	<div class="deo-ajax-opc-loading">\
		<div class="loading-icon"></div>\
	</div>';

// 'dirty' flag to mark checkout form prepared to be payment-confirmed, without submitting data again.
var paymentConfirmationPrepared = false;
var paymentLoaderMaxTime = 3000;
$(document).ready(function () {
	$('body').addClass('document-ready');
	initBlocksSelectors();
	getShippingAndPaymentBlocks();
	//getCartSummary();

	setAddressFieldsCountryCSS();

	// Remove default checkout handlers (e.g. country change)
	$('body').off('change', '.js-country');

	// Set handlers
	// Check if email was registered
	$('body').on('change', '#deoonepagecheckout-account [name="email"]', function (e) {
		checkEmail('form.account-fields', $(this));
	});

	$('body').on('click', '#deo_save_account', function (e) {
		$(this).addClass('loading');
		checkEmail('form.account-fields', $(this));
	});

	$('body').on('change', '.opc-element input.-error, .opc-element select.-error', function () {
		$(this).removeClass('-error').addClass('-former-error');
		checkAndHideGlobalError();
	});

	$('body').on('change', '#js-delivery input', function () {
		selectDeliveryOption($('#js-delivery')); // delivery form object as parameter
	});

	$('body').on('change', '.js-gift-checkbox', function () {
		toggleGiftMessage();
	});

	$('body').on('blur', '#gift_message', function () {
		selectDeliveryOption($('#js-delivery'));
	});

	$('body').on('blur', '#delivery_message', function () {
		setDeliveryMessage();
	});

	$('body').on('click', '[data-link-action="deo-delete-from-cart"]', function () {
		deleteFromCart($(this));
		return false;
	});

	$('body').on('click', '[data-link-action="deo-sign-in"]', function () {
		signIn();
		return false;
	});

	$('body').on('click', '[data-link-action="deo-confirm-order"]', function () {
		confirmOrder($(this));
		return false;
	});

	$('body').on('click', '[data-link-action="deo-save-account-overlay"]', function () {
		confirmOrder($(this));
		return false;
	});

	$('body').on('click', '[data-link-action="deo-add-voucher"]', function () {
		addVoucher();
		return false;
	});

	$('body').on('click', '[data-link-action="deo-remove-voucher"]', function () {
		removeVoucher($(this).data());
		return false;
	});

	$('body').on('change', '[data-link-action="deo-create-account"]', function () {
		if ($(this).prop('checked')) {
			$('#deoonepagecheckout-account .form-group.password, #deoonepagecheckout-account .form-group.dm_gdpr_active').slideDown('fast', function () {
				$(this).removeClass('hidden')
			});
		} else {
			$('#deoonepagecheckout-account .form-group.password, #deoonepagecheckout-account .form-group.dm_gdpr_active').slideUp('fast');
		}
		return false;
	});

	$('body').on('change', '[data-link-action="deo-ship-to-different-address"]', function () {

		if ($('#deoonepagecheckout-address-delivery').is(':visible')) {
			$(this).prop('checked', false);
			$('#deoonepagecheckout-address-delivery').hide(10, function () {
				//modifyAccountAndAddress($('#deoonepagecheckout-address-invoice [name=id_country]'));
				modifyAddressSelection('delivery');
			});

		} else {
			$(this).prop('checked', true);
			$('#deoonepagecheckout-address-delivery').show(10, function () {
				//modifyAccountAndAddress($('#deoonepagecheckout-address-delivery [name=id_country]'));
				modifyAddressSelection('delivery');
			});

		}
		return false;
	});

	$('body').on('change', '[data-link-action="deo-bill-to-different-address"]', function () {
		if ($('#deoonepagecheckout-address-invoice').is(':visible')) {
			$(this).prop('checked', false);
			$('#deoonepagecheckout-address-invoice').hide(10, function () {
				modifyAddressSelection('invoice');
				//modifyAccountAndAddress($('#deoonepagecheckout-address-delivery [name=id_country]'));
			});
		} else {
			$(this).prop('checked', true);
			$('#deoonepagecheckout-address-invoice').show(10, function () {
				modifyAddressSelection('invoice');
				//modifyAccountAndAddress($('#deoonepagecheckout-address-invoice [name=id_country]'));
			});
		}
		return false;
	});

	$('body').on('change', '.deo-invoice-addresses', function () {
		modifyAddressSelection('invoice');
		return false;
	});

	$('body').on('change', '.deo-delivery-addresses', function () {
		modifyAddressSelection('delivery');
		return false;
	});

	$('body').on('change', '[data-link-action="deo-i-am-business"]', function () {
		var businessFieldsSelector = '#deoonepagecheckout-address-invoice .form-group.business-field';
		var businessDisabledFieldsSelector = '#deoonepagecheckout-address-invoice .form-group.business-disabled-field';
		if ($(this).prop('checked')) {
			if ($('[data-link-action=deo-i-am-private]').prop('checked')) {
				$('[data-link-action=deo-i-am-private]').prop('checked', false).change();
			}
			$(businessFieldsSelector).not('.hidden').show();
			$('.business-fields-separator').css('display', 'block');
			$(businessDisabledFieldsSelector).hide();
		} else {
			$(businessFieldsSelector + ', .business-fields-separator').not('.need-dni').hide();
			$(businessDisabledFieldsSelector).not('.hidden').show();
		}
		if ($(businessFieldsSelector + ' .live').length) {
			modifyAccountAndAddress($(businessFieldsSelector + ' .live').first());
		}
		if ($('#dni-placeholder').length && $('.business-field.dni').length) {
			swapElements($('#dni-placeholder'), $('.business-field.dni'));
		}

		return false;
	});

	$('body').on('change', '[data-link-action="deo-i-am-private"]', function () {
		var privateFieldsSelector = '#deoonepagecheckout-address-invoice .form-group.private-field';
		var privateDisabledFieldsSelector = '#deoonepagecheckout-address-invoice .form-group.private-disabled-field';
		if ($(this).prop('checked')) {
			if ($('[data-link-action="deo-i-am-business"]').prop('checked')) {
				$('[data-link-action="deo-i-am-busines"s]').prop('checked', false).change();
			}
			$(privateFieldsSelector).not('.hidden').show();
			$('.private-fields-separator').css('display', 'block');
			$(privateDisabledFieldsSelector).hide();
		} else {
			$(privateFieldsSelector + ', .private-fields-separator').not('.need-dni').hide();
			$(privateDisabledFieldsSelector).not('.hidden').show();
		}
		if ($(privateFieldsSelector + ' .live').length) {
			modifyAccountAndAddress($(privateFieldsSelector + ' .live').first());
		}
		if ($('#dni-placeholder-private').length && $('.private-field.dni').length) {
			swapElements($('#dni-placeholder-private'), $('.private-field.dni'));
		}

		return false;
	});


	$('body').on('click', '[data-link-action="toggle-password-visibility"]', function () {
		var input = $(this).closest('label').find('input');
		if (input.attr("type") == "password") {
			input.attr("type", "text");
		} else {
			input.attr("type", "password");
		}
		return false;
	});

	$('body').on('click', '[data-link-action="deo-add-new-address"]', function () {
		$(this).parent('.customer-addresses').find('.addresses-selection')
			.removeClass('hidden')
			.find('select').val(-1).trigger('change');
		$(this).hide();
		return false;
	});


	var quantityInputFieldTimeout;

	$('body').on('input', '[data-link-action="deo-update-cart-quantity"]', function () {
		if (quantityInputFieldTimeout) {
			clearTimeout(quantityInputFieldTimeout);
		}
		var el = $(this);
		var timeout = (1 == $(this).data('no-wait')) ? 0 : 500;
		quantityInputFieldTimeout = setTimeout(function () {
			updateQuantityFromInput(el);
		}, timeout);
		return false;
	});
	$('body').on('click', '[data-link-action="deo-update-cart-quantity-up"]', function (e) {
		e.preventDefault();
		var inputEl = $(this).parent().find('[data-link-action="deo-update-cart-quantity"]');
		inputEl.val(parseInt(inputEl.val()) + 1).data('no-wait', 1).trigger('input');
		return false;
	});
	$('body').on('click', '[data-link-action="deo-update-cart-quantity-down"]', function (e) {
		e.preventDefault();
		var inputEl = $(this).parent().find('[data-link-action="deo-update-cart-quantity"]');
		if (parseInt(inputEl.attr('min')) < parseInt(inputEl.val()))
			inputEl.val(parseInt(inputEl.val()) - 1).data('no-wait', 1).trigger('input');
		return false;
	});

	// Remove errors from checkboxes on their modification
	$('body').on('change', '.form-group.checkbox input[type=checkbox], [data-link-action="deo-create-account"]', function () {
		$(this).closest('.form-group').find('.field.error-msg').remove();
		checkAndHideGlobalError();
		modifyCheckboxOption($(this));
	});

	$('body').on('change', 'input[id^=conditions_to_approve]', function () {
		$(this).closest('.terms-and-conditions').find('.error-msg').hide();
		checkAndHideGlobalError();
		modifyCheckboxOption($(this));
	});

	$('body').on('change', 'input[name=id_gender]', function () {
		$(this).closest('.form-group').find('.field.error-msg').remove();
		checkAndHideGlobalError();
		modifyRadioOption($(this));
	});

	$('body').on('click', '.change-login', function () {
		$('#deo-register-box').removeClass('active').addClass('next');
		$('#deo-login-box').removeClass('prev').addClass('active');
	});

	$('body').on('click', '.change-register', function () {
		$('#deo-login-box').removeClass('active').addClass('prev');
		$('#deo-register-box').removeClass('next').addClass('active');
	});

	// On *any* modification, hide binary payment and let user save again
	$('body').on('change', 'input', function () {
		payment.hideBinary();
		setConfirmationDirty();
	});

	// triggering 'change' events earlier then on focusOut
	var deo_fieldChangeObserverTimeout = {};
	var deo_inputTriggerChangeTimeoutMillis = 1500;
	$('body').on('input', '.opc-element .text input, .opc-element .tel input', function () {
		$self = $(this);
		clearTimeout(deo_fieldChangeObserverTimeout[$self.attr('name')]);
		deo_fieldChangeObserverTimeout[$self.attr('name')] =
			setTimeout(function () {
				$self.trigger('change')
			}, deo_inputTriggerChangeTimeoutMillis);
	});
	$('body').on('change', '.opc-element .text input', function () {
		$self = $(this);
		clearTimeout(deo_fieldChangeObserverTimeout[$self.attr('name')]);
	});

	$('body').on('change', '[name=firstname], [name=lastname], [name=address1], .orig-field[name=city]', function () {
		$(this).val($(this).val().toCapitalize());
		// In firstname and lastname, as preventive measure, replace dots that are not followed
		// by spaces, with dot+space, so that customer_firstname and customer_lastname validation passes properly.
		if ($(this).attr('name').match(/.*?tname/)) {
			$(this).val(jQuery.trim($(this).val().replace(/\.\s*/g, '. ')));
		}
	});

	$('body').on('change', '[name=postcode], [name=vat_number]', function () {
		var t_fieldVal = jQuery.trim($(this).val().toUpperCase());
		// remove spaces for vat_number and for postcode only when enabled in settings
		if ('postcode' !== $(this).attr('name') || config_postcode_remove_spaces) {
			t_fieldVal = t_fieldVal.replace(/\s|\./g, '');
		}
		$(this).val(t_fieldVal);
	});

	$('body').on('change', '.address-fields .js-country', function () {
		setAddressFieldsCountryCSS();
	});

	var liveFieldTimeout;

	// On these fields modification, address shall be stored and carriers / payments reloaded
	// Register it at the end, so that the other fields-modifications take place earlier
	$('body').on('change', '.live', function () {

		// FIX for autofill, which triggers modifyAccount multiple times in short span of time
		// First, let's wait a moment and execute only last call

		if (liveFieldTimeout) {
			clearTimeout(liveFieldTimeout);
		}
		$self = $(this);

		// In certain cases, make full page reload
		// This will be on rare occasions, so we can allow a fixed timeout here
		setTimeout(function () {
			if ('id_country' === $self.attr('name') && installedModules['mondialrelay']) {
				window.location.reload(true);
			}
		}, 2000);

		// if change() is triggered by leaving the field focus, let's clear timeout that watches
		// this field and triggers change() after pre-set time
		if ("undefined" !== typeof deo_fieldChangeObserverTimeout &&
			"number" === typeof deo_fieldChangeObserverTimeout[$self.attr('name')]) {
			clearTimeout(deo_fieldChangeObserverTimeout[$self.attr('name')]);
		}

		var timeout = 20;
		liveFieldTimeout = setTimeout(function () {
			modifyAccountAndAddress($self);
		}, timeout);
		return false;


	});


	// Show password "red_eye" iconds to switch between password and text field
	$('[data-link-action="toggle-password-visibility"]').removeClass('hidden');

	// $(document).ajaxError(function myErrorHandler(event, xhr, ajaxOptions, thrownError) {
	// 	console.info("Ajax error \n\nDetails:\nError thrown: " + thrownError + "\n" +
	// 		'event: ');
	// 	console.info(event);
	// 	console.info("\n" + 'xhr: ');
	// 	console.info(xhr);
	// 	console.info("\n" + 'ajaxOptions: ');
	// 	console.info(ajaxOptions);
	// });

	// Modal window on terms and conditions link click
	$('body').on('click', '.js-terms a', function (event) {
		event.preventDefault();
		var e = $(event.target).closest('a').attr("href");
		e && (e += "?content_only=1",
			$.get(e, function (event) {
				$("#deo-modal-terms").find(".js-modal-content").html($(event).find("[class*=page-cms]:first").contents())
			}).fail(function (event) {
				// l.default.emit("handleError", {
				//     eventType: "clickTerms",
				//     resp: t
				// })
				console.info("terms load failed, check the URL is valid: "+e);
			})),
		$("#deo-modal-terms").modal("show");
	});

	promoteBusinessAndPrivateFields();

	// It's also necessary for modume cgma to comment out /modules/cgma/cgma.php, around line 216: Tools::redirect($base . $this->getCartSummaryURL());
	deo_confirmOrderValidations['cgma_minimal_order_amount_by_customer_groups'] = function () {
		if (
			$('.cart-summary #cgma_errors').is(':visible')
		) {
			scrollToElement($('.cart-summary #cgma_errors').first());
			return false;
		} else {
			return true;
		}
	};

	// Register global events on every ajax request and watch out for property 'customPropAffectedBlocks'
	// in $.ajax settings; and for such property, display loader animation.
	if (config_blocks_update_loader) {
		$(document).ajaxSend(function (event, jqxhr, settings) {
			if ('undefined' !== typeof settings.customPropAffectedBlocks) {
				// append loader right before update
				$(settings.customPropAffectedBlocks).find('.block-inner').prepend(deo_loaderHtml);
				// Attach also loading-remove handler, when (this) ajax is finished
				jqxhr.always(function() {
					$(settings.customPropAffectedBlocks).find('.block-inner > .deo-ajax-opc-loading').remove(); 
					$('.popup-payment-content > .deo-ajax-opc-loading').remove(); 
				});
			}
		});
	}
});

function initBlocksSelectors() {
	shippingBlockElement = $('#deoonepagecheckout-shipping .block-inner');
	paymentBlockElement = $('#deoonepagecheckout-payment .block-inner .dynamic-content');
	cartSummaryBlockElement = $('#deoonepagecheckout-cart-summary .block-inner');
	invoiceAddressBlockElement = $('#deoonepagecheckout-address-invoice');
	deliveryAddressBlockElement = $('#deoonepagecheckout-address-delivery');
}

// On init, and on country change, set data-iso-code attribute on address fields, so that we can modify
// checkout form (address section) with CSS rules, based on selected country
function setAddressFieldsCountryCSS() {
	$('.address-fields .js-country option:selected').each(function () {
		$(this).closest('.address-fields').attr('data-iso-code', $(this).data('iso-code'));
	})
}

function formatErrors(errors, tag) {
	if ('undefined' === typeof tag) {
		tag = 'div';
	}
	var errMsg = "";
	$.each(errors, function (index, value) {
		if ("" !== jQuery.trim(value)) {
			errMsg += "<" + tag + ">";
			if ("" !== jQuery.trim(index) && isNaN(index)) {
				errMsg += index + ': ';
			}
			errMsg += value + "</" + tag + ">\n";
		}
	});
	return errMsg;
}

function checkAndHideGlobalError() {
	if (0 == $('.field.error-msg:visible').length) {
		$('#deo-payment-confirmation > .error-msg').hide();
	}
}

function showGlobalError() {
	$('#deo-payment-confirmation > .error-msg').show();
	scrollToError();
}

function scrollToError() {
	scrollToElement($('.error-msg:visible').closest('.form-group'));
}

function scrollToElement(element) {
	var scrollOffset = ("undefined" !== typeof globalScrollOffset) ? globalScrollOffset : -100;
	if (element.length) {
		var actions = computeScrollIntoView(element.get(0), {
			behavior: 'smooth',
			scrollMode: 'if-needed',
			block: 'center'
		});
		if ("undefined" !== typeof actions[0]) {
			window.scrollTo({
				top: actions[0].top - scrollOffset,
				behavior: "smooth"
			});
		}
	}
}

function showError(element) {
	$(element).show();
}

function hideError(element) {
	$(element).hide();
	checkAndHideGlobalError();
}

function removeError(element) {
	$(element).remove();
	checkAndHideGlobalError();
}

// Modify checkout option (typically checkbox) and send it to backend to be remembered in session (cookie)
function modifyCheckboxOption(element) {
	$.ajax({
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=modifyCheckboxOption" +
			"&name=" + element.attr('name') +
			"&isChecked=" + element.is(':checked') +
			"&token=" + static_token,
		success: function (jsonData) {

		}
	});
}

// Modify checkout option (typically checkbox) and send it to backend to be remembered in session (cookie)
function modifyRadioOption(radioElements) {
	var elName = radioElements.attr('name');
	var checkedElement = $('[name=' + elName + ']:checked');
	$.ajax({
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=modifyRadioOption" +
			"&name=" + checkedElement.attr('name') +
			"&checkedValue=" + checkedElement.val() +
			"&token=" + static_token,
		success: function (jsonData) {

		}
	});
}

function printContextErrors(blockSel, errors, triggerElement, dontShowGlobal) {

	var highlightOnElements = [];
	if ("undefined" !== typeof triggerElement
		&& !isMainConfirmationButton(triggerElement)
		&& !isSaveAccountOverlayConfirmation(triggerElement)) {
		highlightOnElements.push(triggerElement.attr('name'));
		removeError(blockSel + ' [name=' + triggerElement.attr('name') + '] ~ .field.error-msg');
		// With country change, re-validate postcode, if it's filled in
		if ("id_country" === triggerElement.attr('name') && "" != $(blockSel + ' [name=postcode]').val()) {
			highlightOnElements.push('postcode');
			$(blockSel + ' [name=postcode]').removeClass('-error');
			removeError(blockSel + ' [name=postcode] ~ .field.error-msg');
		}
	} else {
		removeError(blockSel + ' .field.error-msg');
		$(blockSel + ' .error').removeClass('-error');
	}

	$.each(errors, function (index, value) {
		if ("" !== jQuery.trim(value) && (0 == highlightOnElements.length || highlightOnElements.indexOf(index) > -1)) {
			$(blockSel + ' [name=' + index + ']').addClass('-error');
			if ($(blockSel + ' [name=' + index + ']').is(':checkbox') || $(blockSel + ' [name=' + index + ']').is(':radio')) {
				$(blockSel + ' [name=' + index + ']').closest('.form-group').append('<div class="field error-msg">' + value + '</div>');
			}else if ($(blockSel + ' [name=' + index + ']').hasClass('js-visible-password')) {
				$(blockSel + ' [name=' + index + ']').closest('.form-group').append('<div class="field error-msg">' + value + '</div>');
			} else {
				$(blockSel + ' [name=' + index + ']').after('<div class="field error-msg">' + value + '</div>');
			}

			if (0 == highlightOnElements.length && ('undefined' === typeof dontShowGlobal || !dontShowGlobal)) {
				showGlobalError();
			}
		}
	});

}

function swapElements(el1, el2) {
	var tempNode = $('<div id="swap-elements-temp"></div>');
	el1.after(tempNode);
	el2.after(el1);
	tempNode.after(el2);
	tempNode.remove();
}

function promoteBusinessAndPrivateFields() {
	// Group and put in front the business fields, if "I am a business" checkbox is ticked
	if (config_show_i_am_business) {
		// Special treatment of .need-dni, which can be displayed for consumer and business, but on different position
		if ($('.business-field.dni').length) {
			$('.business-field.dni').after('<div id="dni-placeholder"></div>');
		}
	}

	if (config_show_i_am_private) {
		if ($('.private-field.dni').length) {
			$('.private-field.dni').after('<div id="dni-placeholder-private"></div>');
		}
	}
	if (config_show_i_am_business) {
		// To save the order of fields, we'd create placeholder and move the placeholder only to business section
		// After #i_am_business is ticked, placeholder will be replaced by field and field by placeholder
		$('#deoonepagecheckout-address-invoice .form-group.business-field, #dni-placeholder').not('.dni').prependTo($('.business-fields-container'));
	}
	if (config_show_i_am_private) {
		$('#deoonepagecheckout-address-invoice .form-group.private-field, #dni-placeholder-private').not('.dni').prependTo($('.private-fields-container'));
	}

	// If company fields are filled in (and thus #i_am_business ticked), we'll right away swap .need-dni with placeholder
	if ($('#i_am_business').is(':checked')) {
		swapElements($('#dni-placeholder'), $('.business-field.dni'));
	}

	$('#i_am_business').prop('disabled', false);

	if ($('#i_am_private').is(':checked')) {
		swapElements($('#dni-placeholder-private'), $('.private-field.dni'));
	}

	$('#i_am_private').prop('disabled', false);
}

function addVoucher() {
	// url - implicitly using current
	$.ajax({
		customPropAffectedBlocks: '#deoonepagecheckout-shipping, #deoonepagecheckout-payment, #deoonepagecheckout-cart-summary',
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=addVoucher" +
			"&addDiscount=1" +
			"&discount_name=" + $('.cart-grid [name=discount_name]').val() +
			"&token=" + static_token,
		success: function (jsonData) {

			if (jsonData.hasErrors) {

				var errMsg = formatErrors(jsonData.cartErrors, 'span');
				$('.promo-code > .alert-danger > .js-error-text').html(errMsg);
				$('.promo-code > .alert-danger').slideDown();

			} else {
				updateCheckoutBlocks(jsonData, true, true, deo_updatePaymentWithShipping);
			}


		}
	});
}

function removeVoucher(data) {
	$.ajax({
		customPropAffectedBlocks: '#deoonepagecheckout-shipping, #deoonepagecheckout-payment, #deoonepagecheckout-cart-summary',
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=removeVoucher" +
			"&deleteDiscount=" + data["discountId"] +
			"&token=" + static_token,
		success: function (jsonData) {

			if (jsonData.hasErrors) {

				var errMsg = formatErrors(jsonData.cartErrors, 'span');
				$('.promo-code > .alert-danger > .js-error-text').html(errMsg);
				$('.promo-code > .alert-danger').slideDown();

			} else {
				updateCheckoutBlocks(jsonData, true, true, deo_updatePaymentWithShipping);
			}


		}
	});
}

/* Prepare checkout form, so that once payment methods are loaded in payment block, they can be used immediately */
function prepareConfirmOrder() {

}

function confirmOrder(confirmButtonEl) {
	// typically, shipping modules can attach to deo_confirmOrderValidations, their respective
	// callbacks will be called here and should they not pass, order confirmation will be stopped
	var validationFailed = false;

	// clear shipping error before validations
	$('#deoonepagecheckout-shipping .error-msg').hide();

	$.each(deo_confirmOrderValidations, function (validationName, validationCallback) {
		if (!validationCallback()) {
			validationFailed = true;
		}
	});
	if (validationFailed) {
		showGlobalError();
		return;
	}

	modifyAccountAndAddress(confirmButtonEl, function (jsonData) {
		// callback method, called when account/address validation was successful

		// check selected carrier and payment method (additionally if they have some selection requirements)
		var selectedDeliveryEl = $('[name^=delivery_option]:checked');
		var selectedPaymentEl = $('[name=payment-option]:checked');
		var cartSummaryErrorVisible = $('#deoonepagecheckout-cart-summary .error-msg:visible').length;

		if (!selectedDeliveryEl.length && !jsonData.isVirtualCart) {
			var shippingErrorMsg = $('#deoonepagecheckout-shipping > .block-inner > .error-msg');
			shippingErrorMsg.show();
			scrollToElement(shippingErrorMsg);
			showGlobalError();
			return;
		}

		if (!selectedPaymentEl.length && !config_separate_payment) {
			var paymentErrorMsg = $('#deoonepagecheckout-payment > .block-inner .error-msg');
			paymentErrorMsg.show();
			scrollToElement(paymentErrorMsg);
			showGlobalError();
			return;
		}

		if (cartSummaryErrorVisible) {
			showGlobalError();
			return;
		}

		// Do we have any unchecked T&C?
		if ($('input[id^=conditions_to_approve]').not(':checked').length) {
			$('.terms-and-conditions > .error-msg').show();
			showGlobalError();
			return;
		}
		// Confirmation processing effect

		showConfirmButtonLoader(confirmButtonEl, true);
		// should there be an issue in Payment method form, handled by payment module only, rather safely set
		// timeout to hide loader after few seconds.
		if (confirmButtonEl.attr('data-link-action') == 'deo-save-account-overlay'){
			setTimeout(function () {
				hideConfirmButtonLoader(confirmButtonEl)
			}, paymentLoaderMaxTime);
		}
		if (isMainConfirmationButton(confirmButtonEl)) {
			if (!config_separate_payment) {
				payment.confirm();
			} else {
				window.location.href = insertUrlParam(separate_payment_key);
			}

			// Maybe: for some payment modules, call confirmButtonEl.find('button').click();
		} else {
			// binary payment method, just hide save account overlay
			payment.hideSaveAccountOverlay();
		}


	});
}


function updateBlockCart(el){
	let data = el.data();
	let refreshURL = $('.blockcart').data('refresh-url');
	let requestData = {};
	if (event && event.reason && typeof event.resp !== 'undefined' && !event.resp.hasError) {
		requestData = {
			id_customization: data["idCustomization"],
			id_product_attribute: data["idProductAttribute"],
			id_product: data["idProduct"],
			action: 'add-to-cart'
		};
	}
	if (event && event.resp && event.resp.hasError) {
		prestashop.emit('showErrorNextToAddtoCartButton', { errorMessage: event.resp.errors.join('<br/>')});
	}
	$.post(refreshURL, requestData).then(function (resp) {
		let html = $('<div />').append($.parseHTML(resp.preview));
		$('.blockcart').replaceWith($(resp.preview).find('.blockcart'));
	}).fail(function (resp) {
		prestashop.emit('handleError', { eventType: 'updateShoppingCart', resp: resp });
	});
}

function updateQuantityFromInput(el) {
	var data = el.data();
	qtyWanted = parseInt(el.val());
	qtyChange = qtyWanted - parseInt(data["qtyOrig"]);
	if (isNaN(qtyWanted) || isNaN(qtyChange)) {
		return;
	}
	data["qtyOrig"] = qtyWanted; // To allow rapid type-in changes in input field, e.g. modifying from single digit to 2-digit number
	if (qtyWanted < 1 || qtyChange == 0) {
		return;
	}
	el.prop('disabled', true);

	// AWP module support (also template - cart-detailed-product-line.tpl - modification is necessary!)
	var awpSpecialInstructions = data.updateUrl.match('special_instructions.*');
	var additionalData = (awpSpecialInstructions)?'&'+awpSpecialInstructions:'';

	// url - implicitly using current
	$.ajax({
		customPropAffectedBlocks: '#deoonepagecheckout-shipping, #deoonepagecheckout-payment, #deoonepagecheckout-cart-summary',
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=updateQuantity" +
			"&update=1" +
			"&qty=" + Math.abs(qtyChange) +
			"&op=" + ((qtyChange > 0) ? "up" : "down") +
			"&id_product=" + data["idProduct"] +
			"&id_product_attribute=" + data["idProductAttribute"] +
			"&id_customization=" + data["idCustomization"] +
			"&token=" + static_token + additionalData,
		success: function (jsonData) {

			// Removed, 5.6.2019: Now errors will go directly to cart-summary.tpl
			// $('#deoonepagecheckout-cart-summary > .error-msg').remove();
			// if (jsonData.hasErrors) {
			//   var errMsg = formatErrors(jsonData.cartErrors, 'span');
			//   $('#deoonepagecheckout-cart-summary').prepend('<div class="error-msg">' + errMsg + '</div>')
			//   $('#deoonepagecheckout-cart-summary > .error-msg').show();
			// }

			updateCheckoutBlocks(jsonData, true, true, deo_updatePaymentWithShipping);
			updateBlockCart(el);
		}
	});
}

function modifyAddressSelection(addressType) {
	// Send to server information about expanded/collapsed second address
	// And additionaly ID of selected address from combobox (for logged-in users)
	var addressesDropdown = $('.deo-' + addressType + '-addresses');
	var newAddressId = 0;
	if (addressesDropdown.length) {
		newAddressId = addressesDropdown.val();
	}

	$.ajax({
		customPropAffectedBlocks: '#deoonepagecheckout-shipping, #deoonepagecheckout-payment, #deoonepagecheckout-cart-summary, #deoonepagecheckout-address-' + addressType,
		url: insertUrlParam('modifyAddressSelection'),
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=modifyAddressSelection" +
			"&addressType=" + addressType +
			"&addressId=" + newAddressId +
			"&invoiceVisible=" + $('#deoonepagecheckout-address-invoice form:visible').length +
			"&deliveryVisible=" + $('#deoonepagecheckout-address-delivery form:visible').length +
			"&token=" + $('#deoonepagecheckout-account [name=token]').val(),
		success: function (jsonData) {
			updateAddressBlock(addressType, jsonData.newAddressBlock, jsonData.newAddressSelection);
			updateCheckoutBlocks(jsonData, true, true, deo_updatePaymentWithShipping);
		}
	});


	// Returned value - whole address block; simply replace, and also update other blocks - cart, shipping, payment

	// for non-logged in users, simply call modifyAccountAndAddress
	// modifyAccountAndAddress($('#deoonepagecheckout-address-' + addressType + ' [name=id_country]'));
}

function showConfirmButtonLoader(buttonEl, showLoadingAnimation) {
	if (showLoadingAnimation) {
		buttonEl.addClass('loading')
	}
	buttonEl.prop('disabled', true);
}

function hideConfirmButtonLoader(buttonEl) {
	buttonEl.removeClass('loading').prop('disabled', false);
}

function isMainConfirmationButton(element) {
	return ("deo-confirm-order" === element.data()["linkAction"]);
}

function isSaveAccountOverlayConfirmation(element) {
	return ("deo-save-account-overlay" === element.data()["linkAction"]);
}

function checkEmail(accountFormSelector, triggerEl, callback) {
	// url - implicitly using current
	// fix potential email errors (accented chars)

	// Commented out, as it did not work for accented domains (which are allowed, and in chrome replaced to xn-- format
	// var unaccented = element.val().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
	// if (element.val() !== unaccented || element.val().match('xn--')) {
	//     element.val(unaccented);
	// }
	$.ajax({
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=checkEmail" +
			"&triggerEl=" + encodeURIComponent(triggerEl.attr('id')) +
			"&account=" + serializeVisibleFields(accountFormSelector) +
			"&token=" + $('#deoonepagecheckout-account [name=token]').val(),
		success: function (jsonData) {
			if (jsonData.hasErrors) {
				blockSel = '.account-fields';
				printContextErrors(blockSel, jsonData.errors, undefined, true);
			} else {
				updateAccountToken(jsonData.newToken);
				updateStaticToken(jsonData.newStaticToken);
				// if out of some reason, shipping/payment blocks are still disallowed, maybe entering email would allow them (e.g. if forced-email-overlay was active)
				if ($('.waiting-block').length) {
					getShippingAndPaymentBlocks();
				}

				displayStaticCustomerInfoAndNav(jsonData.customerSignInArea);
				updateCheckoutBlocks(jsonData, true, true, deo_updatePaymentWithShipping);
			}
			// call 'callback' method to let caller know we're ready
			if ('function' === typeof callback) {
				callback(jsonData);
			}
			triggerEl.removeClass('loading');
		}
	});
}

function updateNoticeStatus(status) {
	if ('undefined' === typeof status) {
		status = '-';
	}
	// url - implicitly using current
	$.ajax({
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=saveNoticeStatus" +
			"&noticeStatus=" + status +
			"&token=" + $('#deoonepagecheckout-account [name="token"]').val(),
		success: function (jsonData) {
			if (jsonData.hasErrors) {
				console.info('notice status update failed');
			} else {
				console.info('notice status update succeeded');
			}
		}
	});
}

function updateAccountToken(token) {
	if ("undefined" !== typeof token) {
		$('#deoonepagecheckout-account input[type="hidden"][name="token"]').val(token);
	}
}

function updateStaticToken(token) {
	if ("undefined" !== typeof token) {
		static_token = token;
		if ('undefined' !== typeof prestashop) {
			prestashop.static_token = token;
		}
	}
}

function serializeVisibleFields(formSelector) {
	return encodeURIComponent($(formSelector).find('input:visible, [type="hidden"], .orig-field:visible').serialize());
}

function setConfirmationDirty() {
	// Check, if everything 'required' to trigger confirmation is filled in
	// If yes, then trigger it, but without visual feedback

	paymentConfirmationPrepared = false;
}

function setConfirmationPrepared() {
	paymentConfirmationPrepared = true;
}

function displayStaticCustomerInfoAndNav(customerSignInArea)
{
	if ('undefined' !== typeof customerSignInArea) {
		if ('undefined' !== typeof customerSignInArea.staticCustomerInfo) {
			$('#static-customer-info-container').replaceWith(customerSignInArea.staticCustomerInfo);

			// Disable email field and hide password when somebody logged in
			$('.account-fields').find('.firstname, .lastname, .password, .email').hide();
			$('#deoonepagecheckout-login-form, #create_account, .form-group.dm_gdpr_active').hide();
			$('body').addClass('logged-in');
		}

		if ('undefined' !== typeof customerSignInArea.displayNav2) {
			var userInfoEl = null;
			if ($('#_desktop_user_info').length) {
				userInfoEl = $('#_desktop_user_info');
			} else if ($('.userinfo-selector.popup-over').length) {
				userInfoEl = $('.userinfo-selector.popup-over');
			} else if ($('#header .user-info').length) {
				userInfoEl = $('#header .user-info');
			} else if ($('.quick_login.dropdown_wrap').length) {
				userInfoEl = $('.quick_login.dropdown_wrap');
			}
			if (null !== userInfoEl) {
				userInfoEl.replaceWith(customerSignInArea.displayNav2);
			}
		}
	}
}


function modifyAccountAndAddress(triggerElement, callback) {
	var triggerSection = triggerElement.closest('.opc-element').attr('id');
	// url - implicitly using current


	if ('prepare_confirmation' == triggerElement.attr('id')) {
		// after calling modifyAccountAndAddress($('#prepare_confirmation'), setConfirmationPrepared);
		triggerSection = 'deoonepagecheckout-prepare-confirmation';
		// Disable (silently) confirmation button
		$('[data-link-action="deo-confirm-order"]').prop('disabled', true).css('cursor', 'wait');

	} else if (paymentConfirmationPrepared && isMainConfirmationButton(triggerElement)) {
		// do not repeat Ajax request, if form wasn't modified since last data refresh and just call callback()
		if ("function" === typeof callback) {
			callback();
			return;
		}
	} else if (isSaveAccountOverlayConfirmation(triggerElement)) {
		showConfirmButtonLoader($('[data-link-action="deo-save-account-overlay"]'), true);
	} else {
		// Add loader (2nd param) only when confirmation button was pressed by user; otherwise, just disable button for a moment
		showConfirmButtonLoader($('[data-link-action="deo-confirm-order"]'), isMainConfirmationButton(triggerElement));
	}

	// Extra fields added through hooks, tepmplate updates or JS injections
	var extraAccountAndAddressFields = $('.account-fields, .address-fields').find('input, select, textarea').not('.orig-field').not('.not-extra-field');
	var extraAccountParams = '';

	if (extraAccountAndAddressFields.length) {
		extraAccountAndAddressFields.each(function () {
			extraAccountParams += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
		})
	}

	// Exceptions for certain modules, that hooks in checkout fields, but need field to be sent separately
	var extraAccountSeparateFields = $('#deoonepagecheckout-account [type="checkbox"]').not('[name="optin"]').not('[name="create-account"]');

	if (extraAccountSeparateFields.length) {
		extraAccountSeparateFields.each(function () {
			extraAccountParams += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
		})
	}

	// Module sponsorship: allinonerewards sponsorship field support
	if ($('input[name="sponsorship"]').length) {
		extraAccountParams += '&sponsorship=' + encodeURIComponent($('input[name="sponsorship"]').val());
	}

	$.ajax({
		customPropAffectedBlocks: '#deoonepagecheckout-shipping, #deoonepagecheckout-payment, #deoonepagecheckout-cart-summary',
		url: insertUrlParam('modifyAccountAndAddress'),
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "modifyAccountAndAddress&ajax_request=1&action=modifyAccountAndAddress&trigger=" + triggerSection +
			"&account=" + serializeVisibleFields('form.account-fields') +
			"&invoice=" + encodeURIComponent($('#deoonepagecheckout-address-invoice form :visible').serialize()) +
			"&delivery=" + encodeURIComponent($('#deoonepagecheckout-address-delivery form :visible').serialize()) +
			"&passwordVisible=" + $('#deoonepagecheckout-account input[name="password"]:visible').length +
			"&passwordRequired=" + $('#deoonepagecheckout-account input[name="create-account"]:checked').length +
			"&invoiceVisible=" + $('#deoonepagecheckout-address-invoice form:visible').length +
			"&deliveryVisible=" + $('#deoonepagecheckout-address-delivery form:visible').length +
			"&token=" + $('#deoonepagecheckout-account [name="token"]').val() +
			extraAccountParams,
		success: function (jsonData) {

			var noErrors = true;
			// We can't clean all errors here, e.g. if we're updating delivery address only and have errors in invoice
			// this would clean also invoice errors (which we don't wont unless we update invoice address too)



			// Go through account, invoice and delivery errors, show them all
			if ("undefined" !== typeof jsonData.account && null !== jsonData.account) {
				blockSel = '.account-fields';
				printContextErrors(blockSel, jsonData.account.errors);

				if (jsonData.account.hasErrors) {
					// account.errors could contain also firstname / lastname errors, in that case, we need to push this to
					// invoice address error highlight also
					var customerProps = ['firstname', 'lastname'];
					var customerProp;
					for (ci = 0; ci < customerProps.length; ci++) {
						customerProp = customerProps[ci];
						if ('undefined' !== typeof jsonData.account.errors &&
							'undefined' !== typeof jsonData.invoice &&
							null !== jsonData.invoice &&
							'undefined' !== typeof jsonData.invoice.errors &&
							'' != jsonData.account.errors[customerProp] &&
							0 == $('.account-fields input[name='+customerProp+']:visible').length) {
							jsonData.invoice.errors[customerProp] = jsonData.account.errors[customerProp];
						}
					}

					noErrors = false;
				} else {
					// Update token only when customer account ID or password is changed
					displayStaticCustomerInfoAndNav(jsonData.customerSignInArea);

					updateAccountToken(jsonData.account.newToken);
					updateStaticToken(jsonData.account.newStaticToken);

				}
			}// End of jsonData.account handling

			if ("undefined" !== typeof jsonData.invoice && null !== jsonData.invoice) {
				blockSel = '#deoonepagecheckout-address-invoice';
				printContextErrors(blockSel, jsonData.invoice.errors, triggerElement);

				if (jsonData.invoice.hasErrors) {
					noErrors = false;
				}
			}

			if ("undefined" !== typeof jsonData.delivery && null !== jsonData.delivery) {
				blockSel = '#deoonepagecheckout-address-delivery';
				printContextErrors(blockSel, jsonData.delivery.errors, triggerElement);

				if (jsonData.delivery.hasErrors) {
					noErrors = false;
				}
			}

			// Handle states and refresh blocks regardless of errors status
			if ("deoonepagecheckout-address-invoice" === triggerSection || "deoonepagecheckout-address-invoice" === triggerSection) {
				var addressType = triggerSection.substring("deoonepagecheckout-address-".length);
				if ('undefined' !== typeof jsonData[addressType].states) {
					handleStates($('[id=' + triggerSection + '] [name="id_state"]'), jsonData[addressType].states);
				}
				if ('undefined' !== typeof jsonData[addressType].needZipCode) {
					handlePostcode($('[id=' + triggerSection + '] [name="postcode"]'), jsonData[addressType].needZipCode);
				}
				if ('undefined' !== typeof jsonData[addressType].needDni) {
					handleNeedDni($('[id=' + triggerSection + '] [name="dni"]'), jsonData[addressType].needDni);
				}
				if ('undefined' !== typeof jsonData[addressType].callPrefix) {
					handleCallPrefix($('[id=' + triggerSection + '] [name^="phone"]'), jsonData[addressType].callPrefix);
				}
			}

			updateCheckoutBlocks(jsonData, true, true, deo_updatePaymentWithShipping);

			hideConfirmButtonLoader($('[data-link-action="deo-confirm-order"]'));
			hideConfirmButtonLoader($('[data-link-action="deo-save-account-overlay"]'));

			if ("undefined" !== typeof jsonData.shippingErrors && null !== jsonData.shippingErrors && "undefined" !== typeof jsonData.shippingErrors.errors) {
				var errorsTxt = jsonData.shippingErrors.errors.join(', ');
				$('<div class="error-msg shipping-errors">'+errorsTxt+'</div>').prependTo($('#deoonepagecheckout-shipping .block-inner')).show();
				noErrors = false;
				showGlobalError();
			}

			if ('deoonepagecheckout-prepare-confirmation' == triggerSection) {
				$('[data-link-action=""deo-confirm-order""]').prop('disabled', false).css('cursor', 'pointer');
			}

			if (noErrors && "function" === typeof callback) {
				callback(jsonData);
			}

		}
	});
}

function signedInUpdateForm() {
	$('[data-link-action="deo-sign-in"], .forgot-password').hide();
	$('.successful-login.hidden').show();
	// simply reload the checkout page with new context; take care of cart/checkout redirection, do not display
	// cart summary again!
	window.location.reload();
}

function signIn() {
	// recover from (possible) previous login attempts
	$('#errors-login-form').slideUp();
	$('[data-link-action="deo-sign-in"]').prop('disabled', true).css('cursor', 'wait');

	// url - implicitly using current
	$.ajax({
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=signIn&" +
			$('#login-form').serialize() +
			"&token=" + $('#deoonepagecheckout-account [name="token"]').val(),
		success: function (jsonData) {

			$('[data-link-action="deo-sign-in"]').prop('disabled', false).css('cursor', 'pointer');

			if (jsonData.hasErrors) {

				var errMsg = formatErrors(jsonData.errors);

				$('#errors-login-form').html(errMsg).slideDown();

			} else {
				signedInUpdateForm();
			}

		}
	});
}

function deleteFromCart(el) {
	// AWP module support (also template - cart-detailed-product-line.tpl - modification is necessary!)
	let data = el.data();
	let additionalData = '';
	if ('undefined' !== typeof data.deleteUrl) {
		let awpSpecialInstructions = data.deleteUrl.match('special_instructions.*');
		additionalData = (awpSpecialInstructions)?'&'+awpSpecialInstructions:'';
	}
	
	// url - implicitly using current
	$.ajax({
		customPropAffectedBlocks: '#deoonepagecheckout-shipping, #deoonepagecheckout-payment, #deoonepagecheckout-cart-summary',
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=deleteFromCart" +
			"&delete=1" +
			"&id_product=" + data["idProduct"] +
			"&id_product_attribute=" + data["idProductAttribute"] +
			"&id_customization=" + data["idCustomization"] +
			"&token=" + static_token + additionalData,
		success: function (jsonData) {
			updateCheckoutBlocks(jsonData, true, true, deo_updatePaymentWithShipping);
			updateBlockCart(el);
		}
	});
}

// Fill in states to combobox after address change/update
function handleStates(selectEl, states) {

	var oldVal = selectEl.val();
	//var shallResetPointer = selectEl.find('option:selected').index() > states.length;
	selectEl.children('option:not(:first)').remove();

	$.each(states, function (i, item) {
		if ("1" === item.active) {
			$(selectEl).append($('<option>', {
				value: item.id_state,
				text: item.name
			}));
		}
	});

	if (selectEl.find('option[value="' + oldVal + '"]').length) {
		selectEl.val(oldVal);
	} else {
		selectEl.val(null);
	}


	if (states.length > 0) {
		selectEl.closest('.form-group').show();
	} else {
		selectEl.closest('.form-group').hide();
	}
}

// Show/hide postcode input field based on country selected
function handlePostcode(postcodeEl, needZipCode) {
	if (needZipCode) {
		postcodeEl.closest('.form-group').show();
	} else {
		postcodeEl.closest('.form-group').hide();
	}
}

// Show/hide DNI input field based on country selected (we'll done with CSS)
function handleNeedDni(dniEl, needDni) {
	if (needDni) {
		dniEl.closest('.form-group').addClass('need-dni').show();
	} else {
		dniEl.closest('.form-group').removeClass('need-dni');
	}
}

function handleCallPrefix(phoneFieldsEl, callPrefix) {

	phoneFieldsEl.each(function () {
		$(this).closest('label').find('.country-call-prefix').html('+' + callPrefix);
	});

}

function parseShippingMethods(shippingModulesList, html) {

	var parsers = {};
	var doParse = false;
	$.each(shippingModulesList, function (moduleName, deliveryOptionId) {
		if ("undefined" !== typeof checkoutShippingParser[moduleName]) {
			parsers[moduleName] = checkoutShippingParser[moduleName];

			if (
				"undefined" !== typeof parsers[moduleName].init_once ||
				"undefined" !== typeof parsers[moduleName].delivery_option ||
				"undefined" !== typeof parsers[moduleName].extra_content ||
				"undefined" !== typeof parsers[moduleName].all_hooks_content) {
				doParse = true;
			}

		}
	});

	if (doParse) {
		var parsed = $('<div id="shipping-parser-wrapper">' + html + '</div>');

		$.each(parsers, function (moduleName, parser) {

			// Call once per payment module
			if ("undefined" !== typeof parser.init_once) {
				parser.init_once($('.delivery-option.' + moduleName + ', .carrier-extra-content.' + moduleName, parsed));
			}

			// Call once per payment option (payment module may have multiple options)
			$('.delivery-option.' + moduleName, parsed).each(function (i, containerSelector) {
				if ("undefined" !== typeof parser['delivery_option']) {
					parser['delivery_option']($(containerSelector));
				}
			});
			$('.carrier-extra-content.' + moduleName, parsed).each(function (i, containerSelector) {
				if ("undefined" !== typeof parser['extra_content']) {
					parser['extra_content']($(containerSelector));
				}
			});

			if ("undefined" !== typeof parser.all_hooks_content) {
				//parser.all_hooks_content($('>*:last-child', parsed));
				parser.all_hooks_content(parsed);
			}

		});
		html = parsed.html();
	}

	return html;
}

function afterPaymentLoadCallbacks(paymentModulesList, html, triggerElementName) {
	$.each(paymentModulesList, function (key, moduleName) {
		if ("undefined" !== typeof checkoutPaymentParser[moduleName]) {
			if (
				"undefined" !== typeof checkoutPaymentParser[moduleName].after_load_callback
			) {
				//setTimeout(checkoutPaymentParser[moduleName].after_load_callback, 200);
				checkoutPaymentParser[moduleName].after_load_callback();
			}

		}
	});
}


function parsePaymentMethods(paymentModulesList, html, triggerElementName) {

	var parsers = {};
	var doParse = false;
	$.each(paymentModulesList, function (key, moduleName) {
		if ("undefined" !== typeof checkoutPaymentParser[moduleName]) {
			parsers[moduleName] = checkoutPaymentParser[moduleName];

			if (
				"undefined" !== typeof parsers[moduleName].init_once ||
				"undefined" !== typeof parsers[moduleName].container ||
				"undefined" !== typeof parsers[moduleName].additionalInformation ||
				"undefined" !== typeof parsers[moduleName].form ||
				"undefined" !== typeof parsers[moduleName].all_hooks_content) {
				doParse = true;
			}

		}
	});

	if (doParse) {
		var parsed = $('<div id="payments-parser-wrapper">' + html + '</div>')

		$.each(parsers, function (moduleName, parser) {

			// Call once per payment module
			if ("undefined" !== typeof parser.init_once) {
				parser.init_once($('.deo-main-payment[data-payment-module="' + moduleName + '"]', parsed), triggerElementName);
			}

			// Call once per payment option (payment module may have multiple options)
			$('.deo-main-payment[data-payment-module="' + moduleName + '"] .payment-option', parsed).each(function (i, containerSelector) {

				var optId = $(containerSelector).attr('id').slice(0, -10); // remove '-container' suffix

				// we need to prepare 3 selectors: container, additionalInformation, form
				var selectors = {
					container: $(containerSelector),
					additionalInformation: $(containerSelector).nextAll('[id*=' + optId + '-additional-information]'),
					form: $(containerSelector).nextAll('[id*=' + optId + '-form]')
				};

				$.each(selectors, function (sectionName, element) {
					if ("undefined" !== typeof parser[sectionName]) {
						parser[sectionName](element, triggerElementName);
					}
				});

				if ("undefined" !== typeof parser.all_hooks_content) {
					//parser.all_hooks_content($('>*:last-child', parsed));
					parser.all_hooks_content(parsed);
				}
			});
		});
		html = parsed.html();
	}


	return html;
}

var shippingBlockChecksum = 0;
var paymentBlockChecksum = 0;
var cartSummaryBlockChecksum = 0;

var shippingBlockElement = '';
var paymentBlockElement = '';
var cartSummaryBlockElement = '';
var invoiceAddressBlockElement = '';
var deliveryAddressBlockElement = '';

var deliveryOptionSelector = '[type="radio"][name^="delivery_option"]:checked';

function updateHtmlBlock(el, html) {
	el.html(html);
}

function updateShippingBlock(shippingModulesList, html, checksum, triggerElementName) {
	if ('undefined' !== html && null !== html && shippingBlockChecksum != checksum) {
		html = parseShippingMethods(shippingModulesList, html);
		updateHtmlBlock(shippingBlockElement, html);

		shippingBlockChecksum = checksum;

		// Some shipping modules are not extra carriers (in modules list), so we cannot parse
		// them based on their name and thus need general trick to trigger their JS methods.
		// E.g. packzkomaty (sensbitpaczkomatymap) needs to trigger radio button change in order
		// to display list of pickup points; Chronopost and Mondial relay need it as well
		// forceRefreshShipping: If ="1", it will always reload shipping methods, so we need to avoid triggering click to avoid endless loop
		if ($(deliveryOptionSelector).length && !payment.isConfirmationTrigger(triggerElementName) && !forceRefreshShipping) {
			$(deliveryOptionSelector).prop('checked', false).trigger('click');
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
	//return value says if shipping method's 'setDeliveryMethod' might have been called
}

function selectPaymentOption(optionId, paymentFee) {

	// url - implicitly using current
	$.ajax({
		customPropAffectedBlocks: '#deoonepagecheckout-cart-summary',
		url: insertUrlParam('selectPaymentOption'),
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "optionId=" + optionId + "&payment_fee=" + paymentFee + "&ajax_request=1&action=selectPaymentOption" + "&token=" + static_token,
		success: function (jsonData) {

			updateCartSummaryBlock(jsonData.cartSummaryBlock, jsonData.cartSummaryBlockChecksum);

		}
	});
}

// payment options is klarnapayments_pay_later_module, but payment module name is klarnapaymentsofficial
var doNotRefreshOnPaymentMethods = ['klarnapayments', 'pts_stripe', 'paypal_plus'];

function updatePaymentBlock(paymentModulesList, html, checksum, triggerElementName) {
	if ('undefined' !== html && null !== html && (paymentBlockChecksum != checksum || 'deoonepagecheckout-confirm' == triggerElementName)) {

		// Temporarily store: a/ selected payment method; b/ filled-in fields

		// get selected options prior to refresh; firstly try from data-module-name, if unavailable, try element id
		var selectedOption = payment.getSelectedOptionModuleName();

		if ("undefined" !== typeof selectedOption && "" !== selectedOption) {
			selectedOption = '[data-module-name="' + selectedOption + '"]'; // select by module-name
		} else {
			selectedOption = payment.getSelectedOption();
			if ("undefined" !== typeof selectedOption && "" !== selectedOption) {
				selectedOption = '#' + payment.getSelectedOption(); // select by ID
			} else {
				selectedOption = "#none";
			}

		}

		// save payment form text input fields and select boxes, so we can restore them after hook update
		var payment_fields_values = {};
		// Shall be input[type=hidden] added here? It did not work with add_gopay_new
		// then, we need an exception: .not('[data-payment-module=add_gopay_new] input[type=hidden]')
		// Exception for hidden fields: input[name="issuer"] = mollie payments
		paymentBlockElement.find('input[type="text"], select, input[name="issuer"], form input[type="radio"]:checked').each(function () {
			if ("undefined" !== typeof $(this).attr('id') && !$(this).is(':radio')) {
				payment_fields_values['[id="' + $(this).attr('id') + '"]'] = $(this).val();
			} else if ("undefined" !== typeof $(this).attr('name')) {
				payment_fields_values['[name="' + $(this).attr('name') + '"]'] = $(this).val();
			}
		});

		// Store iframe payment forms, if they are already pre-filled and do not need to reload when user data changes
		// if ($('#deoonepagecheckout-payment #stripe-payment-form').length) {
		//   $('#payment_forms_persistence #stripe-payment-form').remove();
		//   $('#deoonepagecheckout-payment #stripe-payment-form').appendTo($('#payment_forms_persistence'));
		// }

		html = parsePaymentMethods(paymentModulesList, html, triggerElementName);

		// Make exception for some modules that init some code directly in payment hook
		var shallSkip = false;
		if (
			paymentBlockChecksum == checksum &&
			'deoonepagecheckout-confirm' == triggerElementName
		) {
			$.each(doNotRefreshOnPaymentMethods, function (index, value) {
				if (selectedOption.match(value)) {
					shallSkip = true;
				}
			});
		}

		if (shallSkip) {
			return;
		}

		updateHtmlBlock(paymentBlockElement, html);
		paymentBlockChecksum = checksum;

		afterPaymentLoadCallbacks(paymentModulesList, html, triggerElementName);

		// restore payment for input and select fields values
		$.each(payment_fields_values, function (index, value) {
			if ($(index).is(':radio')) {
				$(index+'[value='+value+']').prop('checked', true);
			} else {
				$(index).val(value);
			}
		});

		// Special molliepayments update - where we need to restore not only input/select value, but also special <button> (which replaces dropdown)
		if ($('#mollie-issuer-dropdown-button').length && $('input[name="issuer"]').length && '' != $('input[name="issuer"]').val()) {
			var selectedMolliePayment = $('input[name="issuer"]').val(); 
			var aMollieEl = $('a[data-ideal-issuer="'+selectedMolliePayment+'"]');
			if (aMollieEl.length) {
				$('#mollie-issuer-dropdown-button').text(aMollieEl.text());
			}
		}        
	}

	// Init PS Checkout render
	// This will happen always after appending the payment options HTML.
	// If this variable doesn't exist or is not true at this moment,
	// it means that ps_checkout is not loaded as module.
	// if (window.deo_ps_checkout.init) {
	//     window.ps_checkout.renderCheckout();
	// }

	var paymentBlockUpdated = false;

	// Reinit payments always, as there might have been no change in markup but we still need to update
	// COD amount in shopping cart
	if (!config_separate_payment) {
		payment.init(selectedOption, selectPaymentOption); // from file payment.js
		paymentBlockUpdated = true;
	}

	return paymentBlockUpdated;
}

function updateCartSummaryBlockAndRestoreInputs(cartSummaryBlockElement, html, activeQtyButtonCls, qtyControl) {
	updateHtmlBlock(cartSummaryBlockElement, html);
	// Restore focused input field, if any
	if ('undefined' !== typeof activeQtyButtonCls) {
		// Active element could be quantity up/down link
		$('[data-qty-control="' + qtyControl + '"] .' + activeQtyButtonCls).focus();
	} else {
		// or, input field
		if ('undefined' !== typeof jQuery && 'undefined' !== typeof jQuery.fn.putCursorAtEnd && $('[data-qty-control="' + qtyControl + '"] input').length)
			$('[data-qty-control="' + qtyControl + '"] input').putCursorAtEnd().focus();
	}
}

function updateCartSummaryBlock(html, checksum) {
	if ('undefined' !== html && null !== html && cartSummaryBlockChecksum != checksum) {

		// We try to store either focused or disabled input element (i.e. user was just making modifications there)
		// Later on, we'll try to re-focus.
		var activeEl = $(document.activeElement);
		var el = activeEl;
		if (el.is('.cart-line-product-quantity-up') || el.is('.cart-line-product-quantity-down')) {
			// Active element could be quantity up/down link
			var qtyControl = el.parent().data('qty-control');
			var activeQtyButtonCls = el.attr('class');
		} else if (el.is('input.cart-line-product-quantity')) {
			var qtyControl = el.parent().data('qty-control');
		} else {
			el = $('input.cart-line-product-quantity:disabled');
			var qtyControl = el.parent().data('qty-control');
		}



		// ! This is async, and with error message, we need sync information about error, so we need to disable refresh_minicart temporarily if we observe .error-msg in cart summary
		// if (config_refresh_minicart && !$(html).find('.error-msg').length) {
		//     prestashop.on('updatedCart', function () {
		//         updateCartSummaryBlockAndRestoreInputs(cartSummaryBlockElement, html, activeQtyButtonCls, qtyControl);
		//     });
		//     // For Panda themes, 'updatedCart' event is not being emitted; instead 'stUpdatedCart' is.
		//     prestashop.on('stUpdatedCart', function () {
		//         updateCartSummaryBlockAndRestoreInputs(cartSummaryBlockElement, html, activeQtyButtonCls, qtyControl);
		//     });
		// } else {
			updateCartSummaryBlockAndRestoreInputs(cartSummaryBlockElement, html, activeQtyButtonCls, qtyControl);
		// }
		
		if ('undefined' !== typeof prestashop && 'undefined' !== typeof  prestashop.emit) {
			prestashop.emit('deoonepagecheckout_updateCart', {
				reason: 'update',
			});
		}

		cartSummaryBlockChecksum = checksum;
	}
}

function updateAddressBlock(addressType, html, htmlAddressDropdown) {
	if ('undefined' !== html && null !== html) {
		if ("invoice" === addressType) {
			updateHtmlBlock(invoiceAddressBlockElement, html);

		} else {
			updateHtmlBlock(deliveryAddressBlockElement, html);
		}
	}
	if ('undefined' !== htmlAddressDropdown && null !== htmlAddressDropdown) {
		if ("invoice" === addressType) {
			deliveryAddressBlockElement.find('.customer-addresses').replaceWith(htmlAddressDropdown);
		} else {
			invoiceAddressBlockElement.find('.customer-addresses').replaceWith(htmlAddressDropdown);
		}
	}

	$(document).trigger('deoonepagecheckout_Address_Modified');
	promoteBusinessAndPrivateFields();
	setAddressFieldsCountryCSS();
}

function updateCheckoutBlocks(jsonData, updateSummary, updateShipping, updatePayment) {
	if ("undefined" !== typeof jsonData.emptyCart && jsonData.emptyCart === true) {
		$('body').addClass('is-empty-cart');
	}
	
	if ("undefined" !== typeof jsonData.isVirtualCart && jsonData.isVirtualCart === true) {
		$('body').addClass('is-virtual-cart');
	} else {
		$('body').removeClass('is-virtual-cart');
	}

	if ("undefined" !== typeof jsonData.minimalPurchaseError && jsonData.minimalPurchaseError === true) {
		$('#confirm_order .minimal-purchase-error-msg').html(jsonData.minimalPurchaseMsg);
		$('#confirm_order').addClass('minimal-purchase-error');
	} else {
		$('#confirm_order').removeClass('minimal-purchase-error');
	}
	var shippingBlockUpdated = false;
	var paymentBlockUpdated = false;

	if ('undefined' !== typeof updateShipping && updateShipping) {
		shippingBlockUpdated = updateShippingBlock(jsonData.externalShippingModules, jsonData.shippingBlock, jsonData.shippingBlockChecksum, jsonData.triggerElementName);
	}

	// When shipping block is updated, it triggers setDeliveryMethod which will re-update payment methods
	// one more time; so it's not necessary to updatePayments initially, only when shipping block did not update
	if (('undefined' !== typeof updatePayment && updatePayment) || !shippingBlockUpdated) {
		paymentBlockUpdated = updatePaymentBlock(jsonData.paymentMethodsList, jsonData.paymentBlock, jsonData.paymentBlockChecksum, jsonData.triggerElementName);
	}

	// update cart summary block only when updateShipping is not set (because update shipping would call update summary)
	if ('undefined' !== typeof updateSummary && updateSummary && !paymentBlockUpdated && !shippingBlockUpdated) {
		updateCartSummaryBlock(jsonData.cartSummaryBlock, jsonData.cartSummaryBlockChecksum);
	}
}

function getShippingAndPaymentBlocks() {
	// url - implicitly using current
	$.ajax({
		customPropAffectedBlocks: '#deoonepagecheckout-shipping, #deoonepagecheckout-payment, #deoonepagecheckout-cart-summary',
		type: 'POST',
		cache: false,
		dataType: "json",
		data: "&ajax_request=1&action=getShippingAndPaymentBlocks" + "&token=" + static_token,
		success: function (jsonData) {

			updateCheckoutBlocks(jsonData, true, true, false);

		}
	});
}

function toggleGiftMessage() {
	if ($('.order-options #gift.in').length) {
		$('.order-options #gift').slideUp('fast', function () {
			$(this).removeClass('in').removeClass('show');
		});
	} else {
		$('.order-options #gift').slideDown('fast', function () {
			$(this).addClass('in show')
		});
	}
}

function selectDeliveryOption(deliveryForm) {

	// To support mondial relay v3.0+, allow a bit of time for widget markup appear in extra content
	setTimeout(function () {
		var selectedDeliveryOptionExtra = $(deliveryOptionSelector).closest('.delivery-option-row').next('.carrier-extra-content');
		$('.carrier-extra-content').not(selectedDeliveryOptionExtra).hide();
		if (selectedDeliveryOptionExtra.height()) {
			selectedDeliveryOptionExtra.slideDown();
		}
	}, 100);

	// url - implicitly using current
	$.ajax({
		customPropAffectedBlocks: '#deoonepagecheckout-cart-summary, #deoonepagecheckout-payment',
		url: insertUrlParam('selectDeliveryOption'),
		type: 'POST',
		cache: false,
		dataType: "json",
		data: deliveryForm.serialize() + "&selectDeliveryOption&ajax_request=1&action=selectDeliveryOption" + "&token=" + static_token,
		success: function (jsonData) {

			$('#deoonepagecheckout-shipping .error-msg').hide();
			updateCheckoutBlocks(jsonData, true, (forceRefreshShipping ? true : false), true);
			checkAndHideGlobalError();

		}
	});
}

function setDeliveryMessage() {
	// url - implicitly using current
	$.ajax({
		type: 'POST',
		cache: false,
		dataType: "json",
		data: 'delivery_message=' + encodeURIComponent($('#delivery_message').val()) + "&ajax_request=1&action=setDeliveryMessage" + "&token=" + static_token,
		success: function (jsonData) {
			// No action necessary, we just set it on backend to checkout_session
		}
	});
}
