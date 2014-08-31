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
	          			<li><a href="index.php"><i class="icon-signal"></i> Главная страница</a><li>

	          			<!-- DROPDOWN MENU -->
	          			<li class="dropdown">
	          				<a href="project.php" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-sitemap"></i> Проекты <b class="caret"></b></a>
	          				<ul class="dropdown-menu">
	          					<li><a href="project.php"><i class="icon-file"></i> Новый проект</a></li>
	          				</ul>
	          			</li>
	          			<!-- END OF DROPDOWN MENU -->
	          			
	          			<li><a href="statistics.php"><i class="icon-align-left"></i> База статистики</a></li>
	          			<li><a href="report.php"><i class="icon-eye-close"></i> Система отчетов</a></li>
	          			<li class="active"><a href="howto.php"><i class="icon-user"></i> Справка</a>
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
				<h3><i class="icon-user"></i> Справка - Инструкция по использованию, технические данные <img src="img/brand.png" class="pull-right"></h3>
			</div>
		</div>

		<div class="row">
			<!-- MANUAL MENU -->
			<div class="span3">

				<p><div id="manualdata_menu" class="well" style="max-width: 340px; padding: 8px 0;">

              			<ul class="nav nav-list">
                			<li class="nav-header"><i class="icon-user"></i>Инструкция по использованию</li>
                			<li id="mu_testing" class="active"><a href="#"><i class="icon-random"></i>Тестирование</a></li>
                			<li id="mu_reporting"><a href="#"><i class="icon-file-alt"></i>Отчеты</a></li>
                			<li id="mu_statistics"><a href="#"><i class="icon-file-alt"></i>Статистика</a></li>

                			<li class="divider"></li>

                			<li class="nav-header"><i class="icon-cog"></i>Техническая информация</li>
                			<li id="mt_xmlstructure"><a href="#"><i class="icon-sort"></i>Структура XML-отчета</a></li>

                			<li class="divider"></li>

                			<li class="nav-header"><i class="icon-comments-alt"></i>Спецификации техник анализа</li>
                			<li id="ms_sqlinjection"><a href="#"><i class="icon-share-alt"></i>SQL Injection-техники</a></li>
                			<li id="ms_xss"><a href="#" class="ms_xss"><i class="icon-share-alt"></i>XSS-техники</a></li>
              			</ul>

            	</div></p>

			</div>
			<!-- END OF MANUAL MENU -->

			<!-- MANUAL CONTENT -->
			<div class="span9">

				<p style="padding-top: 10px;"><div id="manualdata_content">

					<div id="manual_user_testing">
						<blockquote>
							<p><i class="icon-signal"></i> Тестирование</p>
							<small>Главное меню - Проекты</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> Настройка задачи тестирования</a></li>
								<li><a href="#"><i class="icon-signout"></i> Запуск тестирования</a></li>
								<li><a href="#"><i class="icon-signout"></i> Сохранение настроенного проекта</a></li>
								<li><a href="#"><i class="icon-signout"></i> Загрузка настроенного проекта</a></li>
							</ul>
						<hr>

					</div>

					<div id="manual_user_reporting" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> Отчеты</p>
							<small>Главное меню - Система отчетов</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> Просмотр определенного отчета</a></li>
								<li><a href="#"><i class="icon-signout"></i> Загрузка определенного проекта</a></li>
								<li><a href="#"><i class="icon-signout"></i> Информационные блоки отчета</a></li>
							</ul>
						<hr>

					</div>

					<div id="manual_user_statistics" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> Статистика</p>
							<small>Главное меню - База статистики</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> Общая статистика</a></li>
								<li><a href="#"><i class="icon-signout"></i> Статистика по SQL Injection</a></li>
								<li><a href="#"><i class="icon-signout"></i> Статистика по XSS</a></li>
							</ul>
						<hr>

					</div>
					


					<div id="manual_technical_xmlstructure" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> Структура XML-файла отчета</p>
							<small>Спецификации XML-файла отчета</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> Структура XML-файла</a></li>
								<li><a href="#"><i class="icon-signout"></i> Сущности XML-файла</a></li>
							</ul>
						<hr>

					</div>

					<div id="manual_specification_sqlinjection" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> SQL Injection Techniques</p>
							<small>SQL Injection Techniques Specifications</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> Magic Quotes</a></li>
								<li><a href="#"><i class="icon-signout"></i> Boolean Operations</a></li>
								<li><a href="#"><i class="icon-signout"></i> Grouping Operations</a></li>
								<li><a href="#"><i class="icon-signout"></i> Numerical Operations</a></li>								
								<li><a href="#"><i class="icon-signout"></i> Column Count</a></li>
								<li><a href="#"><i class="icon-signout"></i> Base Info</a></li>
								<li><a href="#"><i class="icon-signout"></i> System DB Check</a></li>
							</ul>
						<hr>

					</div>

					<div id="manual_specification_xss" class="hide">
						<blockquote>
							<p><i class="icon-signal"></i> XSS Techniques</p>
							<small>XSS Techniques Specifications</small>
						</blockquote>

						<hr>
							<ul class="unstyled">
								<li><a href="#"><i class="icon-signout"></i> Unique Vector</a></li>
								<li><a href="#"><i class="icon-signout"></i> Numeric Code</a></li>
								<li><a href="#"><i class="icon-signout"></i> Remote Code</a></li>
								<li><a href="#"><i class="icon-signout"></i> Data Protocol</a></li>
								<li><a href="#"><i class="icon-signout"></i> Hands-Free</a></li>
							</ul>						
						<hr>

					</div>

				</div></p>

			</div>
			<!-- END OF MANUAL CONTENT -->

		</div>

	</div>
	<!-- END OF CONTENT -->
</body>
</html>