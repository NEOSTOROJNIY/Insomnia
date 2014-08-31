	$(function() {


		/////////////////////////////////////////////
		// Downloading the report list. [PAGE BUILDERS]
		/////////////////////////////////////////////
		$.ajax({
			url: 'bootstrap.php',
			type: 'POST',
			data: { 'insomniaAction' : 'report_getReportList' },
			dataType: 'json',
			success: function(jsonReportList) {
				// iv got this list: { {fileLink, fileName}, .., .. }
				// need to parse

				// Getting count.
				var reportsCount = jsonReportList.length;

				// for each report we will make individuals link for filename and button for showing itself.
				for(var i = 0; i < reportsCount; ++i) {
					
					var li = $('<li></li>'); // creatng new row of #reportListBlock

					var deleteButton = $('<button class="close">&times;</button>'); // creating close button for current report link.

					var reportName = $('<small></small>');
					var downloadLink = $('<a name="downloadLink" href="' + jsonReportList[i]['fileLink'] + '"></a>'); // creating download link
					downloadLink.append('<i class="icon-download-alt"><i>');
					var reportLink = $('<a name="reportLink" href="#"></a>').append(jsonReportList[i]['fileName']); // creating 'show-report' link

					reportName.append(downloadLink);
					reportName.append(reportLink);
					
					// Registrates a function, that will be throwed when user has clicked on the 'show-report' link.
					// This function hides current showed report and shows report of the 'show-report' link.
					reportLink.click( function() { 
						var fileName = this.text;
						$.ajax({
							url: 'bootstrap.php',
							type: 'POST',
							data: { 'insomniaAction' : 'report_getReport',  'fileName' : fileName },
							dateType: 'xml',
							success: function(xmlData) {
								var report = buildReport(xmlData);
								$('#reportContent').fadeOut(400, function() {
									$(this).text('').append(report).fadeIn(400);					
								});
							},
							error: function() {
								$('#downloadingError_reportFile').modal('show');
							}

						});

					});

					li.append(deleteButton);
					li.append(reportName);

					$('#reportListBlock').append(li);

					// registrates close function for the close button
					$('.close').click(function() {
						var parent = $(this).parent();
						$(this).parent().slideUp(400);
						$.ajax({
							url: 'bootstrap.php',
							type: 'POST',
							data: { 'insomniaAction' : 'report_deleteReport', 'fileName' : parent.find('a').text() }
						});
					});

				}
			},
			error: function() {
				$('#downloadingError_reportList').modal('show');
			}
		});
		/////////////////////////////////////////////

	});

	function buildReport(xmlData) {
		var report = $('<div></div>'); 
		report.append(getTopicReportBlock(xmlData));
		report.append(getReportTabbableDataContainer(xmlData));
		return report;
	}

	// Making tabbable container of a report.
	function getReportTabbableDataContainer(xmlData) {
		var reportTabbableDataContainer = $('<div class="tabbable tabs-left"></div>');

		// Making NAV block.
		var navTabsBlock = $('<ul class="nav nav-tabs"></ul>');
		navTabsBlock.append('<li class="active"><a id="resourceTabToggler" href="#resourceTab" data-toggle="tab"><i class="icon-cogs"></i> Resource</a></li>');
		navTabsBlock.append('<li><a id="securityTabToggler" href="#securityTab" data-toggle="tab"><i class="icon-lock"></i> Security</a></li>');
		navTabsBlock.append('<li><a id="scanDetailsTabToggler" href="#scanDetailsTab" data-toggle="tab"><i class=" icon-qrcode"></i> Scan Details</a></li>');

		// Making TABS CONTENT block
		var tabsContentBlock = $('<div class="tab-content"></div>');

		// Making RESOURCE TAB block
		var resourceTabBlock = $('<div class="tab-pane active" id="resourceTab"></div>');
		var resourceTabContent = getResourceTabData(xmlData);
		resourceTabBlock.append(resourceTabContent);

		// Making SECURITY TAB block
		var securityTabBlock = $('<div class="tab-pane" id="securityTab"></div>');
		// Calculating Security Levels
		// complex security level
		var complexSecurityLevel = $(xmlData).find('securitylevel').text();
		// xss/sqli security levels
		var xssSecurityLevel = '-1';
		var sqliSecurityLevel = '-1';
		// checking and saving security levels of xss and slq injection tests.
		$(xmlData).find('testedvulnerabilities vulnerability').each(function() {
			switch($(this).text()){

				case 'XSS':
					xssSecurityLevel = $(this).attr('security_level');
					break;

				case 'SQL Injection':
					sqliSecurityLevel = $(this).attr('security_level');
					break;

				default:
					break;
			}
		});

		var securityTabContent = getSecurityTabData(complexSecurityLevel, xssSecurityLevel, sqliSecurityLevel);
		securityTabBlock.append(securityTabContent);

		// Making SCAN DETAILS TAB block
		var scanDetailsTabBlock = $('<div class="tab-pane" id="scanDetailsTab"></div>');
		var scanDetailsContent = getScanDetailsTabData(xmlData);
		scanDetailsTabBlock.append(scanDetailsContent);

		// Builds the full content block.
		tabsContentBlock.append(resourceTabBlock);
		tabsContentBlock.append(securityTabBlock);
		tabsContentBlock.append(scanDetailsTabBlock);

		// Appends tabs to the report tabblable data container
		reportTabbableDataContainer.append(navTabsBlock);
		reportTabbableDataContainer.append(tabsContentBlock);

		return reportTabbableDataContainer;
	}
	

	// Generating a Topic Report block.
	function getTopicReportBlock(xmlData) {
		var topicBlock = $('<blockquote></blockquote>');
		var brandBlock = $('<p><i class="icon-signal"></i> Insomnia Vulnerability Scan Report</p>');
		var dateBlock = $('<small></small>').append('Date: ' + $(xmlData).find('date').text());

		topicBlock.append(brandBlock);
		topicBlock.append(dateBlock);

		return topicBlock;
	}


	// Generating data of a Resource Tab
	function getResourceTabData(xmlData) {
		var resource = $(xmlData).find('resource');

		var resourceTabDataContainer = $('<p></p>');

		var headerBlock = $('<blockquote></blockquote>');
		var headerText = $('<p><strong><i class="icon-double-angle-right"></i> Resource Data</strong></p>');
		headerBlock.append(headerText);

		var dividerBlock = $('<hr>');

		//////////////////////////////////////////////////////////////////////////////////////////////////////
		var contentBlock = $('<dl></dl>');
		var testedResourceURLTitle = $('<dt>Resource URL</dt>');
		var testedResourceURL = $('<dd>' + resource.find('addrdata urladdr').text() + '</dd>');
		contentBlock.append(testedResourceURLTitle);
		contentBlock.append(testedResourceURL);

		var testedResourceIPTitle = $('<dt>Resource IP</dt>');
		var testedResourceIP = $('<dd>' + resource.find('addrdata ipaddr').text() + '</dd>');
		contentBlock.append(testedResourceIPTitle);
		contentBlock.append(testedResourceIP);

		var testedResourceServerBannerTitle = $('<dt>Server Banner</dt>');
		var testedResourceServerBanner = $('<dd>' + resource.find('serverbanner').text() + '</dd>');
		contentBlock.append(testedResourceServerBannerTitle);
		contentBlock.append(testedResourceServerBanner);

		//////////////////////////////////////////////////////////////////////////////////////////////////////
		var resourceInfoTable = $('<table class="table table-hover table-condensed"></table>');
		var resourceInfoTableBody = $('<tbody></tbody>');

		var resourceURLInfoBlock = $('<tr><td><strong>Resource URL</strong></td><td>' + resource.find('addrdata urladdr').text() + '</td></tr>');
		resourceInfoTableBody.append(resourceURLInfoBlock);

		var resourceIPInfoBlock = $('<tr><td><strong>Resource IP</strong></td><td>' + resource.find('addrdata ipaddr').text() + '</td></tr>');
		resourceInfoTableBody.append(resourceIPInfoBlock);

		var resourceServerBannerInfoBlock = $('<tr><td><strong>Server Banner</strong></td><td>' + resource.find('serverbanner').text() + '</td></tr>');
		resourceInfoTableBody.append(resourceServerBannerInfoBlock);

		resourceInfoTable.append(resourceInfoTableBody);
		//////////////////////////////////////////////////////////////////////////////////////////////////////

		// constructing
		resourceTabDataContainer.append(headerBlock);
		//resourceTabDataContainer.append(dividerBlock);
		//resourceTabDataContainer.append(contentBlock);

		resourceTabDataContainer.append(resourceInfoTable);

		return resourceTabDataContainer;
	}

	// Generating data of Security Tab
	function getSecurityTabData(complexSecurityLevel, xssSecurityLevel, sqliSecurityLevel) {

		var securityTabDataContainer = $('<p></p>');

		// Security Tab TOPIC HEADER
		var headerBlock = $('<blockquote></blockquote>');
		var headerText = $('<p><strong><i class="icon-double-angle-right"></i> Security Statistics</p>');
		headerBlock.append(headerText);

		var dividerBlock = $('<hr>');

		// Signal Security Level Information Text
		var securitySignalBlock = $('<div></div>');
		switch(complexSecurityLevel) {
			case '3':
				securitySignalBlock.addClass('alert alert-success');
				securitySignalBlock.append('<strong>Success!</strong> The Investigated Resource is not vulnerable!');
				break;

			case '2':
				securitySignalBlock.addClass('alert');
				securitySignalBlock.append('<strong>Warning!</strong> The Investigated Resource has some vulnerabilities!');
				break;

			case '1':
				securitySignalBlock.addClass('alert alert-error');
				securitySignalBlock.append('<strong>Danger!</strong> The Investigated Resource can be exploited!');
				break;

			default:
				break;
		}

		// Security Statistics TITLE
		var securityDetailsTitleBlock = $('<blockquote></blockquote>');
		var securityDetailsTitleText = $('<p>Security Details</p><small>Test techniques and their results</small>');
		securityDetailsTitleBlock.append(securityDetailsTitleText);

		// Security Statistics TABLE
		var securityDetailsContentBlock_table = $('<table class="table table-hover table-condensed"></table>');
		var securityDetailsContentBlock_tbody = $('<tbody></tbody>');

		// XSS TR
		var securityDetailsContentBlock_xssTR = $('<tr></tr>');
		securityDetailsContentBlock_xssTR.append('<td>XSS</td>');
		switch(xssSecurityLevel) {
			case '3':
				securityDetailsContentBlock_xssTR.append('<td><span class="text-success">Not Vulnerable</span></td>');
				break;

			case '2':
				securityDetailsContentBlock_xssTR.append('<td><span class="text-warning">Vulnerable</span></td>');
				break;

			case '1':
				securityDetailsContentBlock_xssTR.append('<td><span class="text-error">Can be Exploited</span></td>');
				break;

			case '-1':
				securityDetailsContentBlock_xssTR.append('<td><span class="muted">Not Tested</span></td>');
				break;

			default:
				break;
		}

		// SQL Injection TR
		var securityDetailsContentBlock_sqliTR = $('<tr></tr>');
		securityDetailsContentBlock_sqliTR.append('<td>SQL Injection</td>');
		switch(sqliSecurityLevel) {
			case '3':
				securityDetailsContentBlock_sqliTR.append('<td><span class="text-success">Not Vulnerable</span></td>');
				break;

			case '2':
				securityDetailsContentBlock_sqliTR.append('<td><span class="text-warning">Vulnerable</span></td>');
				break;

			case '1':
				securityDetailsContentBlock_sqliTR.append('<td><span class="text-error">Can be Exploited</span></td>');
				break;

			case '-1':
				securityDetailsContentBlock_sqliTR.append('<td><span class="muted">Not Tested</span></td>');
				break;

			default:
				break;
		}

		// Appends rows to TBODY
		securityDetailsContentBlock_tbody.append(securityDetailsContentBlock_xssTR);
		securityDetailsContentBlock_tbody.append(securityDetailsContentBlock_sqliTR);

		// Appends TBODY to TABLE.
		securityDetailsContentBlock_table.append(securityDetailsContentBlock_tbody);

		// Appends ALL
		securityTabDataContainer.append(headerBlock);  // Appends HEADER
		securityTabDataContainer.append(securitySignalBlock);
		securityTabDataContainer.append(dividerBlock); // Appends DIVIDER
		securityTabDataContainer.append(securityDetailsTitleBlock); // Appends SECURITY DETAILS
		securityTabDataContainer.append(securityDetailsContentBlock_table); // Appends SECURITY CONTENT

		return securityTabDataContainer;
	}

	// Generating data of Scan Details Tab
	function getScanDetailsTabData(xmlData) {
		var scanDetailsTabDataContainer = $('<p></p>');

		// Header
		var headerBlock = $('<blockquote></blockquote>');
		headerBlock.append('<p><strong><i class="icon-double-angle-right"></i> Scan Details</strong></p>');

		// Divider
		var dividerOneBlock = $('<hr>');

		// Tested vulnerabilities text info.
		var testedVulnerabilitiesDataBlock = $('<p></p>');
		testedVulnerabilitiesDataBlock.append('<strong>Tested vulnerabilites</strong>: ');
		// generates vulnerabilities list in the string form.
		var vulnerabilitiesList = new Array();
		$(xmlData).find('testedvulnerabilities vulnerability').each(function() { vulnerabilitiesList.push( $(this).text() ); } );
		// Appends vulnerabilities list into testedVulnerabilitiesDataBlock.
		if(vulnerabilitiesList.length !== 0 )
			testedVulnerabilitiesDataBlock.append(vulnerabilitiesList.join(', '));
		else {
			
			testedVulnerabilitiesDataBlock.append('Testing was not conducted.');
		}
			

		scanDetailsTabDataContainer.append(headerBlock);
		scanDetailsTabDataContainer.append(dividerOneBlock);
		scanDetailsTabDataContainer.append(testedVulnerabilitiesDataBlock);

		// Makin XSS Scan Details
		if($(xmlData).find('xsstestdetails').text()) {

			var xssDetailsHeaderBlock = $('<blockquote><p>XSS Scan Details</p><small>Vulnerable parameters and exploits.</small></blockquote>');
			var xssDetailsDataContainer = $('<p></p>');

			if($(xmlData).find('xsstestdetails notvulnerable').text()) {

				xssDetailsDataContainer.append('Not vulnerable.');

				scanDetailsTabDataContainer.append('<hr>');
				scanDetailsTabDataContainer.append(xssDetailsHeaderBlock);
				scanDetailsTabDataContainer.append(xssDetailsDataContainer);

			} else {

				var xssDetailsTriggeredTechniquesTextBlock = $('<p></p>');

				var xssVulnerabilitySubjectsBlock = $('<strong>Vulnerable to: </strong>');
				var xssVulnerabilitySubjects = new Array();

				$(xmlData).find('xsstestdetails subjects subject[type^="vulnerability"]').each(function() { xssVulnerabilitySubjects.push( $(this).text() ); });
				xssDetailsTriggeredTechniquesTextBlock.append(xssVulnerabilitySubjectsBlock);
				xssDetailsTriggeredTechniquesTextBlock.append(xssVulnerabilitySubjects.join(', '));

				var xssExploitSubjectsBlock = $('<strong>Can be exploited by: </strong>');
				var xssExploitSubjects = new Array();	
				$(xmlData).find('xsstestdetails subjects subject[type^="exploit"]').each(function() { xssExploitSubjects.push( $(this).text() ); });
				if(xssExploitSubjects.length !== 0) { 
					xssDetailsTriggeredTechniquesTextBlock.append('<br>');
					xssDetailsTriggeredTechniquesTextBlock.append(xssExploitSubjectsBlock);
					xssDetailsTriggeredTechniquesTextBlock.append(xssExploitSubjects.join(', '));					
				}


				var xssAccordionBlock = $('<div class="accordion" id="xssAccordion"></div>');

					// SCHEME:
					// accordion
					//	  accordion-group
					//       accordion-heading/ (accordion-toggle) _title_ (FIRST SUB)
					//       accordion-body
					//			accordion-inner/
					//   	 /accordion-body
					//    /accordion-group
					//	  accordion-group
					//		...	(SECOND SUB)
					//	  /accordion-group
					// accordion
				
				// making acordion groups
				$(xmlData).find('xsstestdetails vulnerableforms form').each(function ( ) {

					var formAccordionCollapseID = 'xssAccordion_formCollapse' + $(this).attr('form_id');

					// Making group block
					var formAccordionGroupBlock = $('<div class="accordion-group"></div>');
					// Making heading block
					var formAccordionHeadingBlock = $('<div class="accordion-heading"></div>');
					// Making toggler
					var formAccordionHeadingToggler = $('<a class="accordion-toggle" data-toggle="collapse" data-parent="#xssAccordion" href="#' + formAccordionCollapseID + '"></a>');
					formAccordionHeadingToggler.append('<i class="icon-retweet"></i> Vulnerable form: #' + $(this).attr('form_id'));
					formAccordionHeadingBlock.append(formAccordionHeadingToggler);

					// Making collapse block
					var formAccordionCollapse = $('<div id="' + formAccordionCollapseID + '" class="accordion-body collapse"></div>');
					// making data container for formAccordionCollapse
					var formAccordionCollapseDataContainer = $('<div class="accordion-inner"></div>');


					// Making accordions for PARAMS DATA
					$(this).find('vulnerableparam').each(function() {



						var paramName = $(this).find('paramname').text();
						var paramAccordionID = formAccordionCollapseID + '_paramAccordion_' + paramName;
						var paramAccordionCollapseID = paramAccordionID + '_collapse';

						// Making param accordion
						var paramAccordionBlock = $('<div class="accordion" id="' + paramAccordionID + '"></div>');

						// Making accordion group block
						var paramAccordionGroupBlock = $('<div class="accordion-group"></div>');
						// Making heading block
						var paramAccordionHeadingBlock = $('<div class="accordion-heading"></div>');

						// Making toggler
						var xssType = $(this).find('xsstype').text();

						var paramAccordionHeadingToggler = 
							$('<a class="accordion-toggle" data-toggle="collapse" data-parent="#' + paramAccordionID + '" href="#' + paramAccordionCollapseID + '"></a>');
						paramAccordionHeadingToggler.append('<i class="icon-random"></i> Vulnerable param: <strong>' + paramName + '</strong> [' + xssType + ']');
						paramAccordionHeadingBlock.append(paramAccordionHeadingToggler);

						// Making collapse block
						var paramAccordionCollapse = $('<div id="' + paramAccordionCollapseID + '" class="accordion-body collapse"></div>');
						// making data container for paramAccordionCollapse
						var paramAccordionCollapseDataContainer = $('<div class="accordion-inner"></div>');
						paramAccordionCollapseDataContainer.append('<strong>XSS type:</strong> ' + xssType + '<br>');

						// if exists vulnerable values *[check tech results] -> add it
						if($(this).find('vulnerablevalues').text()) {

							// Making title
							var vulnerableDataTitle = $('<strong>Vulnerable values:</strong>');
							// Making data table
							var vulnerableDataTable = $('<table class="table table-striped table-hover table-condensed table-bordered"></table>');
							// Making table body
							var vulnerableDataTableBody = $('<tbody></tbody>');
							var tableHeaders = $('<tr><td>Technique</td><td>Values</td></tr>');
							vulnerableDataTableBody.append(tableHeaders);

							$(this).find('vulnerablevalues checktechnique').each( function () {
								// Making technique/values TR
								var newTR = $('<tr></tr>');

								// Making technique TD Container
								var techniqueNameTD = $('<td>' + $(this).attr('technique_name') + '</td>');

								// Making values TD Container
								var valuesTD = $('<td></td>');

								// Making values UL content for values TD container
								var valuesListUL = $('<ul></ul>');
								$(this).find('value').each( function() { valuesListUL.append('<li>' + $(this).text() + '</li>'); });

								// Appends values values UL content to values TD container
								valuesTD.append(valuesListUL);

								// Building the new row
								newTR.append(techniqueNameTD);
								newTR.append(valuesTD);

								// Appends new row to the table body.
								vulnerableDataTableBody.append(newTR);
							});

							// Building the data table.
							vulnerableDataTable.append(vulnerableDataTableBody);

							// Adds content to the collapse container
							paramAccordionCollapseDataContainer.append(vulnerableDataTitle);
							paramAccordionCollapseDataContainer.append(vulnerableDataTable);
						}

						// if exists exploits *[inject tech resutls] -> add it
						if($(this).find('exploits').text()) {
							// Making title
							var exploitsDataTitle = $('<strong>Exploits:</strong>');
							// Making data table
							var exploitsDataTable = $('<table class="table table-striped table-hover table-condensed table-bordered"></table>');
							// Making table body
							var exploitsDataTableBody = $('<tbody></tbody>');
							var tableHeaders = $('<tr><td>Technique</td><td>Values</td></tr>');
							exploitsDataTableBody.append(tableHeaders);

							$(this).find('exploits exploittechnique').each( function() {
								// making new row
								var newTR = $('<tr></tr>');
								// Making technique name DD container
								var techniqueNameTD = $('<td>' + $(this).attr('technique_name') + '</td>');
								// Making values TD container
								var valuesTD = $('<td></td>');

								// Making OL containers for exploitkinds. 1 exploitkind -> one OL
								$(this).find('valuesqueue').each( function() {
									var dataListOL = $('<ol></ol>');

									var valueList = $(this).text().split(' ->-> ');
									for(var i = 0; i < valueList.length; ++i)
										dataListOL.append('<li>' + valueList[i] + '</li>')

									valuesTD.append(dataListOL);
								});

								var exploitDataDIV = $('<div class="well well-small"><strong>Exploit result</strong>:<br></div>');
								exploitDataDIV.append( $(this).find('exploitdata').text() );
								valuesTD.append(exploitDataDIV);

								// making new row
								newTR.append(techniqueNameTD);
								newTR.append(valuesTD);

								// adds new row to table.
								exploitsDataTableBody.append(newTR);
							});

							// building exploits table.
							exploitsDataTable.append(exploitsDataTableBody);

							// Adds content to the collapse container
							paramAccordionCollapseDataContainer.append(exploitsDataTitle);
							paramAccordionCollapseDataContainer.append(exploitsDataTable);
						}

						// End of making the collapse
						paramAccordionCollapse.append(paramAccordionCollapseDataContainer);

						// Building the param accordion
						paramAccordionGroupBlock.append(paramAccordionHeadingBlock); // GROUP <- HEADING
						paramAccordionGroupBlock.append(paramAccordionCollapse); // GROUP <- COLLAPSE
						paramAccordionBlock.append(paramAccordionGroupBlock); // ACCORDION <- GROUP

						// Appends PARAM ACCORDIONT TO THE FORM ACCORDION
						formAccordionCollapseDataContainer.append(paramAccordionBlock);
					});
					
					// making THE FORM ACCORDION

					formAccordionCollapse.append(formAccordionCollapseDataContainer);

					formAccordionGroupBlock.append(formAccordionHeadingBlock);
					formAccordionGroupBlock.append(formAccordionCollapse);

					// Appends THE FORM to the GENERAL ACCORDION BLOCK
					xssAccordionBlock.append(formAccordionGroupBlock);
				});


				xssDetailsDataContainer.append(xssDetailsTriggeredTechniquesTextBlock);
				xssDetailsDataContainer.append(xssAccordionBlock);

				scanDetailsTabDataContainer.append('<hr>');
				scanDetailsTabDataContainer.append(xssDetailsHeaderBlock);
				scanDetailsTabDataContainer.append(xssDetailsDataContainer);
			}
		}

		if($(xmlData).find('sqlinjtestdetails').text()) {

			var sqliDetailsHeaderBlock = $('<blockquote><p>SQL Injection Scan Details</p><small>Vulnerable parametrs, requests and epxloits.</small></blockquote>');
			var sqliDetailsDataContainer = $('<p></p>');

			if($(xmlData).find('sqlinjtestdetails notvulnerable').text()) {

				sqliDetailsDataContainer.append('Not vulnerable.');

				scanDetailsTabDataContainer.append('<hr>');
				scanDetailsTabDataContainer.append(sqliDetailsHeaderBlock);
				scanDetailsTabDataContainer.append(sqliDetailsDataContainer);

			} else {

				var sqliDetailsTriggeredTechniquesTextBlock = $('<p></p>');
				var sqliVulnerabilitySubjectsBlock = $('<strong>Vulnerable to: </strong>');
				var sqliVulnerabilitySubjects = new Array();

				$(xmlData).find('sqlinjtestdetails subjects subject[type^="vulnerability"]').each(function() { sqliVulnerabilitySubjects.push( $(this).text() ); });
				sqliDetailsTriggeredTechniquesTextBlock.append(sqliVulnerabilitySubjectsBlock);
				sqliDetailsTriggeredTechniquesTextBlock.append(sqliVulnerabilitySubjects.join(', '));

				var sqliExploitSubjectsBlock = $('<strong>Can be exploited by: </strong>');
				var sqliExploitSubjects = new Array();	
				$(xmlData).find('sqlinjtestdetails subjects subject[type^="exploit"]').each(function() { sqliExploitSubjects.push( $(this).text() ); });
				if(sqliExploitSubjects.length !== 0) {
					sqliDetailsTriggeredTechniquesTextBlock.append('<br>');					
					sqliDetailsTriggeredTechniquesTextBlock.append(sqliExploitSubjectsBlock);
					sqliDetailsTriggeredTechniquesTextBlock.append(sqliExploitSubjects.join(', '));					
				}


				var possibleDB = $(xmlData).find('sqlinjtestdetails possibledb').text();
				sqliDetailsTriggeredTechniquesTextBlock.append('<br>').append('<strong>Possible DB:</strong> ' + possibleDB);

				var sqliAccordionBlock = $('<div class="accordion" id="sqliAccordion"></div>');

				// Searching vulnerable params and construct data blocks with theirs
				$(xmlData).find('sqlinjtestdetails vulnerableparams vulnerableparam').each(function() {

					var paramName = $(this).find('paramname').text();

					// Making accordion group
					var paramGroupBlock = $('<div class="accordion-group"></div>');
					var paramCollapseBlockID = 'sqliAccordion_paramCollapse_' + paramName;
					
					// Making heading block
					var paramHeadingBlock = $('<div class="accordion-heading"></div>');
					// Making toggler
					var paramHeadingToggler =
						$('<a class="accordion-toggle" data-toggle="collapse" data-parent="#sqliAccordion" href="#' + paramCollapseBlockID + '"></a>');
					paramHeadingToggler.append('Vulnerable param: ' + paramName);
					paramHeadingBlock.append(paramHeadingToggler);

					// Making collapse block
					var paramCollapseBlock = $('<div id="' + paramCollapseBlockID + '" class="accordion-body collapse"></div>');
					// Making data container for paramCollapseBlock
					var paramCollapseBlockDataContainer = $('<div class="accordion-inner"></div>');

					// if exists vulnerable requests *[check tech results] -> add it
					if($(this).find('vulnerablerequests').text()) {

						if($(this).find('checktechnique').text()) {
							// Making title
							var vulnerableDataTitle = $('<strong>Vulnerable Requests:</strong>');
							// Making data table
							var vulnerableDataTable = $('<table class="table table-striped table-hover table-condensed table-bordered"></table>');
							// Making table body
							var vulnerableDataTableBody = $('<tbody></tbody>');
							var tableHeaders = $('<tr><td>Technique</td><td>Requests</td></tr>');

							vulnerableDataTableBody.append(tableHeaders);	

							$(this).find('checktechnique').each(function() {
								// Making technique/values TR
								var newTR = $('<tr></tr>');

								// Making technique TD Container
								var techniqueNameTD = $('<td>' + $(this).attr('technique_name') + '</td>');

								// Making values TD Container
								var valuesTD = $('<td></td>');

								// Making values UL content for values TD container
								var valuesListUL = $('<ul></ul>');
								$(this).find('request').each(function() { valuesListUL.append('<li>' + $(this).text() + '</li>'); });

								// Appends values UL content to the values TD container
								valuesTD.append(valuesListUL);

								// Building the new row
								newTR.append(techniqueNameTD);
								newTR.append(valuesTD);

								// Appends new row to the table body
								vulnerableDataTableBody.append(newTR);
							});		

							vulnerableDataTable.append(vulnerableDataTableBody);

							paramCollapseBlockDataContainer.append(vulnerableDataTitle);
							paramCollapseBlockDataContainer.append(vulnerableDataTable);
						}


						if($(this).find('exploits').text()) {
							// Making title
							var exploitsDataTitle = $('<strong>Exploits:</strong>');
							// Making data table
							var exploitsDataTable = $('<table class="table table-striped table-hover table-condensed table-bordered"></table>');
							// Making table body
							var exploitsDataTableBody = $('<tbody></tbody>');
							var tableHeaders = $('<tr><td>Technique</td><td>Exploit</td></tr>');

							exploitsDataTableBody.append(tableHeaders);

							$(this).find('exploittechnique').each(function() {

								var newTR = $('<tr></tr>');
								var techniqueNameTD = $('<td>' + $(this).attr('technique_name') + '</td>');
								var exploitsTD = $('<td></td>');

								$(this).find('resultqueue').each(function() {
									var exploitListOL = $('<ol></ol>');

									var exploitList = $(this).text().split(' ->-> ');
									for(var i = 0; i < exploitList.length; ++i)
										exploitListOL.append('<li>' + exploitList[i] + '</li>')

									exploitsTD.append(exploitListOL);	
								});

								var exploitDataDIV = $('<div class="well well-small"><strong>Exploit result</strong>:<br></div>');
								exploitDataDIV.append( $(this).find('exploitdata').text() );
								exploitsTD.append(exploitDataDIV);


								newTR.append(techniqueNameTD);
								newTR.append(exploitsTD);

								exploitsDataTableBody.append(newTR);
							});

							exploitsDataTable.append(exploitsDataTableBody);

							paramCollapseBlockDataContainer.append(exploitsDataTitle);
							paramCollapseBlockDataContainer.append(exploitsDataTable);
						}
					}

					paramCollapseBlock.append(paramCollapseBlockDataContainer);

					paramGroupBlock.append(paramHeadingBlock);
					paramGroupBlock.append(paramCollapseBlock);

					sqliAccordionBlock.append(paramGroupBlock);

				});

				sqliDetailsDataContainer.append(sqliDetailsTriggeredTechniquesTextBlock);
				sqliDetailsDataContainer.append(sqliAccordionBlock);

				scanDetailsTabDataContainer.append('<hr>');
				scanDetailsTabDataContainer.append(sqliDetailsHeaderBlock);
				scanDetailsTabDataContainer.append(sqliDetailsDataContainer);
			}
		}

		return scanDetailsTabDataContainer;
	}