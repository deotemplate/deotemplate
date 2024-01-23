/**
 *  @author    DeoTemplate <deotemplate@gmail.com>
 *  @copyright by DeoTemplate
 *  @license   http://deotemplate.com - prestashop template provider
 */
$(document).ready(function(){
	let term_check = deo_variables_social_login.term_check;
	let processing_text = deo_variables_social_login.processing_text;
	let email_valid = deo_variables_social_login.email_valid;
	let email_required = deo_variables_social_login.email_required;
	let password_required = deo_variables_social_login.password_required;
	let password_long = deo_variables_social_login.password_long;
	let password_repeat = deo_variables_social_login.password_repeat;
	let firstname_required = deo_variables_social_login.firstname_required;
	let lastname_required = deo_variables_social_login.lastname_required;
	let check_terms = deo_variables_social_login.check_terms;
	let enable_redirect = deo_variables_social_login.enable_redirect;
	let myaccount_url = deo_variables_social_login.myaccount_url;
	let module_dir = deo_variables_social_login.module_dir;
	let is_gen_rtl = deo_variables_social_login.is_gen_rtl;

	// Check avoid include duplicate library Twitter SDK
	if ($("#twitter-wjs").length == 0) {
		window.twttr = (function (d, s, id) {
			var t, js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id; js.async = " "; js.src= "https://platform.twitter.com/widgets.js";
			fjs.parentNode.insertBefore(js, fjs);
			return window.twttr || (t = { _e: [], ready: function (f) { t._e.push(f) } });
		}(document, "script", "twitter-wjs"));
	}

	// Check avoid include duplicate library Facebook SDK
	if ($("#facebook-jssdk").length == 0) {
		(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id; js.async = " ";
		  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	}


	// build sliderbar for types
	if ($('.deo-social-login-sidebar').length){
		for (let i=0; i<3; i++){		
			$('.deo-social-login-sidebar').first().clone().insertAfter('.deo-social-login-sidebar:last');
			let slidebar_class = '';
			switch (i){
				case 0:
					slidebar_class += 'slidebar_right';
					break;
				case 1:
					slidebar_class += 'slidebar_top';
					break;
				case 2:
					slidebar_class += 'slidebar_bottom';
					break;
			}
			$('.deo-social-login-sidebar').last().addClass(slidebar_class);
		}
		$('.deo-social-login-sidebar').first().addClass('slidebar_left');
	}

	// login action
	$('.deo-social-login').click(function(){
		if (!$(this).hasClass('active') && !$(this).hasClass('deo-dropdown')){
			$(this).addClass('active');
			let type = $(this).data('type');
			let layout = $(this).data('layout');
			// disable/enable social login for deotemplate
			let enable_sociallogin = $(this).data('enable-sociallogin');
			if (type == 'dropdown' || type == 'dropup'){
				if (enable_sociallogin){
					$(this).closest('.deo-social-login-builder').find('.deo-social-login').show();
				}else{
					$(this).closest('.deo-social-login-builder').find('.deo-social-login').hide();
				}
				if (layout != 'both'){
					$(this).closest('.deo-social-login-builder').removeClass('both-form').addClass('only-one');

					// add class active, next, prev
					proccessClassForm($(this).closest('.deo-social-login-builder').find('.deo-'+layout+'-form'));
				}else{
					$(this).closest('.deo-social-login-builder').addClass('both-form').removeClass('only-one');

					// add class active, next, prev
					proccessClassForm($(this).closest('.deo-social-login-builder').find('.deo-login-form'));
				}
			}
			if (type == 'popup'){
				if (enable_sociallogin){
					$('.deo-social-login-modal .deo-social-login').show();
				}else{
					$('.deo-social-login-modal .deo-social-login').hide();
				}
				if (layout != 'both'){
					$('.deo-social-login-modal').removeClass('both-form').addClass('only-one');

					// add class active, next, prev
					proccessClassForm($('.deo-social-login-modal .deo-'+layout+'-form'));
				}else{
					$('.deo-social-login-modal').addClass('both-form').removeClass('only-one');

					// add class active, next, prev
					proccessClassForm($('.deo-social-login-modal .deo-login-form'));
				}
				$('.deo-social-login-modal').modal('show');
			}
			if (type == 'slidebar_left' || type == 'slidebar_right' || type == 'slidebar_top' || type == 'slidebar_bottom'){
				if (enable_sociallogin){
					$('.deo-social-login-sidebar .deo-social-login').show();
				}else{
					$('.deo-social-login-sidebar .deo-social-login').hide();
				}
				let prefix_class = type;
				if (layout != 'both'){
					$('.deo-social-login-sidebar.'+prefix_class).removeClass('both-form').addClass('only-one');
					
					// add class active, next, prev
					proccessClassForm($('.deo-social-login-sidebar.'+prefix_class+' .deo-'+layout+'-form'));
				}else{
					$('.deo-social-login-sidebar.'+prefix_class).addClass('both-form').removeClass('only-one');
					
					// add class active, next, prev
					proccessClassForm($('.deo-social-login-sidebar.'+prefix_class+' .deo-login-form'));
				}
				$('.deo-social-login-sidebar.'+prefix_class).addClass('active');
				$('.deo-social-login-mask').addClass('active');
				// check auto gen rtl
				if (is_gen_rtl && prestashop.language.is_rtl == 1){
					$('body').addClass('is_gen_rtl');
				}
			}
		}
	});
	
	activeEventModalSocialLogin();
	activeEventSlidebarSocialLogin();
	activeEventDropdownAndDropupSocialLogin();
	$('.deo-social-login-builder.js-dropdown .dropdown-menu').click(function (e) {
		e.stopPropagation();
	});

	// display forgotpass form
	$('.call-reset-action').click(function(){
		callResetPasswordForm($(this));
		
		return false;
	});
	
	$('.call-login-action').click(function(){	
		callLoginForm($(this));
		return false;
	})
	
	// display register form
	$('.call-register-action').click(function(){
		callRegisterForm($(this));
		return false;
	});
	
	$('.deo-reset-password-form form').submit(function(){
		// if ($(this).find('.form-group.validate-error').length)
		if ($(this).find('.btn-reset-password').hasClass('validate-ok') || $(this).find('.validate-error').length){
			return false;
		}
	});
	
	// button send email reset password
	$('.btn-reset-password').click(function(){
		if (!$(this).hasClass('active') && !$(this).hasClass('success')){		
			let object_e = $(this);
			let parent_obj = object_e.closest('.form-content');
			let wrapper = object_e.closest('.deo-social-login-form-wrapper');

			object_e.addClass('active deo-loading-button');

			//validate
			parent_obj.find('input').each(function(){
				let form_group = $(this).closest('.form-group');
				// console.log($(this));
				if ($.trim($(this).val()) == ''){
					form_group.attr('data-validate', email_required);
					form_group.addClass('validate-error');
	
					return false;
				}else{
					if (!validateEmail($(this).val())){
						form_group.attr('data-validate', email_valid);
						form_group.addClass('validate-error');	

						return false;
					}else{
						form_group.removeAttr('data-validate');
						form_group.removeClass('validate-error');
					}
				}
			});
			
			
			if (!parent_obj.find('.validate-error').length){
				parent_obj.find('.btn-reset-password').addClass('validate-ok');
				let email_reset = $.trim(parent_obj.find('.email-reset').val());
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax_social_login,
					async: true,
					cache: false,
					data: {
						"ajax": 1,
						"action": "reset-password",
						"email-reset": email_reset,
					},
					beforeSend: function (){
						wrapper.addClass('proccessing');
						wrapper.find('.login-text-process').html(processing_text);
					},
					success: function (result){
						let object_result = $.parseJSON(result);
						// console.log(object_result);
						object_e.removeClass('active');					

						if (object_result.errors.length){
							$.each(object_result.errors,function(key, val){
								DeoTemplate.messageError(val);
							})

							wrapper.removeClass('proccessing');
							object_e.removeClass('active deo-loading-button');
						}else{
							wrapper.removeClass('proccessing').addClass('success');
							wrapper.find('.login-text-process').html(object_result.success[0]);
							object_e.removeClass('deo-loading-button').addClass('deo-success');
						}											
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}else{
				object_e.removeClass('active deo-loading-button');
				// return false;	
			}		
		}
	});

	$('#delete-account-link a').click(function(){
		if (confirm(deo_confirm_delete_account) == true) {
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: deo_url_ajax_social_login,
				async: true,
				cache: false,
				data: {
					"action": "delete-account",
				},
				success: function (result){
					let object_result = $.parseJSON(result);

					if (object_result.success){
						DeoTemplate.messageSuccess(object_result.message);
						setTimeout(function(){
							window.location.href = deo_redirect_url;
						}, 2000);

					}else{
						DeoTemplate.messageError(object_result.message);
					}											
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});
		}
	});	

	$('.deo-login-form form').submit(function(){
		// if ($(this).find('.form-group.validate-error').length)
		if ($(this).find('.btn-login').hasClass('validate-ok') || $(this).find('.validate-error').length){
			return false;
		}
	});
	
	// button login
	$('.btn-login').click(function(){
		if (!$(this).hasClass('active') && !$(this).hasClass('success')){		
			let object_e = $(this);
			let parent_obj = object_e.closest('.form-content');
			let wrapper = object_e.closest('.deo-social-login-form-wrapper');

			object_e.addClass('active deo-loading-button');

			//validate
			parent_obj.find('input').each(function(){
				let form_group = $(this).closest('.form-group');
				if ($.trim($(this).val()) == ''){
					if ($(this).hasClass('password-login')){
						form_group.attr('data-validate', password_required);
						form_group.addClass('validate-error');
					}else{
						form_group.attr('data-validate', email_required);
						form_group.addClass('validate-error');
					}
				}else{
					if ($(this).hasClass('email-login') && !validateEmail($(this).val())){
						form_group.attr('data-validate', email_valid);
						form_group.addClass('validate-error');					
					}else if ($(this).hasClass('password-login') && $(this).val().length < 5){
						form_group.attr('data-validate', password_long);
						form_group.addClass('validate-error');
					}else{
						form_group.removeAttr('data-validate');
						form_group.removeClass('validate-error');
					}
				}
			});
			
			if (!parent_obj.find('.validate-error').length){
				object_e.addClass('validate-ok');
				let email_login = $.trim(parent_obj.find('.email-login').val());
				let password_login = $.trim(parent_obj.find('.password-login').val());
				let data_send = {};
				data_send.ajax = 1;
				data_send.action = "customer-login";
				data_send.email_login = email_login;
				data_send.password_login = password_login;
				if (parent_obj.find('.keep-login').length){
					let keep_login = 0;
					if (parent_obj.find('.keep-login').is(":checked")){
						keep_login = 1;
					}
	
					data_send.keep_login = keep_login;
				}
				// console.log(data_send);
				// return false;
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax_social_login,
					async: true,
					cache: false,
					data: data_send,
					beforeSend: function (){
						wrapper.addClass('proccessing');
						wrapper.find('.login-text-process').html(processing_text);
					},
					success: function (result){
						let object_result = $.parseJSON(result);
						// console.log(object_result);
						object_e.removeClass('active deo-loading-button');
						if (object_result.errors.length){
							$.each(object_result.errors,function(key, val){
								DeoTemplate.messageError(val);
							})
							wrapper.removeClass('proccessing');
							object_e.removeClass('active deo-loading-button');
						}else{
							wrapper.removeClass('proccessing').addClass('success');
							wrapper.find('.login-text-process').html(object_result.success[0]);
							object_e.removeClass('deo-loading-button').addClass('deo-success');
							if (enable_redirect){
								window.location.replace(myaccount_url);
							}else{
								window.location.reload();
							}
						}											
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}else{
				object_e.removeClass('active deo-loading-button');
				// return false;	
			}
		}
	});
	
	$('.deo-register-form form').submit(function(){
		// if ($(this).find('.form-group.validate-error').length)
		if ($(this).find('.btn-register').hasClass('validate-ok') || $(this).find('.validate-error').length){
			return false;
		}
	});
	
	// button login
	$('.btn-register').click(function(){
		if (!$(this).hasClass('active') && !$(this).hasClass('success')){		
			let object_e = $(this);
			let parent_obj = object_e.closest('.form-content');
			let wrapper = object_e.closest('.deo-social-login-form-wrapper');

			object_e.addClass('active deo-loading-button');
			
			//validate
			parent_obj.find('input').each(function(){
				let form_group = $(this).closest('.form-group');
				if ($.trim($(this).val()) == ''){
					if ($(this).hasClass('register-password') || $(this).hasClass('repeat-register-password')){
						form_group.attr('data-validate', password_required);
						form_group.addClass('validate-error');
					}else if ($(this).hasClass('register-email')){
						form_group.attr('data-validate', email_required);
						form_group.addClass('validate-error');
					}else if ($(this).hasClass('firstname')){
						form_group.attr('data-validate', firstname_required);
						form_group.addClass('validate-error');
					}else if ($(this).hasClass('lastname')){
						form_group.attr('data-validate', lastname_required);
						form_group.addClass('validate-error');
					}
				}else{
					if ($(this).hasClass('register-email') && !validateEmail($(this).val())){
						form_group.attr('data-validate', email_valid);
						form_group.addClass('validate-error');					
					}else if ($(this).hasClass('register-password') && $(this).val().length < 5){
						form_group.attr('data-validate', password_long);
						form_group.addClass('validate-error');
					}else if ($(this).hasClass('repeat-register-password') && $(this).val().length < 5){
						form_group.attr('data-validate', password_long);
						form_group.addClass('validate-error');
					}else if ($(this).hasClass('register-checkbox') && !$(this).is(':checked')){
						form_group.addClass('validate-error');
						DeoTemplate.messageError(term_check);
					}else{
						form_group.removeAttr('data-validate');
						form_group.removeClass('validate-error');
					}
				}
			});
			
			
			let validate_password = parent_obj.find('.register-password');
			let validate_repeat_password = parent_obj.find('.repeat-register-password');
			if (validate_password.val().length > 5 && validate_repeat_password.val().length > 5){
				if (validate_repeat_password.val() != validate_password.val()){
					validate_password.closest('.form-group').attr('data-validate', password_repeat);
					validate_repeat_password.closest('.form-group').attr('data-validate', password_repeat);

					validate_password.closest('.form-group').addClass('validate-error');
					validate_repeat_password.closest('.form-group').addClass('validate-error');
				}else{
					validate_password.closest('.form-group').removeAttr('data-validate');
					validate_password.closest('.form-group').removeAttr('data-validate');

					validate_password.closest('.form-group').removeClass('validate-error');
					validate_password.closest('.form-group').removeClass('validate-error');
				}
			}
							
			if (!parent_obj.find('.validate-error').length){
				parent_obj.find('.btn-register').addClass('validate-ok');
				let firstname = $.trim(parent_obj.find('.register-firstname').val());
				let lastname = $.trim(parent_obj.find('.register-lastname').val());
				let register_email = $.trim(parent_obj.find('.register-email').val());
				let register_pass = $.trim(parent_obj.find('.register-password').val());
				let repeat_register_pass = $.trim(parent_obj.find('.repeat-register-password').val());
				let data = {
					"ajax": 1,
					"action": "create-account",
					"firstname": firstname,
					"lastname": lastname,
					"register-email": register_email,
					"register-password": register_pass,
					"repeat-register-password": repeat_register_pass,
				};
				if (check_terms){
					data.check_terms = (parent_obj.find('.register-checkbox').is(':checked')) ? 1 : 0;
				}
				$.ajax({
					type: 'POST',
					headers: {"cache-control": "no-cache"},
					url: deo_url_ajax_social_login,
					async: true,
					cache: false,
					data: data,
					beforeSend: function (){
						wrapper.addClass('proccessing');
						wrapper.find('.login-text-process').html(processing_text);
					},
					success: function (result){
						let object_result = $.parseJSON(result);
						// console.log(object_result);
						
						object_e.removeClass('active deo-loading-button');					
						if (object_result.errors.length){
							$.each(object_result.errors,function(key, val){
								DeoTemplate.messageError(val);
							})
							wrapper.removeClass('proccessing');
							object_e.removeClass('active deo-loading-button');
						}else{
							wrapper.removeClass('proccessing').addClass('success');
							wrapper.find('.login-text-process').html(object_result.success[0]);
							object_e.removeClass('deo-loading-button').addClass('deo-success');
							if (enable_redirect){
								window.location.replace(myaccount_url);
							}else{
								window.location.reload();
							}
						}											
					},
					error: function (XMLHttpRequest, textStatus, errorThrown) {
						console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}else{
				object_e.removeClass('active deo-loading-button');
				// return false;	
			}
		}
	});

	if (typeof google_client_id != 'undefined' && google_client_id.length){
		gapi.load('auth2', function(){
			// Retrieve the singleton for the GoogleAuth library and set up the client.
			let auth2 = gapi.auth2.init({
				client_id: google_client_id,
				cookiepolicy: 'single_host_origin',
				// Request scopes in addition to 'profile' and 'email'
				scope: 'profile email'
			});

			$('.google').each(function(){
				auth2.attachClickHandler(this, {},
					function(googleUser) {
						let profile = googleUser.getBasicProfile();
						$('.deo-social-login-modal').modal('hide');
						$('.deo-social-login-mask').trigger('click');
						
						setTimeout(function(){  
							if (profile.getEmail()){
								$.ajax({
									type: 'POST',
									headers: {"cache-control": "no-cache"},
									url: deo_url_ajax_social_login,
									async: true,
									cache: false,
									data: {
										"ajax": 1,
										"action": "social-login",
										"email": profile.getEmail(),
										"first_name": profile.getGivenName(),
										"last_name": profile.getFamilyName(),
									},
									beforeSend:  function (result){
										$('.deo-message-social-modal').addClass('loading');
										$('.deo-message-social-modal').modal('show');
									},
									success: function (result){
										let object_result = $.parseJSON(result);
															
										if (object_result.errors.length){						
											$('.deo-message-social-modal').removeClass('loading').addClass('error-login');
										}else{						
											$('.deo-message-social-modal').removeClass('loading').addClass('success');
										}											
									},
									error: function (XMLHttpRequest, textStatus, errorThrown) {
										console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
									}
								});
							}else{
								$('.deo-message-social-modal').addClass('error-email');
								$('.deo-message-social-modal').modal('show');

								auth2.disconnect();
							}
						},500);
					}, function(error) {
					  console.log(error);
					}
				);	
			});
		});
		// window.___gcfg = {
		// 	lang: 'zh-CN',
		// 	parsetags: 'onload'
		// };
	}
	
	// twitter login
	$(document).on('click', '.deo-social-login-links .social-login-btn.twitter', function(){
		window.open(deo_url_ajax_social_login+'?request=twitter&lang='+prestashop.language.iso_code, '_blank', 'toolbar=yes, scrollbars=yes, resizable=yes, top=100, left=300, width=700, height=600');
	});

	$(document).on('click', '.cancel-form-social-login', function(){
		$(this).closest('.deo-social-login-form-wrapper').removeClass('success proccessing');
	});

	$(document).on('change', '.form-group-checkbox input[type="checkbox"]', function(){
		if ($(this).is(':checked')){
			$(this).closest('.form-group-checkbox').removeClass('validate-error');
		}
	});

	$(document).on('click', '.deo-social-login-links .social-login-btn.facebook', function(){
		FB.login(function(response) {
			if (response.status === 'connected') {
				// Logged into your app and Facebook.

				$('.deo-social-login-modal').modal('hide');
				$('.deo-social-login-mask').trigger('click');
				
				setTimeout(function(){
					FB.api('/me?fields=email,birthday,first_name,last_name,name,gender', function(response) {
						if (response.email){
							$.ajax({
								type: 'POST',
								headers: {"cache-control": "no-cache"},
								url: deo_url_ajax_social_login,
								async: true,
								cache: false,
								data: {
									"ajax": 1,
									"action": "social-login",
									"email": response.email,
									"first_name": response.first_name,
									"last_name": response.last_name,
								},
								beforeSend:  function (result){
									$('.deo-message-social-modal').addClass('loading');
									$('.deo-message-social-modal').modal('show');
								},
								success: function (result){
									let object_result = $.parseJSON(result);
														
									if (object_result.errors.length){				
										$('.deo-message-social-modal').removeClass('loading').addClass('error-login');		
									}else{						
										$('.deo-message-social-modal').removeClass('loading').addClass('success');
									}											
								},
								error: function (XMLHttpRequest, textStatus, errorThrown) {
									console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
								}
							});
							
						}else{
							$('.deo-message-social-modal').addClass('error-email');
							$('.deo-message-social-modal').modal('show');
							FB.api('/me/permissions', 'delete', function(response) {
								// console.log(response); 
							});
						}
					});
				
				},300);
			} else {
				// The person is not logged into your app or we are unable to tell.
				DeoTemplate.messageWarning('Please login to use this app.');
			}
		} , {scope: 'public_profile,email'}); 
	});

	function proccessClassForm(element){
		element.closest('.deo-social-login-form').children('.deo-form').removeClass('active next prev');
		element.addClass('active');
		element.nextAll().addClass('next');
		element.prevAll().addClass('prev');
	}
	
	function callLoginForm($element){
		let parent_obj = $element.parents('.deo-social-login-form');
		proccessClassForm(parent_obj.children('.deo-login-form'));
	}

	function callRegisterForm($element){
		let parent_obj = $element.parents('.deo-social-login-form');
		proccessClassForm(parent_obj.children('.deo-register-form'));
	}

	function callResetPasswordForm($element){
		let parent_obj = $element.parents('.deo-social-login-form');
		proccessClassForm(parent_obj.children('.deo-reset-password-form'));
	}

	// event for slidebar
	function activeEventSlidebarSocialLogin(){
		$('.deo-social-login-mask, .sidebar-close').click(function(){
			$('.deo-social-login-mask.active').removeClass('active');
			$('.deo-social-login.active').removeClass('active');
			setTimeout(function(){
				$('deo-social-login-sidebar deo-form').removeClass('active next prev');

				// check auto gen rtl
				if (is_gen_rtl && prestashop.language.is_rtl == 1){
					$('body').removeClass('is_gen_rtl');
				}
			},500);
				
			$('.deo-social-login-sidebar.active').removeClass('active');
		});
	}

	function activeEventDropdownAndDropupSocialLogin(){
		$('.deo-social-login-builder').on('hide.bs.dropdown', function (e) {
			$(this).find('.deo-social-login.active').removeClass('active');
			$(this).find('.deo-form').removeClass('active next prev');
		});
	}

	// event for modal
	function activeEventModalSocialLogin(){
		$('.deo-social-login-modal').on('hide.bs.modal', function (e) {
			$('.deo-social-login.active').removeClass('active');
			$(this).find('.deo-form').removeClass('active next prev');
		});
		$('.deo-message-social-modal').on('hide.bs.modal', function (e) {
			if ($(this).hasClass('success')){
				if (enable_redirect){
					window.location.replace(myaccount_url);
				}else{
					window.location.reload();
				}
			}
		});
		$('.deo-message-social-modal').on('show.bs.modal', function (e) {
			$(this).removeClass('loading success error-login error-email');
		});
	}

	function validateEmail(email) {
	  // let regex = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	  // return regex.test(email);
		let reg = /^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i;
		return reg.test(email);
	}

});

// twitter login
function twitterLogin(first_name, last_name, email) {
	$('.deo-social-login-modal').modal('hide');
	$('.deo-social-login-mask').trigger('click');

	setTimeout(function(){  
		if (email.length){			
			// console.log(response);
			// when can't get last name of user
			if (last_name == ''){
				last_name = 'twitter';
			}
			// console.log('Successful login for: ' + response.name);
			// console.log('Thanks for logging in, ' + response.name + '!');
			$.ajax({
				type: 'POST',
				headers: {"cache-control": "no-cache"},
				url: deo_url_ajax_social_login,
				async: true,
				cache: false,
				data: {
					"ajax": 1,
					"action": "social-login",
					"email": email,
					"first_name": first_name,
					"last_name": last_name,
				},
				beforeSend:  function (result){
					$('.deo-message-social-modal').addClass('loading');
					$('.deo-message-social-modal').modal('show');
				},
				success: function (result){
					let object_result = $.parseJSON(result);
										
					if (object_result.errors.length){		
						$('.deo-message-social-modal').removeClass('loading').addClass('error-login');			
					}else{						
						$('.deo-message-social-modal').removeClass('loading').addClass('success');
					}											
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					console.log("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				}
			});	
		}else{
			$('.deo-message-social-modal').addClass('error-email');
			$('.deo-message-social-modal').modal('show');
			// console.log('Fail');
		}
	},300);
}



