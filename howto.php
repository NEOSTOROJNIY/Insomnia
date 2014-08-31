<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>insomnia - How To</title>
	

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/docs.css">
	<link rel="stylesheet" href="css/custom.css">
	
	<script type="text/javascript" src="js/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>

	<script type="text/javascript" src="js/howtopage_functions.js"></script>

	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
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
	          			
	          			<li><a href="statistics.php"><i class="icon-align-left"></i> Statistics</a></li>
	          			<li><a href="report.php"><i class="icon-eye-close"></i> Report Viewer</a></li>
	          			<li class="active"><a href="howto.php"><i class="icon-user"></i> How To</a>
	          		</ul>
	          	</div>
	        </div>
	    </div>
	</div>
	<!-- END OF NAVBAR -->

	<!-- CONTENT -->
	<div class="container">

		<div class="row">
			<div class="span12 custom-hero">
				<h3><i class="icon-user"></i> How To - Pentest Manual <img src="img/brand.png" class="pull-right"></h3>
			</div>
		</div>

		<div class="row">
			<!-- MANUAL MENU -->
			<div class="span3">

				<p><div id="manualdata_menu" class="well" style="max-width: 340px; padding: 8px 0;">

              			<ul class="nav nav-list">
                			<li class="nav-header"><i class="icon-user"></i>User Manual</li>
                			<li id="mu_testing" class="active"><a href="#"><i class="icon-random"></i>Testing</a></li>
                			<li id="mu_reporting"><a href="#"><i class="icon-file-alt"></i>Reporting</a></li>
                			<li id="mu_statistics"><a href="#"><i class="icon-file-alt"></i>Statistics</a></li>

                			<li class="divider"></li>

                			<li class="nav-header"><i class="icon-cog"></i>Technical Manual</li>
                			<li id="mt_xmlstructure"><a href="#"><i class="icon-sort"></i>XML Report Structure</a></li>

                			<li class="divider"></li>

                			<li class="nav-header"><i class="icon-comments-alt"></i>Technique Specifications</li>
                			<li id="ms_sqlinjection"><a href="#"><i class="icon-share-alt"></i>SQL Injection Techniques</a></li>
                			<li id="ms_xss"><a href="#" class="ms_xss"><i class="icon-share-alt"></i>XSS Techniques</a></li>
              			</ul>

            	</div></p>

			</div>
			<!-- END OF MANUAL MENU -->

			<!-- MANUAL CONTENT -->
			<div class="span9">

				<p style="padding-top: 10px;"><div id="manualdata_content">

					<div id="manual_user_testing">
						<blockquote>
							<p><i class="icon-signal"></i> Testing</p>
							<small>Main menu - Project</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> Configure the testing task</a></li>
								<li><a href="#"><i class="icon-signout"></i> Save Configuration</a></li>
								<li><a href="#"><i class="icon-signout"></i> Open Configuration</a></li>
							</ul>
						<hr>

						<h4><i class="icon-cogs"></i> Configure the testing task</h4>

						Configuration is carried out in three stages:<br>
						<ol>
							<li>Adding an URL of possibly vulnerable resource;</li>
							<li>Selection of vulnerabilities for the test;</li>
							<li>Adding additional URLs and starting the test by pushing on "Start Testing" button.</li>
						</ol>

						<hr>

						<h4><i class="icon-save"></i> Save Configuration</h4>

						Configuration saving is carried out in three stages:<br>
						<ol>
							<li>Click on the "Project" button of nav-bar menu;</li>
							<li>Click on the "Save Project" context menu line;</li>
							<li>In new window type the filename and click on the "Save" button.</li>
						</ol>

						<hr>

						<h4><i class="icon-folder-open"></i> Open Configuration</h4>

						Configuration opening is carried out in the three stages:<br>
						<ol>
							<li>Click on the "Project" button of nav-bar menu;</li>
							<li>Click on the "Open Project" context menu line;</li>
							<li>In new window select your project file and click on the "Open" button.</li>
						</ol>

					</div>

					<div id="manual_user_reporting" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> Reporting</p>
							<small>Main menu - Report Viewer</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> View the certain report</a></li>
								<li><a href="#"><i class="icon-signout"></i> Download the certain report</a></li>
								<li><a href="#"><i class="icon-signout"></i> Report items</a></li>
							</ul>
						<hr>

						<h4><i class="icon-eye-open"></i> View the certain report</h4>

						View of the certain report is carried out in the two stages:

						<ol>
							<li>Open report: click on the current report in the left report list on page;</li>
							<li>Click on the report tabs for content viewing: resource data, security data, scan details.</li>
						</ol>

						<hr>

						<h4><i class="icon-download-alt"></i> Download the certain report</h4>
						For download the xml report file from report page you should click on the download button (<i class="icon-download-alt"></i>) that placed nearby the report name in the report list.</li>
						<hr>

						<h4><i class="icon-cog"></i> Report items</h4>

						<dl class="dl-horizontal">
							<dt><i class="icon-cogs"></i> Resource</dt>
							<dd>Contains the resource data: resource URL, resource IP, Server Banner.</dd>

							<dt><i class="icon-lock"></i> Security</dt>
							<dd>Contains the security statistics: vulnerabilities and their security level.</dd>

							<dt><i class="icon-qrcode"></i> Scan Details</dt>
							<dd>Contains the data about scan: vulnerable params, vulnerable values, list of worked techniques, type of vulnerability (XSS), type of database (SQLInjection).</dd>
						</dl>

					</div>

					<div id="manual_user_statistics" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> Statistics</p>
							<small>Main menu - Statistics</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> General Statistics</a></li>
								<li><a href="#"><i class="icon-signout"></i> SQL Injection Statistics</a></li>
								<li><a href="#"><i class="icon-signout"></i> XSS Statistics</a></li>
							</ul>
						<hr>

						<h4><i class="icon-bar-chart"></i> General Statistics</h4>

						General statistics shows:

						<ol>
							<li> Count of tests;</li>
							<li> General count of resources with green, yellow and red security level;</li>
							<li> SQL Injection count of resources with green security level;</li>
							<li> XSS count of resources with green security level;</li>
							<li> Comparative charts of complex data and SQL Injection / XSS testing data.</li>
						</ol>

						<hr>

						<h4><i class="icon-tasks"></i> SQL Injection Statistics</h4>

						SQL Injection statistics shows:

						<ol>
							<li> Count of resources with green, yellow and red security level;</li>
							<li> Count of worked techniques that has detecdet vulnerable params and scenarios;</li>
							<li> Comparative counter charts of realized techniques.</li>
						</ol>

						<hr>

						<h4><i class="icon-tasks"></i> XSS Statistics</h4>

						XSS Statistics statistics shows:

						<ol>
							<li> Count of resources with green, yellow and red security level;</li>
							<li> Count of worked techniques that has detecdet vulnerable params and scenarios;</li>
							<li> Comparative counter charts of realized techniques.</li>
						</ol>

					</div>
					


					<div id="manual_technical_xmlstructure" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> XML Report Structure</p>
							<small>XML Report File Specification</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> XML File Structure</a></li>
								<li><a href="#"><i class="icon-signout"></i> XML File Parts</a></li>
							</ul>
						<hr>

						<h4><i class="icon-tasks"></i> XML File Structure</h4>

						The XML Report file contains data about results of the vulnerability test. This data cointains next information:

						<ol>
							<li>Security level of tested resource;</li>
							<li>System data of tested resource: URL address, IP address, Server Banner;</li>
							<li>Security level about all tested vulnerabilites: XSS security level, SQL Injection security level;</li>
							<li>Data about worked techniques and their results: vulnerable params, vulnerable values, possible hack scenarios, list of worked techniques, type of vulnerability (XSS), type of database (SQL Injection).</li>
						</ol>

						<hr>

						<h4><i class="icon-tasks"></i> XML File Parts</h4>

						<hr>

						<dl class="dl-horizontal">
							<dt><i class="icon-cog"></i> date</dt>
							<dd>Date of report creation.</dd>

							<dt><i class="icon-cog"></i> resource</dt>
							<dd>Technical data about the tested resource.</dd>

							<dt><i class="icon-cog"></i> securitylevel</dt>
							<dd>Security leve of resource (1 - greed, 2 - yellow, 3 - green).</dd>

							<dt><i class="icon-cog"></i> testedvulns</dt>
							<dd>Contains blocks of vulnerability information, which store the data of the performed types of scans (SQLInjection / XSS) and the corresponding results of the analysis (security_level attribute).</dd>

							<dt><i class="icon-cog"></i> scandetails</dt>
							<dd>Details of the analysis (results of testing for XSS/SQL Injection vulnerability).</dd>
						</dl>

					</div>

					<div id="manual_specification_sqlinjection" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> SQL Injection Techniques</p>
							<small>SQL Injection Techniques Specifications</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> Magic Quotes (Check)</a></li>
								<li><a href="#"><i class="icon-signout"></i> Boolean Operations (Check)</a></li>
								<li><a href="#"><i class="icon-signout"></i> Grouping Operations (Check)</a></li>
								<li><a href="#"><i class="icon-signout"></i> Numerical Operations (Check)</a></li>								
								<li><a href="#"><i class="icon-signout"></i> Column Count (Inject)</a></li>
								<li><a href="#"><i class="icon-signout"></i> Base Info (Inject)</a></li>
								<li><a href="#"><i class="icon-signout"></i> System DB Check (Inject)</a></li>
							</ul>
						<hr>

						<h5><i class="icon-bug"></i> Magic Quotes (Check)</h5>
						Identification of vulnerabilities by concatenating the request parameter with the final comment sequence.

						<hr>

						<h5><i class="icon-bug"></i> Boolean Operations (Check)</h5>
						Identification of vulnerabilities by inserting Boolean operations

						<hr>

						<h5><i class="icon-bug"></i> Grouping Operations (Check)</h5>
						Identification of vulnerabilities by implementing grouping operators.

						<hr>

						<h5><i class="icon-bug"></i> Numerical Operations (Check)</h5>
						Defining vulnerability by manipulating the numerical values ​​of request parameters.

						<hr>

						<h5><i class="icon-bug"></i> Column Count (Inject)</h5>
						Determine the possible number of columns in the table of vulnerable query.

						<hr>

						<h5><i class="icon-bug"></i> Base Info (Inject)</h5>
						Determine the version of database, the database name and the user database.

						<hr>

						<h5><i class="icon-bug"></i> System DB Check (Inject)</h5>
						Determine the possible access to the system tables.

					</div>

					<div id="manual_specification_xss" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> XSS Techniques</p>
							<small>XSS Techniques Specifications</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> Unique Vector (Check)</a></li>
								<li><a href="#"><i class="icon-signout"></i> Numeric Code (Inject)</a></li>
								<li><a href="#"><i class="icon-signout"></i> Remote Code (Inject)</a></li>
								<li><a href="#"><i class="icon-signout"></i> Data Protocol (Inject)</a></li>
								<li><a href="#"><i class="icon-signout"></i> Hands-Free (Inject)</a></li>
							</ul>						
						<hr>

						<h5><i class="icon-bug"></i> Unique Vector (Check)</h5>
						Determine of vulnerabilities by implementing universal string that contains the language constructs that implements the functionality of the client application.

						<hr>

						<h5><i class="icon-bug"></i> Numeric Code (Inject)</h5>
						The possibility of introducing the code that converted into a numeric sequence form.

						<hr>

						<h5><i class="icon-bug"></i> Remote Code (Inject)</h5>
						The ability of calling scripts from remote sources.

						<hr>

						<h5><i class="icon-bug"></i> Data Protocol (Inject)</h5>
						The possibility of introducing a code with the Data protocol.

						<hr>

						<h5><i class="icon-bug"></i> Hands-Free (Inject)</h5>
						The possibility of free code injection.

					</div>

				</div></p>

			</div>
			<!-- END OF MANUAL CONTENT -->

		</div>

	</div>
	<!-- END OF CONTENT -->
</body>
</html>