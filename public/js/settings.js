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
				console.log('key', indexed_array[key]);
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

		let sendEmailAfterEachAction = (userInfos.sendEmailAfterEachAction && userInfos.sendEmailAfterEachAction === true) ? 'value="true" checked ' : 'value="false" ';
		let sendEmailIfFileIsInfected = (userInfos.sendEmailIfFileIsInfected && userInfos.sendEmailIfFileIsInfected === true) ? 'value="true" checked ' : 'value="false" ';

		let sendPostToUrlAfterEachAction = (userInfos.sendPostToUrlAfterEachAction && userInfos.sendPostToUrlAfterEachAction === true) ? 'value="true" checked ' : 'value="false" ';
		let sendPostToUrlIfFileIsInfected = (userInfos.sendPostToUrlIfFileIsInfected && userInfos.sendPostToUrlIfFileIsInfected === true) ? 'value="true" checked ' : 'value="false" ';


		return '                        <div class="row">\n' +
			'                                <div class="col-md-12 form-group">\n' +
			'									Your e-mail address\n' +
			'                                    <input type="text" class="form-control" name="email" id="email" value="'+userInfos.email+'" readonly>\n' +
			'                                </div>\n' +
			'                            </div><br><br>\n' +
			'\n' +
			'                            <div class="row">\n' +
			'                            	<div class="form-check">\n' +
			'                            		<input type="hidden" '+sendEmailAfterEachAction+' id="sendEmailAfterEachAction" name="sendEmailAfterEachAction">\n' +
			'                            		<input class="form-check-input myCheckbox" '+sendEmailAfterEachAction+' type="checkbox" for="sendEmailAfterEachAction" '+sendEmailAfterEachAction+'>\n' +
			'                            		<label class="form-check-label" for="flexCheckDefault">Send me a message by email after each virus scan</label>\n' +
			'                            	</div>\n' +
			'                            </div><br><br>\n' +
			'\n' +
			'                            <div class="row">\n' +
			'                            	<div class="form-check">\n' +
			'                            		<input type="hidden" '+sendEmailIfFileIsInfected+' id="sendEmailIfFileIsInfected" name="sendEmailIfFileIsInfected">\n' +
			'                            		<input class="form-check-input myCheckbox" '+sendEmailIfFileIsInfected+' type="checkbox" value="" id="sendEmailIfFileIsInfected" for="sendEmailIfFileIsInfected">\n' +
			'                            		<label class="form-check-label" for="flexCheckDefault">Send me a message by mail if a virus is detected</label>\n' +
			'                            	</div>\n' +
			'                            </div>\n' +
			'\n' +
			'                            <div class="row">\n' +
			'                                <div class="col-md-12 form-group">\n' +
			'\n  								<br><br>Choose a URL that will be called after the scan is finished \n' +
			'                                    <input type="text" class="form-control" name="postUrlAfterAction" id="postUrlAfterAction" value="'+userInfos.postUrlAfterAction+'" placeholder="http://YOUR-DOMAIN.COM/anti-virus-notifications">\n' +
			'                                </div>\n' +
			'                            </div><br>\n' +
			'\n' +
			'                            <div class="row">\n' +
			'                                <div class="col-md-12 form-group">\n' +
			'                            		 <input type="hidden" '+sendPostToUrlAfterEachAction+' id="sendPostToUrlAfterEachAction" name="sendPostToUrlAfterEachAction">\n' +
			'                                    <input type="checkbox" class="form-check-input myCheckbox" '+sendPostToUrlAfterEachAction+' for="sendPostToUrlAfterEachAction" id="sendPostToUrlAfterEachAction" value="">\n' +
			'                            		<label class="form-check-label" for="flexCheckDefault">Send post request to this URL after each action</label>\n' +
											'</div>\n' +
			'                            </div><br>\n' +
			'\n' +
			'                            <div class="row">\n' +
			'                                <div class="col-md-12 form-group">\n' +
			'                            		 <input type="hidden" '+sendPostToUrlIfFileIsInfected+' id="sendPostToUrlIfFileIsInfected" name="sendPostToUrlIfFileIsInfected">\n' +
			'                                    <input type="checkbox" class="form-check-input myCheckbox" '+sendPostToUrlIfFileIsInfected+' for="sendPostToUrlIfFileIsInfected" id="sendPostToUrlIfFileIsInfected" value="">\n' +
			'                            		<label class="form-check-label" for="flexCheckDefault">Send post request to this URL if a virus is detected</label>\n' +
			'								 </div>\n' +
			'                            </div><br>\n' +
			'\n' +
			'                            <div class="row">\n' +
			'                                <div class="col-md-12">\n' +
			'                                    <input type="submit" value="Save" class="btn btn-primary rounded-0 py-2 px-4">\n' +
			'                                    <span class="submitting"></span>\n' +
			'                                </div>\n' +
			'                            </div>';
	}

	function updateFormView(content) {
		$('#settingsForm').html(content);

		$('.myCheckbox').change(function () {
			let clickedName = $(this).attr("for");
			if ($(this).prop("checked")) {
				$('#'+clickedName).attr("value", "true");
				return;
			}
			$('#'+clickedName).attr("value", "false");
		});
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

	var submitSetting = function() {
		if ($('#settingsForm').length > 0) {
			$("#settingsForm").validate({
				rules: {
				},
				messages: {
				},
				submitHandler: function(form) {
					$.ajax({
						type: "POST",
						url: "/api/user/edit-user/"+sessionStorage.getItem('userId'),
						contentType: "application/json",
						dataType: "json",
						headers: {
							Authorization: 'Bearer '+sessionStorage.getItem('token')
						},
						data: JSON.stringify(getFormData($(form))),
						beforeSend: function() {
						},
						success: function(response) {
							alert('SUCCESS');
						},
						error: function(request, status, error) {
							alert('ERROR');
						}
					});
				}
			});
		}
	}

	submitSetting();
});
