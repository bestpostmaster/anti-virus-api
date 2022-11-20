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

	$('#uploadFromUrlLink').click(function()
		{
			setTimeout(function(){
				$('#file').fadeOut();
			}, 500);

			setTimeout(function(){
				$('#chooseFileLabel').fadeOut();
			}, 500);

			setTimeout(function(){
				$('#selectFileLink').fadeOut();
			}, 500);

			setTimeout(function(){
				$('#uploadFromUrlLink').fadeOut();
			}, 500);

			setTimeout(function(){
				$('#fileUrl').fadeIn();
			}, 500);

			setTimeout(function(){
				$('#selectFileLink').fadeIn();
			}, 500);
		}
	);

	$('#selectFileLink').click(function()
		{
			setTimeout(function(){
				$('#fileUrl').fadeOut();
			}, 500);

			setTimeout(function(){
				$('#selectFileLink').fadeOut();
			}, 500);

			setTimeout(function(){
				$('#file').fadeIn();
			}, 500);

			setTimeout(function(){
				$('#chooseFileLabel').fadeIn();
			}, 500);

			setTimeout(function(){
				$('#uploadFromUrlLink').fadeIn();
			}, 500);
		}
	);

	var updateTableView = function (elements) {
		elements.forEach(function(element, index, array)
		{
			let lastElement = null;
			let relatedActionsList = null;
			if (element.relatedActions && Array.isArray(element.relatedActions) && element.relatedActions.length >0) {
				lastElement = (element.relatedActions)[element.relatedActions.length -1];
				relatedActionsList = element.relatedActions;
			}

			if(relatedActionsList) {
				relatedActionsList.forEach(function (action, index, array) {
					if (!action.accomplished) {
						return;
					}

					$('#accomplished-' + action.id).html(action.accomplished);
					$('#startDate-' + action.id).html(action.startTime);
					$('#endDate-' + action.id).html(action.endTime);

					let actionResults = '';
					(action.actionResults).forEach(function (resultFile, index, array) {
						actionResults += '<a href="javascript:;" file_name="' + resultFile + '" link="/api/actions/download-action-result/' + action.id + '/' + resultFile + '" class="downloadResultRefreshed">' + resultFile + '</a><br>';
					});

					$('#actionResults-' + action.id).html(actionResults);
				});
			}

			if ($("#file-details-"+element.id)) {
				if (lastElement && lastElement.accomplished === true) {
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

		$('.downloadResultRefreshed').click(function() {
			let fileName = $(this).attr('file_name');
			let url = $(this).attr('link');

			var showFile = function (blob) {

				var data = '';

				try {
					var binaryData = [];
					binaryData.push(blob);
					data = window.URL.createObjectURL(new Blob(binaryData));
				}  catch (error) {
					console.error(error);
					alert('This file can not be downloaded');
					return;
				}

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
				error: function(request, status, error) {

				}
			});

		}, 5000);
	}

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
				initRefreshList(data, limit, offset);
			},
			error: function(request, status, error) {
				sessionStorage.clear();
				alert('Your are disconnected!');
				document.location.href="/";
			}
		});

		return dataReceived;
	}

	function displayFilesList(files, divId) {
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

		var generateFileDetails = function(id, elementDescription, name, url, actionName, status, result, relatedActionsList) {
			let description = '<input type="button" value="'+elementDescription.slice(-25)+'" class="downloadLink" file_name="'+name+'" url="'+url+'" description="'+elementDescription+'">'
			let rows = '<tr id="file-details-'+id+'">\n' +
				'				<td id="file-description-'+id+'">'+description+'</td>\n' +
				'				<td id="file-action-'+id+'">'+actionName+'</td>\n' +
				'				<td id="file-action-status-'+id+'">'+status+'</td>\n' +
				'				<td id="file-action-result-'+id+'">'+result+'</td>\n' +
				'				<td id="drop-down-'+id+'"><button type="button" class="btn btn-secondary btn-sm dropdown-toggle btn-dropdown" target="actions-list-'+id+'"></td>\n' +
				'			</tr>\n<tr id="actions-list-'+id+'" class="actions-list"><td colspan="5">__Actionslist__</td></tr>'
				;

			let actionsList = '<table class="details_table"><tr><td><b>Action name</b></td><td><b>Accomplished</b></td><td><b>Start date</b></td><td><b>End date</b></td><td><b>Action results</b></td></tr>';

			if (!relatedActionsList) {
				relatedActionsList = [];
			}

			relatedActionsList.forEach(function(action, index, array)
			{

				actionsList += '<tr><td>'+action.action.actionName+'</td><td id="accomplished-'+action.id+'">'+action.accomplished+'</td><td id="startDate-'+action.id+'">'+action.startTime+'</td><td id="endDate-'+action.id+'">'+action.endTime+'</td><td id="actionResults-'+action.id+'">__ActionResults__</td></tr>';

				if(action.accomplished && action.actionResults && Array.isArray(action.actionResults)) {
					let actionResults = '';
					(action.actionResults).forEach(function(resultFile, index, array)
					{
						actionResults += '<a href="javascript:;" file_name="'+resultFile+'" link="/api/actions/download-action-result/'+action.id+'/'+resultFile+'" class="downloadResult">'+resultFile+'</a><br>';
					});
					actionsList = actionsList.replace('__ActionResults__', actionResults);
				}

				if (actionsList.includes("__ActionResults__")) {
					actionsList = actionsList.replace('__ActionResults__', 'In progress..');
				}

			});

			let buttonsList = '<tr style="background-color: #FFFFFF;"><td colspan="5" class="btn_list">'+
				/*'<button type="button" class="btn btn-info">Edit</button>\n '+
				'<button type="button" class="btn btn-info">Share</button>\n '+
				'<button type="button" class="btn btn-info">New action</button>\n '+
				'<button type="button" class="btn btn-info">Light</button>\n '+ */
				'<button type="button" class="btn btn-danger btn_delete" id="btn_delete_'+id+'" file_id="'+id+'">Delete file</button></td></tr>';

			actionsList = actionsList + buttonsList+'</table>';

			return rows.replace('__Actionslist__', actionsList);
		}

		files.forEach(function(element, index, array)
			{
				lastOffset++;
				let description = '<input type="button" value="'+(element.description).slice(-25)+'" class="downloadLink" file_name="'+element.name+'" url="'+element.url+'" description="'+element.description+'">'

				let lastElement = null;
				let relatedActionsList = null;
				if (element.relatedActions && Array.isArray(element.relatedActions) && element.relatedActions.length >0) {
					lastElement = (element.relatedActions)[element.relatedActions.length -1];
					relatedActionsList = element.relatedActions;
				}

				if (lastElement && element.relatedActions[0].accomplished === false) {
					tableMiddle += generateFileDetails(element.id, element.description, element.name, element.url, lastElement.action.actionName, 'In progress..', '', relatedActionsList);
					return;
				}

				if (lastElement && element.infected) {
					tableMiddle += generateFileDetails(element.id, element.description, element.name, element.url, lastElement.action.actionName, 'Done', '<b style="color: darkred">!!>Infected!</b>', relatedActionsList);
					return;
				}

				if (lastElement && !element.infected) {
					tableMiddle += generateFileDetails(element.id, element.description, element.name, element.url, lastElement.action.actionName, 'Done', '<b style="color: #1e7e34">Is safe</b>', relatedActionsList);
					return;
				}

				if (element.relatedActions && !element.relatedActions[0]) {
					tableMiddle += generateFileDetails(element.id, element.description, element.name, element.url, '', '', '', relatedActionsList);
					return;
				}

				var relatedActions = (element.relatedActions && element.relatedActions[0]) ? element.relatedActions[0] : 'No action';

				tableMiddle += '<tr>\n' +
					'				<td>'+description+'</td>\n' +
					'				<td>'+relatedActions+'</td>\n' +
					'				<td>'+relatedActions+'</td>\n' +
					'				<td></td>\n' +
					'			</tr>\n'
			}
		);

		let tableFoot = '    </table><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" id="load-more">Load more..</a>';

		$("#"+divId).html(tableHead+tableMiddle+tableFoot);

		$('.downloadResult').click(function() {
			let fileName = $(this).attr('file_name');
			let url = $(this).attr('link');

			var showFile = function (blob) {

				var data = '';

				try {
					var binaryData = [];
					binaryData.push(blob);
					data = window.URL.createObjectURL(new Blob(binaryData));
				}  catch (error) {
					console.error(error);
					alert('This file can not be downloaded');
					return;
				}

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

		$('.btn-dropdown').click(function() {
			let target = $(this).attr('target');
			if (!$('#'+target).is(':visible')) {
				setTimeout(function(){
					$('#'+target).fadeIn();
				}, 500);
				return;
			}
			setTimeout(function(){
				$('#'+target).fadeOut();
			}, 500);
		});

		$('.btn_delete').click(function() {
			let fileId = $(this).attr('file_id');
			let url = '/api/files/delete/'+fileId;
			$.ajax({
				type: "DELETE",
				headers: {
					Authorization: 'Bearer '+sessionStorage.getItem('token')
				},
				url: url,
				contentType: false,
				dataType: "json",
				enctype: 'multipart/form-data',
				data: {},
				processData:false,

				beforeSend: function() {
				},
				success: function(json) {
					setTimeout(function(){
						$('#actions-list-'+json.fileId).fadeOut();
					}, 500);
					setTimeout(function(){
						$('#file-details-'+json.fileId).fadeOut();
					}, 500);
				},
				error: function(request, status, error) {
					alert('ERROR');
				}
			});
		});

		$('.downloadLink').click(function() {
			let fileName = $(this).attr('file_name');
			let url = '/api/files/download/'+$(this).attr('url');

			var showFile = function (blob) {

				var data = '';

				try {
					var binaryData = [];
					binaryData.push(blob);
					data = window.URL.createObjectURL(new Blob(binaryData));
				}  catch (error) {
					console.error(error);
					alert('This file can not be downloaded');
					return;
				}

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

				let lastElement = null;
				let relatedActionsList = null;
				if (element.relatedActions && Array.isArray(element.relatedActions) && element.relatedActions.length >0) {
					lastElement = (element.relatedActions)[element.relatedActions.length -1];
					relatedActionsList = element.relatedActions;
				}

				if (lastElement && element.relatedActions[0].accomplished === false) {
					trMiddle += generateFileDetails(element.id, element.description, element.name, element.url, lastElement.action.actionName, 'In progress..', '', relatedActionsList);
					return;
				}

				if (lastElement && element.infected) {
					trMiddle += generateFileDetails(element.id, element.description, element.name, element.url, lastElement.action.actionName, 'Done', '<b style="color: darkred">!!>Infected!</b>', relatedActionsList);
					return;
				}

				if (lastElement && !element.infected) {
					trMiddle += generateFileDetails(element.id, element.description, element.name, element.url, lastElement.action.actionName, 'Done', '<b style="color: #1e7e34">Is safe</b>', relatedActionsList);
					return;
				}

				trMiddle += generateFileDetails(element.id, element.description, element.name, element.url, '', '', '', relatedActionsList);
			});

			if (trMiddle !== '') {
				$("#files").append(trMiddle);

				$('.btn-dropdown').click(function() {
					let target = $(this).attr('target');
					if (!$('#'+target).is(':visible')) {
						setTimeout(function(){
							$('#'+target).fadeIn();
						}, 500);
						return;
					}
					setTimeout(function(){
						$('#'+target).fadeOut();
					}, 500);
				});

				$('.downloadResult').click(function() {
					let fileName = $(this).attr('file_name');
					let url = $(this).attr('link');

					var showFile = function (blob) {

						var data = '';

						try {
							var binaryData = [];
							binaryData.push(blob);
							data = window.URL.createObjectURL(new Blob(binaryData));
						}  catch (error) {
							console.error(error);
							alert('This file can not be downloaded');
							return;
						}

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
		}

		$('#load-more').click(function() {
			var elementsToAppend = getFilesList(50, lastOffset);
			if(elementsToAppend.length > 0) {
				appendElementsToList(elementsToAppend);
			}
		});
	}

	//-----------------------------------

	var uploadForm = function() {

		if (!sessionStorage.getItem('token') || sessionStorage.getItem('token')==='') {
			document.location.href="/";
			return;
		}

		let files = getFilesList();
		displayFilesList(files, 'files-list');
		initRefreshTokenCalls();

		if ($('#uploadForm').length > 0 ) {
			$( "#uploadForm" ).validate( {
				rules: {
					//file: "required",
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
					var data = new FormData(form);
					var url = "/api/files/upload";

					if (!data.get('fileUrl') && !data.get('file')) {
						alert('Please select file or URL');
						return;
					}

					if(data.get('fileUrl') && data.get('fileUrl') !== '') {
						url = "/api/files/upload-from-url";
						data.set('url', data.get('fileUrl'));
						data.delete('file');
					}

					$.ajax({
						type: "POST",
						headers: {
							Authorization: 'Bearer '+sessionStorage.getItem('token')
						},
						url: url,
						contentType: false,
						dataType: "json",
						enctype: 'multipart/form-data',
						data: data,
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
							$('#form-message-warning').html("Something went wrong. Please try again.")
								.fadeIn();
							$submit.css('display', 'none');
						}
					});
				}

			} );
		}
	};
	if (!sessionStorage.getItem('token') || sessionStorage.getItem('token')==='') {
		$('#uploadForm').hide();
		document.location.href="/";
		return;
	}
	uploadForm();
});
