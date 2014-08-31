<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>insomnia - Report</title>

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/docs.css">
	<link rel="stylesheet" href="css/custom.css">
	
	<script type="text/javascript" src="js/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>

	<script type="text/javascript" src="js/reportpage_functions.js"></script>

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
	          			<li class="active"><a href="report.php"><i class="icon-eye-close"></i> Report Viewer</a></li>
	          			<li><a href="howto.php"><i class="icon-user"></i> How To</a>
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
				<h3><i class="icon-eye-close"></i> Report Viewer <img src="img/brand.png" class="pull-right"></h3>
			</div>
		</div>

		<div class="row" style="padding-top: 10px;">
			
			<div class="span3">

				<div class="well well-small" name="reportList" style="max-width: 280px; padding: 8px 0;">
					<ul id="reportListBlock" class="nav nav-list">
						<li class="nav-header"><i class="icon-file-alt"></i>Reports</li>
						<li class="divider"></li>
					</ul>
				</div>

			</div>

			<div id="reportContent" class="span9" style="padding-top: 10px;"></div>

		</div>

    </div>
    <!-- END OF CONTENT -->

	<!-- MODAL. CONTAINS ERROR DATA. -->
	<!-- AJAX DOWNLOADING ERROR - GET REPORT LIST ERROR -->
	<div id="downloadingError_reportList" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel"><i class="icon-exclamation-sign"></i> Downloading Error!</h3>
		</div>
		<div class="modal-body">
			<p>Cant get the report list!</p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i> Close</button>
		</div>
	</div>

	<!-- AJAX DOWNLOADING ERROR - GET REPORT XML FILE ERROR -->
	<div id="downloadingError_reportFile" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel"><i class="icon-exclamation-sign"></i> Downloading Error!</h3>
		</div>
		<div class="modal-body">
			<p>Cant download the report XML file!</p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i> Close</button>
		</div>
	</div>
	<!-- END OF MODALS -->
</body>
</html>