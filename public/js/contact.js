$(function() {

	'use strict';

	if (!sessionStorage.getItem('token') || sessionStorage.getItem('token')==='') {
		$('#btn-settings').hide();
		$('#btn-log-out').hide();
		$('#btn-change-password').hide();
		$('#btn-delete-account').hide();
	}

	$('#antiSpamCheckBox').on('change', function() {
		getToken();
	});

	function getToken() {
		$.ajax({
			type: "GET",
			url: "/api/security/get-public-token",
			contentType: "application/json",
			dataType: "json",

			beforeSend: function() {
			},
			success: function(response) {
				if (response.token) {
					$('#token').val(response.token);
				}
			},
			error: function(request, status, error) {
			}
		});
	}

	function getFormData($form){
		var unindexed_array = $form.serializeArray();
		var indexed_array = {};

		$.map(unindexed_array, function(n, i){
			indexed_array[n['name']] = n['value'];
		});

		return indexed_array;
	}

	var contactForm = function() {

		if ($('#contactForm').length > 0 ) {
			$( "#contactForm" ).validate( {
				rules: {
					email: {
						required: true,
						email: true
					},
					message: "required",
				},
				messages: {
					email: {
						required: "Please enter a valid email address",
						email: "Please enter a valid email address",
					}
				},
				/* submit via ajax */
				submitHandler: function(form) {
					var $submit = $('.submitting'),
						waitText = 'Submitting...';

					$('#form-message-warning').html("");

					$.ajax({
						type: "POST",
						url: "/"+LANG+"/send-contact-message",
						contentType: "application/json",
						dataType: "json",
						data: JSON.stringify(getFormData($(form))),

						beforeSend: function() {
							$submit.css('display', 'block').text(waitText);
						},
						success: function(msg) {
							if (msg && msg.confirmation === 'ok') {
								$('#form-message-warning').hide();
								setTimeout(function(){
									$('#contactForm').fadeOut();
								}, 1000);
								setTimeout(function(){
									$('#form-message-success').fadeIn();
								}, 1400);
								$submit.css('display', 'none');
							} else {
								$('#form-message-warning').html(msg)
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
	contactForm();

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
