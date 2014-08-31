$(function() {

	$('#saveProject').click(function() {
		$('#modal_saveProject').modal('show');
	});

	$('#openProject').click(function() {
		$('#modal_openProject').modal('show');
		
		$.ajax({
			url: 'bootstrap.php',
			type: 'POST',
			data: { 'insomniaAction' : 'project_getProjectList' },
			success: function(jsonProjectList) {
				
				var projects = JSON.parse(jsonProjectList);

				var projectList = $('<ul class="nav nav-pills nav-stacked"></ul>');

				for(var i = 0; i < projects.length; ++i) {

					var projectEntity = $('<li></li>');

					projectEntity.append('<a href="#" name="' + projects[i] + '"><i class="icon-sitemap"></i> ' + projects[i] + '<button class="close">&times;</button></a>');

					projectList.append(projectEntity);
				}
				
				$('#modal_openProject_projectListBlock').empty().append('Saved Projects: ').append(projectList);

				$('#modal_openProject_projectListBlock li a').click( function() {
					$('#modal_openProject_projectListBlock li').removeClass('active');
					$(this).parent().addClass('active');
				});

				$('#modal_openProject_projectListBlock li a .close').click(function() {

					$(this).parent().slideUp(400, function(){ $(this).parent().remove(); } );

					$.ajax({
						url: 'bootstrap.php',
						type: 'POST',
						data: { 'insomniaAction' : 'project_deleteProject', 'fileName' : $(this).parent().attr('name') }
					});

				});
			}
		});

	});

	$('#modal_openProject_openButton').click( function() {
		var projectName = $('#modal_openProject li.active a').attr('name');
		
		if(projectName === undefined) {
			$('#modal_openProject_error_fileNotChosen').modal('show');
		} else {
			$.ajax({
				url: 'bootstrap.php',
				type: 'POST',
				data: { 'insomniaAction' : 'project_getProject', 'fileName' : projectName },
				success: function(xmlData) {
					$('#modal_openProject').modal('hide');

					//var taskCount = $('input[name^="_res"]').length;
					var taskCount = 0;
					$('#targetData tbody').empty();

					$('#targetData tbody').append('<tr><td><i class="icon-globe"></i> Resource URL</td><td>SQL Injection</td><td>XSS</td></tr>');

					$(xmlData).find('tasks task').each( function() {
						var newTaskTR = $('<tr></tr>');
						taskCount += 1;

						var urlBlock = $(
							'<td><div class="input-prepend">' +
								'<span class="add-on"><i class="icon-cloud"></i></span>' +
								'<input type="text" name="_res' + taskCount + '" class="span6" placeholder="URL" value="' + $(this).attr('url') + '">' +
							'</div></td>'
						);

						var assignedToSQLInjection = ($(this).find('assignedtosqli').text() === 'true') ? 'checked' : '';
						var sqliBlock = $( '<td><input type="checkbox" name="_sqli' + taskCount + '" ' + assignedToSQLInjection + '></td>' );

						var assignedToXSS = ($(this).find('assignedtoxss').text() === 'true') ? 'checked' : '';
						var xssBlock = $( '<td><input type="checkbox" name="_xss' + taskCount + '" ' + assignedToXSS + '></td>' );

						newTaskTR.append(urlBlock).append(sqliBlock).append(xssBlock);

						$('#targetData tbody').append(newTaskTR);
					});
				}
			});
		}
	});


	$('#modal_saveProject_success_fileSaved').on('hide', function() {
		$('#modal_saveProject').modal('hide');
	});


	$('#modal_saveProject_saveButton').click( function() {

		var projectFileName = $('#modal_saveProject_fileNameInput').val();

		if(projectFileName.length === 0) {
			$('#modal_saveProject_error_emptyFileName').modal('show');

		} else {
			var dataCount = $('input[name^="_res"]').length;
			var projectData = new Object();
			
			projectData.fileName = projectFileName;
			projectData.res  = new Array();
			projectData.sqli = new Array();
			projectData.xss  = new Array();

			for(var i = 1; i <= dataCount; ++i) {
				var resName  = "input[name='_res" + i + "']";
				var sqliName = "input[name='_sqli" + i + "']";
				var xssName  = "input[name='_xss" + i + "']";

				projectData.res.push( $(resName).val() );
				projectData.sqli.push( ($(sqliName).prop('checked')) ? 'true' : 'false' );
				projectData.xss.push( ($(xssName).prop('checked')) ? 'true' : 'false' );
			}

			$.ajax({
				url: 'bootstrap.php',
				type: 'POST',
				data: { 'insomniaAction': 'project_saveProject', 'saveData': JSON.stringify(projectData) },
				success: function(print_r) {

					var responseMessage = JSON.parse(print_r);

					if(responseMessage.error !== undefined) {
						$('#modal_saveProject_error_fileNotSaved').modal('show');
					} else {
						$('#modal_saveProject_success_fileSaved').modal('show');
					}
					
				}
			});


		}
	});

	// Binding click function to #addNewTarget Button.
	// Making new row in target table for inserting new target data.
	$('#addNewTarget').click(function() {
		var resPrefix  = '_res',
		sqliPrefix = '_sqli',
		xssPrefix  = '_xss';

		var targetPostfix = $('input[name^="_res"]').length + 1;

		var targetResName  = resPrefix  + targetPostfix,
		targetSQLIName = sqliPrefix + targetPostfix,
		targetXSSName  = xssPrefix  + targetPostfix;

		var targetResInput = $('<input>', {
			type: 'text',
			name: targetResName,
			placeholder: 'URL',
			class: 'span6'
		});

		var targetSQLIInput = $('<input>', {
			type: 'checkbox',
			name: targetSQLIName,
			value: 'true'
		});

		var targetXSSInput = $('<input>', {
			type: 'checkbox',
			name: targetXSSName,
			value: 'true'
		});

		var customizedTargetResInput = $('<div class="input-prepend"></div>');
		customizedTargetResInput.append('<span class="add-on"><i class="icon-cloud"></i></span>');
		customizedTargetResInput.append(targetResInput);

		var res  = $('<td></td>').append(customizedTargetResInput);
		var sqli = $('<td></td>').append(targetSQLIInput);
		var xss  = $('<td></td>').append(targetXSSInput);
		var row  = $('<tr></tr>').append(res).append(sqli).append(xss);
			
		$('#targetData tbody').append(row.fadeIn(300));
	});


	// Binding ajax function to #startTesting button.
	// Parses testingData table into array form for testing.
	$('#startTesting').click(function() {

		var dataCount = $('input[name^="_res"]').length;

		var testData = new Object();

		testData.res  = new Array();
		testData.sqli = new Array();
		testData.xss  = new Array();

		for(var i = 1; i <= dataCount; ++i) {
			var resName  = "input[name='_res" + i + "']";
			var sqliName = "input[name='_sqli" + i + "']";
			var xssName  = "input[name='_xss" + i + "']";

			testData.res.push( $(resName).val() );
			testData.sqli.push( ($(sqliName).prop('checked')) ? 'true' : 'false' );
			testData.xss.push( ($(xssName).prop('checked')) ? 'true' : 'false' );
		}

		$('#infoArea_targetProcessing').slideUp(1000);

		var progressSpinner = $('<p id="spinnerTesting" class="pager large lead inline"><i class="icon-spinner icon-spin"></i> Testing...</p>');

		$('#infoArea').append(progressSpinner.fadeIn(400));

		/* 
		*	[
		*		{
		*		 "resource":"http://localhost/_helpers/inject.php?id=1",
		*		 "securityLevel":2,
		*		 "vulnerabilities": [
		*		 	{"vulnerability":"XSS",
		*		 	 "securityLevel":2},
		*		 	{"vulnerability":"XSS","securityLevel":2}
		*		 ],
		*		 "reportLinks":{"xmlReportLink":"localhost/reports/AZAZA.xml"}
		*		},
		*		...
		*	]
		*/
		
		$.ajax({
			url: 'bootstrap.php',
			type: 'POST',
			data: { 'insomniaAction': 'project_startTesting', 'testingData': JSON.stringify(testData) },
			success: function(testResults) {

				parsedTestResults = JSON.parse(testResults);

				var resultsCount = parsedTestResults.length;
				var response = $('<div class="littleDown"></div>');

				for(var i = 0; i < resultsCount; ++i) {
					var reportBlock = getMiniReportBlock(
						parsedTestResults[i].resource,
						parsedTestResults[i].securityLevel,
						parsedTestResults[i].vulnerabilities,
						parsedTestResults[i].reportLinks
					);
					response.append(reportBlock);
				}
				
				$('#spinnerTesting').slideUp(400);
				$('#infoArea').append(response.fadeIn(400));
				//$('#infoArea').append(testResults);


			}
		});

	});
});


function getMiniReportBlock(resourceURL, securityLevel, vulnerabilities, reportLinks) {

	var miniReportBlock = $('<div class="well well-small"></div>');
	var dataList = $('<dl class="dl-horizontal"></dl>');

	// Report file links (XML/PDF)
	dataList.append('<dt>Report</dt>');
	var reportsDataBlock = $('<dd></dd>').append('XML: ');
	var xmlReportFileLink = $('<a target="_blank" href="' + reportLinks.xmlReportLink + '"></a>').append(reportLinks.xmlReportLink);
	//var pdfReportFileLink = $('<a href="' + reportLinks.pdfReportLink + '"></a>').append(reportLinks.pdfReportLink);
	reportsDataBlock.append(xmlReportFileLink);
	// reportsDataBlock.append(pdfREportFileLink);
	dataList.append(reportsDataBlock);

	// Resource data
	dataList.append('<dt>Resource</dt>');
	var resourceBlock = $('<dd></dd>');
	var resourceLinkBlock = $('<a target="_blank" href="' + resourceURL + '"></a>').append(resourceURL);
	resourceBlock.append(resourceLinkBlock);
	dataList.append(resourceBlock);

	// Security Level
	dataList.append('<dt>Security Level</dt>');
	var securityDataBlock = $('<dd></dd>');
	switch(securityLevel) {
		case 3:
			securityDataBlock.append('<span class="label label-success">Success!</span>');
			break;
		case 2:
			securityDataBlock.append('<span class="label label-warning">Warning!</span>');
			break;
		case 1:
			securityDataBlock.append('<span class="label label-important">Danger!</span>');
			break;
		default:
			securityDataBlock.append('<span class="label">WTF!?</span>');
	}
	dataList.append(securityDataBlock);

	// Vulnerabilities
	//,"vulnerabilities":[{"vulnerability":"XSS","securityLevel":2},{"vulnerability":"SQL Injection","securityLevel":-1}]
	dataList.append('<dt>Vulnerabilities</dt>');
	var vulnerabilitiesBlock = $('<dd></dd>');
	vulnerabilitiesCount = vulnerabilities.length;

	for(var i = 0; i < vulnerabilitiesCount; ++i) {
		switch(vulnerabilities[i].securityLevel) {
			case 3:
				vulnerabilityDataBlock = $('<span class="badge badge-success">' + vulnerabilities[i].vulnerability + '</span>');
				vulnerabilitiesBlock.append(vulnerabilityDataBlock);
				break;
			case 2:
				vulnerabilityDataBlock = $('<span class="badge badge-warning">' + vulnerabilities[i].vulnerability + '</span>');
				vulnerabilitiesBlock.append(vulnerabilityDataBlock);
				break;
			case 1:
				vulnerabilityDataBlock = $('<span class="badge badge-important">' + vulnerabilities[i].vulnerability + '</span>');
				vulnerabilitiesBlock.append(vulnerabilityDataBlock);
				break;
			case -1:
				vulnerabilityDataBlock = $('<span class="badge">' + vulnerabilities[i].vulnerability + '</span>');
				vulnerabilitiesBlock.append(vulnerabilityDataBlock);
				break;												
		}
	}
	dataList.append(vulnerabilitiesBlock);


	miniReportBlock.append(dataList);
	return miniReportBlock;
}