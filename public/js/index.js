$(function() {

	'use strict';

	function getFormData($form){
		var unindexed_array = $form.serializeArray();
		var indexed_array = {};

		$.map(unindexed_array, function(n, i){
			indexed_array[n['name']] = n['value'];
		});

		return indexed_array;
	}

	var loginForm = function() {

		sessionStorage.getItem('token');
		sessionStorage.getItem('refreshToken');

		if (sessionExists()) {
			window.location.href = "/"+LANG+"/private-space";
		}

		if ($('#loginForm').length > 0 ) {
			$( "#loginForm" ).validate( {
				rules: {
					username: "required",
					password: "required",
					/*
					email: {
						required: true,
						email: true
					},
					message: {
						required: true,
						minlength: 5
					}
					 */
				},
				messages: {
					username: "Please enter your username",
					password: "Please enter your password",
				},
				/* submit via ajax */
				submitHandler: function(form) {		
					var $submit = $('.submitting'),
						waitText = 'Submitting...';

					$('#form-message-warning').html("");

					$.ajax({   	
				      	type: "POST",
				      	url: "/api/login_check",
						contentType: "application/json",
						dataType: "json",
						data: JSON.stringify(getFormData($(form))),

				      beforeSend: function() { 
				      	$submit.css('display', 'block').text(waitText);
				      },
				      success: function(response) {
		               if (response && response.token && response.refresh_token && response.userId) {
						   sessionStorage.setItem('token', response.token);
						   sessionStorage.setItem('refreshToken', response.refresh_token);
						   sessionStorage.setItem('userId', response.userId);
						   initRefreshTokenCalls();
						   window.location.href = "/"+LANG+"/private-space";
					  } else if (response.error && response.message){
						   $('#form-message-warning').html(response.message)
							   .fadeIn();
						   $submit.css('display', 'none');
					   }
				      },
						error: function(request, status, error) {
				      	$('#form-message-warning').html("Something went wrong. Please try again.")
				         .fadeIn();
				         $submit.css('display', 'none');
				      }
			      });    		
		  		}
				
			} );
		}
	};
	loginForm();

	var subscribeForm = function() {

		if ($('#subscribeForm').length > 0 ) {
			$( "#subscribeForm" ).validate( {
				rules: {
					password1: "required",
					password2: {
						required: true,
						equalTo: "#password1"
					},
					email: {
						required: true,
						email: true
					}
				},
				messages: {
					email: {
						required: "Please enter a valid email address",
						email: "Please enter a valid email address",
					},
					password1: "Please enter your password",
					password2: {
						required: "Please enter your password",
						equalTo: "Please enter the same password as above"
					},
				},
				/* submit via ajax */
				submitHandler: function(form) {
					var $submit = $('.submitting'),
						waitText = 'Submitting...';

					$('#form-message-warning').html("");

					$.ajax({
						type: "POST",
						url: "/"+LANG+"/users/register",
						contentType: "application/json",
						dataType: "json",
						data: JSON.stringify(getFormData($(form))),

						beforeSend: function() {
							$submit.css('display', 'block').text(waitText);
						},
						success: function(response) {
							if (response && response.totalSpaceUsedMo === 0) {
								$('#form-message-warning').hide();
								setTimeout(function(){
									$('#subscribeForm').fadeOut();
								}, 1000);
								setTimeout(function(){
									$('#form-message-success').fadeIn();
								}, 1400);
								$submit.css('display', 'none');
							} else {
								$('#form-message-warning').html(response)
									.fadeIn();
								$submit.css('display', 'none');
							}
						},
						error: function(request, status, error) {
							$('#form-message-warning').html("Something went wrong. Please try again.")
								.fadeIn();
							$submit.css('display', 'none');
						}
					});
				}

			} );
		}
	};
	subscribeForm();

	var initRefreshTokenCalls = function () {
		setTimeout(function(){

			$.ajax({
				type: "POST",
				url: "/api/refresh-token",
				contentType: "application/json",
				dataType: "json",
				data: '{"refresh_token":"'+sessionStorage.getItem('refreshToken')+'"}',

				beforeSend: function() {
				},
				success: function(response) {
					if (response && response.token && response.refresh_token) {
						sessionStorage.setItem('token', response.token);
						sessionStorage.setItem('refreshToken', response.refresh_token);
						initRefreshTokenCalls();
					}
				},
				error: function(request, status, error) {
					alert('Your are disconnected!');
					sessionStorage.setItem('refreshToken', response.refresh_token);
					document.location.href="/";
				}
			});

		}, 15000);
	}
});
