$(function() {
	'use strict';
	initRefreshTokenCalls();

	function getFormData($form){
		var unindexed_array = $form.serializeArray();
		var indexed_array = {};

		$.map(unindexed_array, function(n, i){
			indexed_array[n['name']] = n['value'];
		});

		Object.keys(indexed_array).forEach(
			key =>	{
				if (indexed_array[key] === 'true') {
					indexed_array[key] = true;
				}

				if (indexed_array[key] === 'false') {
					indexed_array[key] = false;
				}
			}
		);

		return indexed_array;
	}

	function getFormFromJson(userInfos){
		return '                        <div class="row">\n' +
			'                                <div class="col-md-12 form-group">\n' +
			'									Your e-mail address\n' +
			'                                    <input type="text" class="form-control" name="email" id="email" value="'+userInfos.email+'" readonly>\n' +
			'                                </div>\n' +
			'                            </div><br><br>\n' +
			'\n' +
			'							<div className="row">\n' +
			'								<div className="col-md-12 form-group">\n' +
			'\n 								You must first enter your password<br>' +
			'									<input type="password" className="form-control" name="password1" id="password1" placeholder="Password">\n' +
			'									<input type="hidden" className="form-control" name="lang" value="'+LANG+'">\n' +
			'									</div>\n' +
			'							</div><br>\n' +
			'\n' +
			'                            <div class="row">\n' +
			'                                <div class="col-md-12">\n' +
			'                                    <input type="submit" value="Delete my account and personal data" class="btn btn-primary rounded-0 py-2 px-4">\n' +
			'                                    <span class="submitting"></span>\n' +
			'                                </div>\n' +
			'                            </div>';
	}

	function updateFormView(content) {
		$('#deleteMyAccountForm').html(content);
	}

	function getUserInfos() {
			$.ajax({
				type: "GET",
				url: "/api/user/user/"+sessionStorage.getItem('userId'),
				contentType: "application/json",
				dataType: "json",
				headers: {
					Authorization: 'Bearer '+sessionStorage.getItem('token')
				},

				beforeSend: function() {
				},
				success: function(response) {
					let htmlStr = getFormFromJson(response);
					updateFormView(htmlStr);
				},
				error: function(request, status, error) {

				}
			});
	}

	getUserInfos();

	var submitDeleteMyAccountForm = function() {
		if ($('#deleteMyAccountForm').length > 0) {
			$("#deleteMyAccountForm").validate({
				rules: {
					password1: {
						required: true,
						minlength: 8
					}
				},
				messages: {
					password1: {
						minlength: "Minimum length: 8 characters",
					}
				},
				submitHandler: function(form) {
					var $submit = $('.submitting'),
						waitText = 'Submitting...';
					$.ajax({
						type: "POST",
						url: "/api/user/delete-my-account/"+sessionStorage.getItem('userId'),
						contentType: "application/json",
						dataType: "json",
						headers: {
							Authorization: 'Bearer '+sessionStorage.getItem('token')
						},
						data: JSON.stringify(getFormData($(form))),
						beforeSend: function() {
							$submit.css('display', 'block').text(waitText);
						},
						success: function(response) {
							if (response && response.status === 'ok') {
								$('#form-message-warning').hide();
								setTimeout(function(){
									$('#subscribeForm').fadeOut();
								}, 1000);
								setTimeout(function(){
									$('#form-message-success').fadeIn();
								}, 1400);
								$submit.css('display', 'none');
							} else if (response.error && response.message){
								setTimeout(function(){
									$('#form-message-success').fadeOut();
								}, 1400);
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
			});
		}
	}

	submitDeleteMyAccountForm();
});
