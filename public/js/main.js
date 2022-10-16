TOKEN = '';

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
				      	url: "api/login_check",
						contentType: "application/json",
						dataType: "json",
						data: JSON.stringify(getFormData($(form))),

				      beforeSend: function() { 
				      	$submit.css('display', 'block').text(waitText);
				      },
				      success: function(msg) {
		               if (msg && msg.token) {
						   TOKEN = msg.token;

		               	$('#form-message-warning').hide();
				            setTimeout(function(){
		               		$('#loginForm').fadeOut();
		               	}, 1000);
				            setTimeout(function(){
				               $('#form-message-success').fadeIn();
		               	}, 1400);
							$submit.css('display', 'none');
			            } else {
			               $('#form-message-warning').html(msg);
				            $('#form-message-warning').fadeIn();
				            $submit.css('display', 'none');
			            }
				      },
				      error: function() {
				      	$('#form-message-warning').html("Something went wrong. Please try again.");
				         $('#form-message-warning').fadeIn();
				         $submit.css('display', 'none');
				      }
			      });    		
		  		}
				
			} );
		}
	};
	loginForm();

	//-----------------------------------

	var uploadForm = function() {

		if ($('#uploadForm').length > 0 ) {
			$( "#uploadForm" ).validate( {
				rules: {
					file: "required",
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
					file: "Please select a file",
				},
				/* submit via ajax */
				submitHandler: function(form) {
					var $submit = $('.submitting'),
						waitText = 'Submitting...';

					$('#form-message-warning').html("");

					$.ajax({
						type: "POST",
						headers: {
							Authorization: 'Bearer '+TOKEN
						},
						url: "/api/files/upload",
						contentType: false,
						dataType: "json",
						enctype: 'multipart/form-data',
						data: new FormData(form),
						processData:false,

						beforeSend: function() {
							$submit.css('display', 'block').text(waitText);
						},
						success: function(json) {
							if (json) {
								console.debug(json);

								$('#form-message-warning').hide();
								setTimeout(function(){
									$('#uploadForm').fadeOut();
								}, 1000);
								setTimeout(function(){
									$('#form-upload-success').fadeIn();
								}, 1400);
								$submit.css('display', 'none');

							} else {
								$('#form-message-warning').html(msg);
								$('#form-message-warning').fadeIn();
								$submit.css('display', 'none');
							}
						},
						error: function() {
							$('#form-message-warning').html("Something went wrong. Please try again.");
							$('#form-message-warning').fadeIn();
							$submit.css('display', 'none');
						}
					});
				}

			} );
		}
	};
	uploadForm();


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
						url: "api/users/register",
						contentType: "application/json",
						dataType: "json",
						data: JSON.stringify(getFormData($(form))),

						beforeSend: function() {
							$submit.css('display', 'block').text(waitText);
						},
						success: function(msg) {
							if (msg && msg.totalSpaceUsedMo === 0) {
								$('#form-message-warning').hide();
								setTimeout(function(){
									$('#subscribeForm').fadeOut();
								}, 1000);
								setTimeout(function(){
									$('#form-message-success').fadeIn();
								}, 1400);
								$submit.css('display', 'none');
							} else {
								$('#form-message-warning').html(msg);
								$('#form-message-warning').fadeIn();
								$submit.css('display', 'none');
							}
						},
						error: function() {
							$('#form-message-warning').html("Something went wrong. Please try again.");
							$('#form-message-warning').fadeIn();
							$submit.css('display', 'none');
						}
					});
				}

			} );
		}
	};
	subscribeForm();


	var contactForm = function() {

		if ($('#contactForm').length > 0 ) {
			$( "#contactForm" ).validate( {
				rules: {
					email: {
						required: true,
						email: true
					},
					message: "required",
					response1: {
						required: true
					},
					response2: {
						required: true
					},
				},
				messages: {
					email: {
						required: "Please enter a valid email address",
						email: "Please enter a valid email address",
					},
					response1: "Please calculate the requested sum and give the result (numbers)",
					response2: "Please calculate the requested sum and give the result (numbers)",
				},
				/* submit via ajax */
				submitHandler: function(form) {
					var $submit = $('.submitting'),
						waitText = 'Submitting...';

					$('#form-message-warning').html("");

					$.ajax({
						type: "POST",
						url: "send-contact-message",
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
								$('#form-message-warning').html(msg);
								$('#form-message-warning').fadeIn();
								$submit.css('display', 'none');
							}
						},
						error: function() {
							$('#form-message-warning').html("Something went wrong. Please try again.");
							$('#form-message-warning').fadeIn();
							$submit.css('display', 'none');
						}
					});
				}

			} );
		}
	};
	contactForm();


	$('#uploadAnotherFile').click(function() {
		$('#form-message-warning').hide();
		setTimeout(function(){
			$('#form-upload-success').fadeOut();
		}, 1000);
		setTimeout(function(){
			$('#uploadForm').fadeIn();
		}, 1400);
	});

});