<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>insomnia - Statistics</title>

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/docs.css">
	<link rel="stylesheet" href="css/jquery.jqplot.css">	
	<link rel="stylesheet" href="css/custom.css">
	
	<script type="text/javascript" src="js/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery.jqplot.min.js"></script>
	<script type="text/javascript" src="js/excanvas.min.js"></script>
	<script type="text/javascript" src="js/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="js/plugins/jqplot.pointLabels.min.js"></script>

	<script type="text/javascript">
	$(function() {

		$('#statistics_сlearStatistics').click(function() {
			
			$.ajax({
				url: 'bootstrap.php',
				type: 'POST',
				data: {'insomniaAction' : 'statistics_clearStats'},
				success: function(data) {
					location.href = 'statistics.php';
				}
			})
		});

		$('#statistics_Tab a[href="#statistics_GeneralStatistics"]').click(function() {
			$('#generalStatistics_complexChart').empty();
			$('#generalStatistics_specialChart').empty();
		});

		$('#statistics_Tab a[href="#statistics_SQLInjectionStatistics"]').click(function() {
			$('#sqliStatistics_specialChart').empty();
			$('#sqliStatistics_overallChart').empty();
		});

		$('#statistics_Tab a[href="#statistics_XSSTatistics"]').click(function() {
			$('#xssStatistics_specialChart').empty();
			$('#xssStatistics_overallChart').empty();		
		});	


		$('#statistics_Tab a[href="#statistics_GeneralStatistics"]').on('shown', function (e) {
			e.preventDefault();
			$(this).tab('show');

			$.ajax({
				url: 'bootstrap.php',
				type: 'POST',
				data: { 'insomniaAction' : 'statistics_getGeneralStatistics' },
				success: function(statisticsDataCollection) {
					var statisticsData = JSON.parse(statisticsDataCollection);

					var sqliTechStatsCount = statisticsData['techniqueStats_SQLInjection'].length;
					var xssTechStatsCount  = statisticsData['techniqueStats_XSS'].length;

					var generalStats_totalCount       = statisticsData['GeneralStats']['totalCount'];
					var generalStats_successCount     = statisticsData['GeneralStats']['successCount'];
					var generalStats_vulnerableCount  = statisticsData['GeneralStats']['vulnerableCount'];
					var generalStats_exploitableCount = statisticsData['GeneralStats']['exploitableCount'];

					var sqli_totalCount       = statisticsData['SQLInjectionStats']['totalCount'];
					var sqli_successCount     = statisticsData['SQLInjectionStats']['successCount'];
					var sqli_vulnerableCount  = statisticsData['SQLInjectionStats']['vulnerableCount'];
					var sqli_exploitableCount = statisticsData['SQLInjectionStats']['exploitableCount'];

					var xss_totalCount       = statisticsData['XSSStats']['totalCount'];
					var xss_successCount     = statisticsData['XSSStats']['successCount'];
					var xss_vulnerableCount  = statisticsData['XSSStats']['vulnerableCount'];
					var xss_exploitableCount = statisticsData['XSSStats']['exploitableCount'];

					var sqli_techniqueStats = statisticsData['techniqueStats_SQLInjection'];
					var xss_techniqueStats  = statisticsData['techniqueStats_XSS'];

					$('#generalStatistics_complexllTaskStatisticsData .badge-info').html(generalStats_totalCount);
					$('#generalStatistics_complexllTaskStatisticsData .badge-success').html(generalStats_successCount);
					$('#generalStatistics_complexllTaskStatisticsData .badge-warning').html(generalStats_vulnerableCount);
					$('#generalStatistics_complexllTaskStatisticsData .badge-important').html(generalStats_exploitableCount);

					$('#generalStatistics_sqliTaskStatisticsData .badge-info').html(sqli_totalCount);
					$('#generalStatistics_sqliTaskStatisticsData .badge-success').html(sqli_successCount);
					$('#generalStatistics_sqliTaskStatisticsData .badge-warning').html(sqli_vulnerableCount);
					$('#generalStatistics_sqliTaskStatisticsData .badge-important').html(sqli_exploitableCount);

					$('#generalStatistics_xssTaskStatisticsData .badge-info').html(xss_totalCount);
					$('#generalStatistics_xssTaskStatisticsData .badge-success').html(xss_successCount);
					$('#generalStatistics_xssTaskStatisticsData .badge-warning').html(xss_vulnerableCount);
					$('#generalStatistics_xssTaskStatisticsData .badge-important').html(xss_exploitableCount);


					$('#sqliStatistics_totalStatisticsData .badge-info').html(sqli_totalCount);
					$('#sqliStatistics_totalStatisticsData .badge-success').html(sqli_successCount);
					$('#sqliStatistics_totalStatisticsData .badge-warning').html(sqli_vulnerableCount);
					$('#sqliStatistics_totalStatisticsData .badge-important').html(sqli_exploitableCount);					

					$('#xssStatistics_totalStatisticsData .badge-info').html(xss_totalCount);
					$('#xssStatistics_totalStatisticsData .badge-success').html(xss_successCount);
					$('#xssStatistics_totalStatisticsData .badge-warning').html(xss_vulnerableCount);
					$('#xssStatistics_totalStatisticsData .badge-important').html(xss_exploitableCount);	

					$('#sqliStatistics_specialTable tbody').empty();

					var sqliTopicTR = $('<tr></tr>');
					sqliTopicTR.append('<td></td>');
					sqliTopicTR.append('<td style="text-align: center;"><small>Task Count</small></td>');
					sqliTopicTR.append('<td style="text-align: center;"><small>Not vulnerable</small></td>');
					sqliTopicTR.append('<td style="text-align: center;"><small>Vulnerable</small></td>');
					sqliTopicTR.append('<td style="text-align: center;"><small>Exploitable</small></td>');

					var xssTopicTR = $('<tr></tr>');
					xssTopicTR.append('<td></td>');
					xssTopicTR.append('<td style="text-align: center;"><small>Task Count</small></td>');
					xssTopicTR.append('<td style="text-align: center;"><small>Not vulnerable</small></td>');
					xssTopicTR.append('<td style="text-align: center;"><small>Vulnerable</small></td>');
					xssTopicTR.append('<td style="text-align: center;"><small>Exploitable</small></td>');

					var sqliStandartValuesTR = $('<tr id="sqliStatistics_totalStatisticsData"></tr>');									
					sqliStandartValuesTR.append('<td><i class="icon-double-angle-right"></i> SQL Injection Statistics</td>');
					sqliStandartValuesTR.append('<td style="text-align: center;"><span class="badge badge-info">' + sqli_totalCount + '</span></td>');
					sqliStandartValuesTR.append('<td style="text-align: center;"><span class="badge badge-success">' + sqli_successCount + '</span></td>');
					sqliStandartValuesTR.append('<td style="text-align: center;"><span class="badge badge-warning">' + sqli_vulnerableCount + '</span></td>');
					sqliStandartValuesTR.append('<td style="text-align: center;"><span class="badge badge-important">' + sqli_exploitableCount + '</span></td>');

					$('#sqliStatistics_specialTable tbody').append(sqliTopicTR);
					$('#sqliStatistics_specialTable tbody').append(sqliStandartValuesTR);


					for(var i = 0; i < sqli_techniqueStats.length; ++i) {
						var tr = $('<tr></tr>');
						var techniqueNameTD = $('<td></td>');
						var techniqueValueTD = $('<td style="text-align: center;"></td>');
						var emptyTDrow = $('<td></td><td></td><td></td>');

						techniqueNameTD.append('<i class="icon-cogs"></i> ' + sqli_techniqueStats[i]['techniqueName']);
						techniqueValueTD.append('<span class="badge badge-inverse">' + sqli_techniqueStats[i]['techniqueCount']  + '</span>');

						tr.append(techniqueNameTD).append(techniqueValueTD).append(emptyTDrow);
						$('#sqliStatistics_specialTable tbody').append(tr);
					}

					$('#xssStatistics_specialTable tbody').empty();


					var xssStandartValuesTR = $('<tr id="xssStatistics_totalStatisticsData"></tr>');								
					xssStandartValuesTR.append('<td><i class="icon-double-angle-right"></i> XSS Statistics</td>');
					xssStandartValuesTR.append('<td style="text-align: center;"><span class="badge badge-info">' + xss_totalCount + '</span></td>');
					xssStandartValuesTR.append('<td style="text-align: center;"><span class="badge badge-success">' + xss_successCount + '</span></td>');
					xssStandartValuesTR.append('<td style="text-align: center;"><span class="badge badge-warning">' + xss_vulnerableCount + '</span></td>');
					xssStandartValuesTR.append('<td style="text-align: center;"><span class="badge badge-important">' + xss_exploitableCount + '</span></td>');

					$('#xssStatistics_specialTable tbody').append(xssTopicTR);
					$('#xssStatistics_specialTable tbody').append(xssStandartValuesTR);				

					for(var i = 0; i < xss_techniqueStats.length; ++i) {
						var tr = $('<tr></tr>');
						var techniqueNameTD = $('<td></td>');
						var techniqueValueTD = $('<td style="text-align: center;"></td>');
						var emptyTDrow = $('<td></td><td></td><td></td>');

						techniqueNameTD.append('<i class="icon-cogs"></i> ' + xss_techniqueStats[i]['techniqueName']);
						techniqueValueTD.append('<span class="badge badge-inverse">' + xss_techniqueStats[i]['techniqueCount']  + '</span>');

						tr.append(techniqueNameTD).append(techniqueValueTD).append(emptyTDrow);
						$('#xssStatistics_specialTable tbody').append(tr);
					}

					
				   	var generalStatistics_complexChart = $.jqplot('generalStatistics_complexChart', [
				        [[generalStats_exploitableCount,'Complex']], 
				        [[generalStats_vulnerableCount,'Complex']],
				        [[generalStats_successCount,'Complex']],
				        [[generalStats_totalCount,'Complex']] ], 

				        {
				        	seriesColors:['#d11b1b', '#ffe932', '#6ad52a', '#1753ff'],
				        	seriesDefaults: {
					            renderer:$.jqplot.BarRenderer,
					            pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
					            shadowAngle: 135,
					            rendererOptions: { barDirection: 'horizontal' }
				        	},
				        	axes: { yaxis: { renderer: $.jqplot.CategoryAxisRenderer } }
				    	}
				    );	

				   	var generalStatistics_specialChart = $.jqplot('generalStatistics_specialChart', [
				        [[xss_exploitableCount,'XSS'], [sqli_exploitableCount,'SQL Injection']], 
				        [[xss_vulnerableCount,'XSS'], [sqli_vulnerableCount,'SQL Injection']], 
				        [[xss_successCount,'XSS'], [sqli_successCount,'SQL Injection']],
				        [[xss_totalCount,'XSS'], [sqli_totalCount,'SQL Injection']] ],

				        {
				        	seriesColors:['#d11b1b', '#ffe932', '#6ad52a', '#1753ff'],
				        	seriesDefaults: {
					            renderer:$.jqplot.BarRenderer,
					            pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
					            shadowAngle: 135,
					            rendererOptions: { barDirection: 'horizontal' }
				        	},
				        	axes: { yaxis: { renderer: $.jqplot.CategoryAxisRenderer } }
				   		}
				   	);
				}
			});
	

		});		
	

		$('#statistics_Tab a[href="#statistics_SQLInjectionStatistics"]').on('shown', function (e) {
			e.preventDefault();
			$(this).tab('show');

			$.ajax({
				url: 'bootstrap.php',
				type: 'POST',
				data: { 'insomniaAction' : 'statistics_getSQLInjectionStatistics' },
				success: function(statisticsDataCollection) {
					var statisticsData = JSON.parse(statisticsDataCollection);

					var sqli_totalCount       = statisticsData['SQLInjectionStats']['totalCount'];
					var sqli_successCount     = statisticsData['SQLInjectionStats']['successCount'];
					var sqli_vulnerableCount  = statisticsData['SQLInjectionStats']['vulnerableCount'];
					var sqli_exploitableCount = statisticsData['SQLInjectionStats']['exploitableCount'];

					var sqli_techniqueStats = statisticsData['techniqueStats_SQLInjection'];
					var sqli_techniqueStatsCount = sqli_techniqueStats.length;

					var techniqueStatsCollection = [];
					for(var i = 0; i < sqli_techniqueStats.length; ++i) {
						techniqueStatsCollection.push( [ [sqli_techniqueStats[i]['techniqueCount'], sqli_techniqueStats[i]['techniqueName']] ] );
					}


					var sqliStatistics_specialChart = $.jqplot('sqliStatistics_specialChart', 
						techniqueStatsCollection, 

						{
							seriesColors:['#282828'],
							seriesDefaults: {
								renderer:$.jqplot.BarRenderer,
						    	pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
						    	shadowAngle: 135,
						    	rendererOptions: { barDirection: 'horizontal' }
					    	},
					    	axes: { yaxis: { renderer: $.jqplot.CategoryAxisRenderer } }
						}
					);


					var sqliStatistics_overallChart = $.jqplot('sqliStatistics_overallChart', [
						[[sqli_exploitableCount,'SQL Injection']], 
						[[sqli_vulnerableCount,'SQL Injection']],
						[[sqli_successCount,'SQL Injection']],
						[[sqli_totalCount,'SQL Injection']] ], 

						{
							seriesColors:['#d11b1b', '#ffe932', '#6ad52a', '#1753ff'],
							seriesDefaults: {
					  			renderer:$.jqplot.BarRenderer,
								pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
					        	shadowAngle: 135,
					        	rendererOptions: { barDirection: 'horizontal' } 
					   		},
					    	axes: { yaxis: { renderer: $.jqplot.CategoryAxisRenderer } }
						}
					);
				}
			});	
		});



		$('#statistics_Tab a[href="#statistics_XSSTatistics"]').on('shown', function(e) {
			
			e.preventDefault();
			$(this).tab('show');

			$.ajax({
				url: 'bootstrap.php',
				type: 'POST',
				data: { 'insomniaAction' : 'statistics_getXSSStatistics' },
				success: function(statisticsDataCollection) {
					var statisticsData        = JSON.parse(statisticsDataCollection);

					var xss_totalCount       = statisticsData['XSSStats']['totalCount'];
					var xss_successCount     = statisticsData['XSSStats']['successCount'];
					var xss_vulnerableCount  = statisticsData['XSSStats']['vulnerableCount'];
					var xss_exploitableCount = statisticsData['XSSStats']['exploitableCount'];
					var xss_techniqueStats  = statisticsData['techniqueStats_XSS'];

					var xss_techniqueStatsCount = xss_techniqueStats.length;

					var techniqueStatsCollection = [];
					for(var i = 0; i < xss_techniqueStats.length; ++i) {
						techniqueStatsCollection.push( [ [xss_techniqueStats[i]['techniqueCount'], xss_techniqueStats[i]['techniqueName']] ] );
					}

					
					var xssStatistics_specialChart = $.jqplot('xssStatistics_specialChart', 
						techniqueStatsCollection, 

					    {
					       	seriesColors:['#282828'],
					        seriesDefaults: {
					            renderer:$.jqplot.BarRenderer,
					            pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
					            shadowAngle: 135,
					            rendererOptions: { barDirection: 'horizontal' }
					        },
					        axes: { yaxis: { renderer: $.jqplot.CategoryAxisRenderer } }
					    });

					
					var xssStatistics_overallChart = $.jqplot('xssStatistics_overallChart', [
						[[xss_exploitableCount,'XSS']], 
						[[xss_vulnerableCount,'XSS']],
						[[xss_successCount,'XSS']],
						[[xss_totalCount,'XSS']] ], 

						{
							seriesColors:['#d11b1b', '#ffe932', '#6ad52a', '#1753ff'],
					        seriesDefaults: {
					            renderer:$.jqplot.BarRenderer,
					            pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
					            shadowAngle: 135,
					            rendererOptions: { barDirection: 'horizontal' }
					        },
					        axes: { yaxis: { renderer: $.jqplot.CategoryAxisRenderer } }
					    }
					);
				}
			});	
				
		});

		$.ajax({
			url: 'bootstrap.php',
			type: 'POST',
			data: { 'insomniaAction' : 'statistics_getGeneralStatistics' },
			success: function(statisticsDataCollection) {
				var statisticsData        = JSON.parse(statisticsDataCollection);
				var sqliTechStatsCount    = statisticsData['techniqueStats_SQLInjection'].length;
				var xssTechStatsCount     = statisticsData['techniqueStats_XSS'].length;

				var generalStats_totalCount       = statisticsData['GeneralStats']['totalCount'];
				var generalStats_successCount     = statisticsData['GeneralStats']['successCount'];
				var generalStats_vulnerableCount  = statisticsData['GeneralStats']['vulnerableCount'];
				var generalStats_exploitableCount = statisticsData['GeneralStats']['exploitableCount'];

				var sqli_totalCount       = statisticsData['SQLInjectionStats']['totalCount'];
				var sqli_successCount     = statisticsData['SQLInjectionStats']['successCount'];
				var sqli_vulnerableCount  = statisticsData['SQLInjectionStats']['vulnerableCount'];
				var sqli_exploitableCount = statisticsData['SQLInjectionStats']['exploitableCount'];

				var xss_totalCount       = statisticsData['XSSStats']['totalCount'];
				var xss_successCount     = statisticsData['XSSStats']['successCount'];
				var xss_vulnerableCount  = statisticsData['XSSStats']['vulnerableCount'];
				var xss_exploitableCount = statisticsData['XSSStats']['exploitableCount'];

				var sqli_techniqueStats = statisticsData['techniqueStats_SQLInjection'];
				var xss_techniqueStats  = statisticsData['techniqueStats_XSS'];

				$('#generalStatistics_complexllTaskStatisticsData .badge-info').html(generalStats_totalCount);
				$('#generalStatistics_complexllTaskStatisticsData .badge-success').html(generalStats_successCount);
				$('#generalStatistics_complexllTaskStatisticsData .badge-warning').html(generalStats_vulnerableCount);
				$('#generalStatistics_complexllTaskStatisticsData .badge-important').html(generalStats_exploitableCount);

				$('#generalStatistics_sqliTaskStatisticsData .badge-info').html(sqli_totalCount);
				$('#generalStatistics_sqliTaskStatisticsData .badge-success').html(sqli_successCount);
				$('#generalStatistics_sqliTaskStatisticsData .badge-warning').html(sqli_vulnerableCount);
				$('#generalStatistics_sqliTaskStatisticsData .badge-important').html(sqli_exploitableCount);

				$('#generalStatistics_xssTaskStatisticsData .badge-info').html(xss_totalCount);
				$('#generalStatistics_xssTaskStatisticsData .badge-success').html(xss_successCount);
				$('#generalStatistics_xssTaskStatisticsData .badge-warning').html(xss_vulnerableCount);
				$('#generalStatistics_xssTaskStatisticsData .badge-important').html(xss_exploitableCount);


				$('#sqliStatistics_totalStatisticsData .badge-info').html(sqli_totalCount);
				$('#sqliStatistics_totalStatisticsData .badge-success').html(sqli_successCount);
				$('#sqliStatistics_totalStatisticsData .badge-warning').html(sqli_vulnerableCount);
				$('#sqliStatistics_totalStatisticsData .badge-important').html(sqli_exploitableCount);					

				$('#xssStatistics_totalStatisticsData .badge-info').html(xss_totalCount);
				$('#xssStatistics_totalStatisticsData .badge-success').html(xss_successCount);
				$('#xssStatistics_totalStatisticsData .badge-warning').html(xss_vulnerableCount);
				$('#xssStatistics_totalStatisticsData .badge-important').html(xss_exploitableCount);	

				for(var i = 0; i < sqli_techniqueStats.length; ++i) {
					var tr = $('<tr></tr>');
					var techniqueNameTD = $('<td></td>');
					var techniqueValueTD = $('<td style="text-align: center;"></td>');
					var emptyTDrow = $('<td></td><td></td><td></td>');

					techniqueNameTD.append('<i class="icon-cogs"></i> ' + sqli_techniqueStats[i]['techniqueName']);
					techniqueValueTD.append('<span class="badge badge-inverse">' + sqli_techniqueStats[i]['techniqueCount']  + '</span>');

					tr.append(techniqueNameTD).append(techniqueValueTD).append(emptyTDrow);
					$('#sqliStatistics_specialTable tbody').append(tr);
				}

				for(var i = 0; i < xss_techniqueStats.length; ++i) {
					var tr = $('<tr></tr>');
					var techniqueNameTD = $('<td></td>');
					var techniqueValueTD = $('<td style="text-align: center;"></td>');
					var emptyTDrow = $('<td></td><td></td><td></td>');

					techniqueNameTD.append('<i class="icon-cogs"></i> ' + xss_techniqueStats[i]['techniqueName']);
					techniqueValueTD.append('<span class="badge badge-inverse">' + xss_techniqueStats[i]['techniqueCount']  + '</span>');

					tr.append(techniqueNameTD).append(techniqueValueTD).append(emptyTDrow);
					$('#xssStatistics_specialTable tbody').append(tr);
				}

				var generalStatistics_complexChart = $.jqplot('generalStatistics_complexChart', [
					[[generalStats_exploitableCount,'Complex']], 
					[[generalStats_vulnerableCount,'Complex']],
					[[generalStats_successCount,'Complex']],
					[[generalStats_totalCount,'Complex']] ], 

					{
						seriesColors:['#d11b1b', '#ffe932', '#6ad52a', '#1753ff'],
						seriesDefaults: {
							renderer:$.jqplot.BarRenderer,
					    	pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
					     	shadowAngle: 135,
					    	rendererOptions: { barDirection: 'horizontal' }
				    	},
				  		axes: { yaxis: { renderer: $.jqplot.CategoryAxisRenderer } }
				    }
				);	

				var generalStatistics_specialChart = $.jqplot('generalStatistics_specialChart', [
					[[xss_exploitableCount,'XSS'], [sqli_exploitableCount,'SQL Injection']], 
					[[xss_vulnerableCount,'XSS'], [sqli_vulnerableCount,'SQL Injection']], 
					[[xss_successCount,'XSS'], [sqli_successCount,'SQL Injection']],
					[[xss_totalCount,'XSS'], [sqli_totalCount,'SQL Injection']] ],

					{
				  		seriesColors:['#d11b1b', '#ffe932', '#6ad52a', '#1753ff'],
				        seriesDefaults: {
				            renderer:$.jqplot.BarRenderer,
				            pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
				            shadowAngle: 135,
				            rendererOptions: { barDirection: 'horizontal' }
				        },
				        axes: { yaxis: { renderer: $.jqplot.CategoryAxisRenderer } }
				   	}
				);
			}
		});	
	});
	</script>


	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script type="text/javascript" src="js/excanvas.min.js"></script>>		
    <![endif]-->
</head>
<body>

	<!-- NAVBAR -->
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<button type="button" class="btn btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
	          	</button>
	          	<a class="brand" href="#">insomnia</a>

	          	<div class="nav-collapse collapse">
	          		<ul class="nav">
	          			<li><a href="index.php"><i class="icon-signal"></i> Main</a><li>

	          			<!-- DROPDOWN MENU -->
	          			<li class="dropdown">
	          				<a href="project.php" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-sitemap"></i> Project <b class="caret"></b></a>
	          				<ul class="dropdown-menu">
	          					<li><a href="project.php"><i class="icon-file"></i> New Project</a></li>
	          				</ul>
	          			</li>
	          			<!-- END OF DROPDOWN MENU -->
	          			
	          			<li class="active"><a href="statistics.php"><i class="icon-align-left"></i> Statistics</a></li>
	          			<li><a href="report.php"><i class="icon-eye-close"></i> Report Viewer</a></li>
	          			<li><a href="howto.php"><i class="icon-user"></i> How To</a>
	          		</ul>
	          	</div>
	        </div>
	    </div>
	</div>
	<!-- END OF NAVBAR -->

	<!-- CONTENT -->
	<div class="container">

		<!-- TOPIC -->
		<div class="row">
			<div class="span12 custom-hero">
				<h3><i class="icon-align-left"></i> Statistics - Web Application Threats Statistics <img src="img/brand.png" class="pull-right"></h3>
			</div>
		</div>

		<!-- CONTENT -->
		<div class="row">

			<div class="span12" style="padding-top: 10px;">

						<div class="tabbable tabs-left">
			              	<ul id="statistics_Tab" class="nav nav-tabs">
			                	<li class="active">
			                		<a href="#statistics_GeneralStatistics" data-toggle="tab">
			                		<i class="icon-bar-chart"></i> General Statistics</a>
			                	</li>
			                	<li>
			                		<a href="#statistics_SQLInjectionStatistics" data-toggle="tab">
			                		<i class="icon-tasks"></i> SQL Injection Statistics</a>
			                	</li>
			                	<li>
			                		<a href="#statistics_XSSTatistics" data-toggle="tab">
			                		<i class="icon-tasks"></i> XSS Statistics</a>
			                	</li>
			              	</ul>
							<div id="statistics_TabContent" class="tab-content">
			                	<div class="tab-pane fade active in" id="statistics_GeneralStatistics">
			                  		<p>
										<blockquote>
											<p><i class="icon-bar-chart"></i>  General Statistics</p>
											<small>General statistics of all vulnerability tasks and threats types</small>
											<small><a href="#" id="statistics_сlearStatistics">[<i class="icon-retweet"></i> Clear Statistics]</a></small>
										</blockquote>
						
										
										<table class="table table-hover">
											<tbody>
												<tr>
													<td></td>
													<td style="text-align: center;"><small>Task Count</small></td>
													<td style="text-align: center;"><small>Not Vulnerable</small></td>
													<td style="text-align: center;"><small>Vulnerable</small></td>
													<td style="text-align: center;"><small>Exploitable</small></td>
												</tr>
												<tr id="generalStatistics_complexllTaskStatisticsData">
													<td><i class="icon-double-angle-right"></i> Complex Task statistics</td>
													<td style="text-align: center;"><span class="badge badge-info"></span></td>
													<td style="text-align: center;"><span class="badge badge-success"></span></td>
													<td style="text-align: center;"><span class="badge badge-warning"></span></td>
													<td style="text-align: center;"><span class="badge badge-important"></span></td>

												<tr id="generalStatistics_sqliTaskStatisticsData">
													<td><i class="icon-double-angle-right"></i> SQL Injection Task Statistics</td>
													<td style="text-align: center;"><span class="badge badge-info"></span></td>
													<td style="text-align: center;"><span class="badge badge-success"></span></td>
													<td style="text-align: center;"><span class="badge badge-warning"></span></td>
													<td style="text-align: center;"><span class="badge badge-important"></span></td>
												</tr>
												<tr id="generalStatistics_xssTaskStatisticsData">
													<td><i class="icon-double-angle-right"></i> XSS Task Statistics</td>
													<td style="text-align: center;"><span class="badge badge-info"></span></td>
													<td style="text-align: center;"><span class="badge badge-success"></span></td>
													<td style="text-align: center;"><span class="badge badge-warning"></span></td>
													<td style="text-align: center;"><span class="badge badge-important"></span></td>
												</tr>
											</tbody>
   										</table>

										<div class="accordion" id="generalStatisticsAccordion">
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#generalStatisticsAccordion" href="#generalStatistics_OverallStats">
														<i class="icon-indent-left"></i> Complex Statistics
													</a>
												</div>
												<div id="generalStatistics_OverallStats" class="accordion-body collapse in">
													<div class="accordion-inner">
														<div id="generalStatistics_complexChart" style="height:120px"></div>
													</div>
												</div>
											</div>
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#generalStatisticsAccordion" href="#generalStatistics_SpecialStats">
														<i class="icon-indent-left"></i>  Special Statistics
													</a>
												</div>
												<div id="generalStatistics_SpecialStats" class="accordion-body collapse">
													<div class="accordion-inner">
														<div id="generalStatistics_specialChart" style="height:190px"></div>
													</div>
												</div>
											</div>
										</div>

			                  		</p>

			                	</div>


			               		<div class="tab-pane fade" id="statistics_SQLInjectionStatistics">
			                  		<p>
			                  			<blockquote>
			                  				<p><i class="icon-tasks"></i> SQL Injection Statistics</p>
			                  				<small>SQL Injection task and threats statistics data</small>
			                  			</blockquote>

										<table id='sqliStatistics_specialTable' class="table table-hover">
											<tbody>
												<tr>
													<td></td>
													<td style="text-align: center;"><small>Task Count</small></td>
													<td style="text-align: center;"><small>Not Vulnerable</small></td>
													<td style="text-align: center;"><small>Vulnerable</small></td>
													<td style="text-align: center;"><small>Exploitable</small></td>
												</tr>												
												<tr id="sqliStatistics_totalStatisticsData">
													<td><i class="icon-double-angle-right"></i> SQL Injection Statistics</td>
													<td style="text-align: center;"><span class="badge badge-info"></span></td>
													<td style="text-align: center;"><span class="badge badge-success"></span></td>
													<td style="text-align: center;"><span class="badge badge-warning"></span></td>
													<td style="text-align: center;"><span class="badge badge-important"></span></td>
												</tr>											
											</tbody>
										</table>

										<div class="accordion" id="sqliStatisticsAccordion">
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#sqliStatisticsAccordion" href="#sqliStatistics_OverallStats">
														<i class="icon-indent-left"></i> SQL Injection Statistics
													</a>
												</div>
												<div id="sqliStatistics_OverallStats" class="accordion-body collapse in">
													<div class="accordion-inner">
														<div id="sqliStatistics_overallChart" style="height:120px"></div>
													</div>
												</div>
											</div>
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#sqliStatisticsAccordion" href="#sqliStatistics_TechniqueStats">
														<i class="icon-indent-left"></i>  Technique Statistics
													</a>
												</div>
												<div id="sqliStatistics_TechniqueStats" class="accordion-body collapse">
													<div class="accordion-inner">
														<div id="sqliStatistics_specialChart" style="height:220px"></div>
													</div>
												</div>
											</div>
										</div>
			                  		</p>
			                	</div>


			                	<div class="tab-pane fade" id="statistics_XSSTatistics">
			                  		<p>
			                  			<blockquote>
			                  				<p><i class="icon-tasks"></i> XSS Statistics</p>
			                  				<small>XSS task and threats statistics data</small>
			                  			</blockquote>

										<table id='xssStatistics_specialTable' class="table table-hover">
											<tbody>
												<tr>
													<td></td>
													<td style="text-align: center;"><small>Task Count</small></td>
													<td style="text-align: center;"><small>Not Vulnerable</small></td>
													<td style="text-align: center;"><small>Vulnerable</small></td>
													<td style="text-align: center;"><small>Exploitable</small></td>
												</tr>												
												<tr id="xssStatistics_totalStatisticsData">
													<td><i class="icon-double-angle-right"></i> XSS Statistics</td>
													<td style="text-align: center;"><span class="badge badge-info"></span></td>
													<td style="text-align: center;"><span class="badge badge-success"></span></td>
													<td style="text-align: center;"><span class="badge badge-warning"></span></td>
													<td style="text-align: center;"><span class="badge badge-important"></span></td>
												</tr>											
											</tbody>
										</table>

										<div class="accordion" id="xssStatisticsAccordion">
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#xssStatisticsAccordion" href="#xssStatistics_OverallStats">
														<i class="icon-indent-left"></i> XSS Statistics
													</a>
												</div>
												<div id="xssStatistics_OverallStats" class="accordion-body collapse in">
													<div class="accordion-inner">
														<div id="xssStatistics_overallChart" style="height:120px"></div>
													</div>
												</div>
											</div>
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#xssStatisticsAccordion" href="#xssStatistics_TechniqueStats">
														<i class="icon-indent-left"></i>  Technique Statistics
													</a>
												</div>
												<div id="xssStatistics_TechniqueStats" class="accordion-body collapse">
													<div class="accordion-inner">
														<div id="xssStatistics_specialChart" style="height:100px"></div>
													</div>
												</div>
											</div>
										</div>
			                  		</p>
			                	</div>
							</div>
						</div>
						
			</div>
		</div>
	</div>
</body>
</html>