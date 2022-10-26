$(function() {

	'use strict';

	$('#uploadAnotherFile').click(function() {
		$('#form-message-warning').hide();
		setTimeout(function(){
			$('#form-upload-success').fadeOut();
		}, 1000);
		setTimeout(function(){
			$('#uploadForm').fadeIn();
		}, 1400);
	});

	function getFormData($form){
		var unindexed_array = $form.serializeArray();
		var indexed_array = {};

		$.map(unindexed_array, function(n, i){
			indexed_array[n['name']] = n['value'];
		});

		return indexed_array;
	}

	function displayFilesList(files, divId) {
		console.log('displayFilesList', files, divId);
		let tableHead = '<table id="files" class="table table-striped" style="width:100%">\n' +
			'        <thead>\n' +
			'            <tr>\n' +
			'                <th>File name</th>\n' +
			'                <th>Actions</th>\n' +
			'                <th>Result</th>\n' +
			'            </tr>\n' +
			'        </thead>\n';

		let tableMiddle = '';

		files.forEach(function(element, index, array)
			{
				let description = '<input type="button" value="'+element.description+'" class="downloadLink" file_name="'+element.name+'" url="'+element.url+'" description="'+element.description+'">'
				tableMiddle += '<tr>\n' +
					'				<td>'+description+'</td>\n' +
					'				<td>'+element.scaned+'</td>\n' +
					'				<td>'+element.infected+'</td><td></td>\n' +
					'			</tr>\n'
			}
		);

		let tableFoot = '    </table>';

		$("#"+divId).html(tableHead+tableMiddle+tableFoot);
		$('.downloadLink').click(function() {
			let fileName = $(this).attr('file_name');
			let url = '/api/files/download/'+$(this).attr('url');

			var showFile = function (blob) {
				const data = window.URL.createObjectURL(blob);
				var link = document.createElement('a');
				link.href = data;
				link.download = fileName;
				link.click();
				setTimeout(function () {
					window.URL.revokeObjectURL(data);
				}, 100)
			}
			var jwtToken = sessionStorage.getItem('token');
			var headerObj = {"Authorization": "Bearer " + jwtToken}

			var xhr = new XMLHttpRequest();
			$.ajax({
				xhrFields: {
					responseType: 'blob'
				},
				headers: headerObj,
				type:'GET',
				url:url
			}).done(function(blob){
				showFile(blob);
			});

		});
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
				      success: function(response) {
		               if (response && response.token && response.refresh_token) {
						   sessionStorage.setItem('token', response.token);
						   sessionStorage.setItem('refreshToken', response.refresh_token);
						   initRefreshTokenCalls()
						   $('#h-top').text('Welcome in your private space');
						   let files = getFilesList();
						   displayFilesList(files, 'files-list');

		               	$('#form-message-warning').hide();
				            setTimeout(function(){
		               		$('#loginForm').fadeOut();
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
				      error: function() {
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
							Authorization: 'Bearer '+sessionStorage.getItem('token')
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
								$('#form-message-warning').hide();
								setTimeout(function(){
									$('#uploadForm').fadeOut();
								}, 1000);
								setTimeout(function(){
									$('#form-upload-success').fadeIn();
								}, 1400);
								$submit.css('display', 'none');
								let files = getFilesList();
								displayFilesList(files, 'files-list');

							} else {
								$('#form-message-warning').html(json)
									.fadeIn();
								$submit.css('display', 'none');
							}
						},
						error: function(request, status, error) {
							console.log('Upload status : ', status);
							console.log('Upload error : ', error);
							$('#form-message-warning').html("Something went wrong. Please try again.")
								.fadeIn();
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
						error: function() {
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
								$('#form-message-warning').html(msg)
									.fadeIn();
								$submit.css('display', 'none');
							}
						},
						error: function() {
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

	var getFilesList = function(limit = 50, offset = 0) {

		let dataReceived = null;
		$.ajax({
			type: "POST",
			url: "/api/files/"+limit+'/'+offset,
			contentType: "application/json",
			dataType: "json",
			async: false,
			headers: {
				Authorization: 'Bearer '+sessionStorage.getItem('token')
			},

			beforeSend: function() {
			},
			success: function(data) {
				dataReceived = data;
			},
			error: function() {
				alert('Your are disconected!');
				document.location.href="/";
			}
		});

		return dataReceived;
	}

	var initRefreshTokenCalls = function () {
		setTimeout(function(){

			$.ajax({
				type: "POST",
				url: "/api_refresh_token",
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
				error: function() {
					alert('Your are disconected!');
					sessionStorage.setItem('refreshToken', response.refresh_token);
					document.location.href="/";
				}
			});

		}, 15000);
	}
});
