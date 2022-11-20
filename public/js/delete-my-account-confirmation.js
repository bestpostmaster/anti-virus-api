$(function() {
	'use strict';

	function getFormFromJson(userInfos){

	}

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

	function updateFormView(content) {
		$('#deleteMyAccountForm').html(content);
	}

	var submitDeleteMyAccountForm = function() {
		var form = $('#deleteMyAccountForm');
		$.ajax({
			type: "POST",
			url: "/api/users/delete-my-account-confirmed",
			contentType: "application/json",
			dataType: "json",
			data: JSON.stringify(getFormData(form)),

			beforeSend: function() {
			},
			success: function(response) {
				$('#deleteMyAccountForm').html('<h1>All your personal data has been deleted</h1>');
			},
			error: function(request, status, error) {
				alert('ERROR');
				$('#deleteMyAccountForm').html('<h1>A technical problem has occurred. This account may already be deleted.</h1>');
			}
		});
	}

	submitDeleteMyAccountForm();
});
