$(function() {

	'use strict';
	var lastOffset = 0

	$('#uploadAnotherFile').click(function() {
		$('#form-message-warning').hide();
		setTimeout(function(){
			$('#form-upload-success').fadeOut();
		}, 1000);
		setTimeout(function(){
			$('#uploadForm').fadeIn();
		}, 1400);
	});

	var updateTableView = function (elements) {
		elements.forEach(function(element, index, array)
		{
			if ($("#file-details-"+element.id)) {
				if (element.actionsRequested[0] && element.actionsRequested[0].accomplished === true) {
					$("#file-action-status-"+element.id).html('Done');
					if (element.infected) {
						$("#file-action-result-"+element.id).html('<b style="color: darkred">!!>Infected!</b>');
						return;
					}
					$("#file-action-result-"+element.id).html('<b style="color: #1e7e34">Is safe</b>');
					return;
				}

				$("#file-action-status-"+element.id).html('In progress..')
				$("#file-action-result-"+element.id).html('');
			}
		});
	}

	function initRefreshList(response, limit, offset) {
		setTimeout(function(){

			$.ajax({
				type: "POST",
				url: "/api/files/"+limit+'/'+offset,
				contentType: "application/json",
				dataType: "json",
				headers: {
					Authorization: 'Bearer '+sessionStorage.getItem('token')
				},

				beforeSend: function() {
				},
				success: function(response) {
					updateTableView(response);

					initRefreshList(response, limit, offset);
				},
				error: function() {

				}
			});

		}, 5000);
	}

	var getFilesList = function(limit = 10, offset = 0) {
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
				initRefreshList(data, limit, offset);
			},
			error: function() {
				sessionStorage.clear();
				alert('Your are disconnected!');
				document.location.href="/";
			}
		});

		return dataReceived;
	}

	function displayFilesList(files, divId) {
		console.log('displayFilesList', files, divId);
		let tableHead = '<table id="files" class="table table-striped" style="width:100%">\n' +
			'        <thead>\n' +
			'            <tr>\n' +
			'                <th>File name</th>\n' +
			'                <th>Actions</th>\n' +
			'                <th>Status</th>\n' +
			'                <th>Result</th>\n' +
			'            </tr>\n' +
			'        </thead>\n';

		let tableMiddle = '';

		var generateFileDetails = function(id, elementDescription, name, url, actionName, status, result) {
			let description = '<input type="button" value="'+elementDescription+'" class="downloadLink" file_name="'+name+'" url="'+url+'" description="'+elementDescription+'">'
			return '<tr id="file-details-'+id+'">\n' +
				'				<td id="file-description-'+id+'">'+description+'</td>\n' +
				'				<td id="file-action-'+id+'">'+actionName+'</td>\n' +
				'				<td id="file-action-status-'+id+'">'+status+'</td>\n' +
				'				<td id="file-action-result-'+id+'">'+result+'</td>\n' +
				'			</tr>\n';
		}

		files.forEach(function(element, index, array)
			{
				lastOffset++;
				let description = '<input type="button" value="'+element.description+'" class="downloadLink" file_name="'+element.name+'" url="'+element.url+'" description="'+element.description+'">'

				if (element.actionsRequested[0] && element.actionsRequested[0].accomplished === false) {
					tableMiddle += generateFileDetails(element.id, element.description, element.name, element.url, element.actionsRequested[0].action.actionName, 'In progress..', '');
					return;
				}

				if (element.actionsRequested[0] && element.infected) {
					tableMiddle += generateFileDetails(element.id, element.description, element.name, element.url, element.actionsRequested[0].action.actionName, 'Done', '<b style="color: darkred">!!>Infected!</b>');
					return;
				}

				if (element.actionsRequested[0] && !element.infected) {
					tableMiddle += generateFileDetails(element.id, element.description, element.name, element.url, element.actionsRequested[0].action.actionName, 'Done', '<b style="color: #1e7e34">Is safe</b>');
					return;
				}

				var actionsRequested = element.actionsRequested[0] ? element.actionsRequested[0] : 'No action';

				tableMiddle += '<tr>\n' +
					'				<td>'+description+'</td>\n' +
					'				<td>'+actionsRequested+'</td>\n' +
					'				<td>'+actionsRequested+'</td>\n' +
					'				<td></td>\n' +
					'			</tr>\n'
			}
		);

		let tableFoot = '    </table><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" id="load-more">Load more..</a>';

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

		var appendElementsToList = function(elementsToAppend) {
			let trMiddle = '';
			elementsToAppend.forEach(function(element, index, array)
			{
				lastOffset++;

				if (element.actionsRequested[0].accomplished === false) {
					trMiddle += generateFileDetails(element.id, element.description, element.name, element.url, element.actionsRequested[0].action.actionName, 'In progress..', '');
					return;
				}

				if (element.infected) {
					trMiddle += generateFileDetails(element.id, element.description, element.name, element.url, element.actionsRequested[0].action.actionName, 'Done', '<b style="color: darkred">!!>Infected!</b>');
					return;
				}

				if (!element.infected) {
					trMiddle += generateFileDetails(element.id, element.description, element.name, element.url, element.actionsRequested[0].action.actionName, 'Done', '<b style="color: #1e7e34">Is safe</b>');
					return;
				}
			});
			$("#files").append(trMiddle);
		}

		$('#load-more').click(function() {
			var elementsToAppend = getFilesList(10, lastOffset);
			if(elementsToAppend.length > 0) {
				appendElementsToList(elementsToAppend);
			}
		});
	}

	//-----------------------------------

	var uploadForm = function() {

		let files = getFilesList();
		displayFilesList(files, 'files-list');
		initRefreshTokenCalls();

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
});
