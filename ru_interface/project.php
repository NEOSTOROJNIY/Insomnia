<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>insomnia - Project</title>

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/docs.css">
	<link rel="stylesheet" href="css/custom.css">
	
	<script type="text/javascript" src="js/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>

	<script type="text/javascript" src="js/projectpage_functions.js"></script>

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
	          			<li class="dropdown active">
	          				<a href="project.php" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-sitemap"></i> Проект тестирования <b class="caret"></b></a>
	          				<ul class="dropdown-menu">
	          					<li><a href="project.php"><i class="icon-file"></i> Новый проект</a></li>
	          					<li class="divider"></li>
	          					<li><a href="#" id="openProject"><i class="icon-folder-open"></i> Открыть проект</a></li>
	          					<li><a href="#" id="saveProject"><i class="icon-save"></i> Сохранить проект</a></li>
	          				</ul>
	          			</li>
	          			<!-- END OF DROPDOWN MENU -->
	          			
	          			<li><a href="statistics.php"><i class="icon-align-left"></i> База статистики</a></li>
	          			<li><a href="report.php"><i class="icon-eye-close"></i> Система отчетов</a></li>
	          			<li><a href="howto.php"><i class="icon-user"></i> Справка</a>
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
				<h3><i class="icon-sitemap"></i> Проект тестирования - Анализ прилоежний на уязвимости<img src="img/brand.png" class="pull-right"></h3>
			</div>
		</div>

		<div class="row">
			<div class="span1"></div>

			<div class="span10" id="infoArea">

				<div id="infoArea_targetProcessing">
					<p class="pager pull-left">Введите URL-адрес ресурса, который необходимо протестировать, выберите анализируемые уязвимости и нажмите на кнопку старта.</p>
					<p class="pager">
						<form id="targetDataForm">
						<table id="targetData" class="table table-hover table-striped">
							<tbody>
								<tr>
									<td><i class="icon-globe"></i> URL-адрес ресурса</td><td>SQL Injection</td><td>XSS</td>
								</tr>

								<tr>
									<td><div class="input-prepend">
										<span class="add-on"><i class="icon-cloud"></i></span>
										<input type="text" name="_res1" class="span6" placeholder="URL" value="http://localhost/testapps/pgsql_inject.php?id=1">
									</div></td>
									<td><input type="checkbox" name="_sqli1"></td>
									<td><input type="checkbox" name="_xss1"></td>
								</tr>

							</tbody>
						</table>
						</form>
						<button class="btn" id="addNewTarget" type="button"><i class="icon-random"></i> Добавить ресурс</button>
						<button class="btn btn-primary" id="startTesting" type="button"><i class="icon-tasks"></i> Начать тестирование</button>
					</p>
				</div>

			</div>

			<div class="span1"></div>
		</div>

	</div>



<!-- MODAL WINDOW 'SAVE PROJECT' -->
<div id="modal_saveProject" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_SaveProjectLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="modal_SaveProjectLabel"><i class="icon-save"></i> Сохранить проект</h3>
  </div>
  <div class="modal-body">
    <p> 
	    <div class="input-prepend">
		    <span class="add-on"><i class="icon-indent-right"></i></span>
		    <input type="text" id="modal_saveProject_fileNameInput" class="span5" placeholder="Type the filename here..."> 
	    </div>
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Закрить</button>
    <button id="modal_saveProject_saveButton" class="btn btn-primary">Сохранить проект</button>
  </div>
</div>
<!-- END OF MODAL WINDOW 'SAVE PROJECT' -->
	
	<!-- MODAL WINDOW 'SAVE PROJECT EMPTY FILE NAME ERROR' -->
	<div id="modal_saveProject_error_emptyFileName" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_SaveProject_EmptyFileNameLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="modal_SaveProject_EmptyFileNameLabel"><i class="icon-exclamation-sign"></i> Ошибка!</h3>
		</div>
		<div class="modal-body">
			<p>Пустое имя файла! Введите имя файла перед сохранением!</p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i> Закрыть</button>
		</div>
	</div>
	<!-- END OF MODAL WINDOW 'SAVE PROJECT EMPTY FILE NAME ERROR' -->

	<!-- MODAL WINDOW 'SAVE PROJECT EMPTY FILE NAME ERROR' -->
	<div id="modal_saveProject_success_fileSaved" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_SaveProject_FileSavedLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="modal_SaveProject_FileSavedLabel"><i class="icon-ok-sign"></i> Выполнено!</h3>
		</div>
		<div class="modal-body">
			<p>Проект успешно сохранен!</p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i> Закрить</button>
		</div>
	</div>
	<!-- END OF MODAL WINDOW 'SAVE PROJECT EMPTY FILE NAME ERROR' -->

	<!-- MODAL WINDOW 'SAVE PROJECT EMPTY FILE NAME ERROR' -->
	<div id="modal_saveProject_error_fileNotSaved" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_SaveProject_FileExistsLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="modal_SaveProject_FileExistsLabel"><i class="icon-exclamation-sign"></i> Ошибка!</h3>
		</div>
		<div class="modal-body">
			<p>Файл с таким именем уже существует!</p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i> Закрыть</button>
		</div>
	</div>
	<!-- END OF MODAL WINDOW 'SAVE PROJECT EMPTY FILE NAME ERROR' -->



<!-- MODAL WINDOW 'OPEN PROJECT' -->
<div id="modal_openProject" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_OpenProjectLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="modal_OpenProjectLabel"><i class="icon-folder-open"></i> Открыть проект</h3>
  </div>
  <div class="modal-body">
    <p id="modal_openProject_projectListBlock">
    	Проекты:
    </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>
    <button id="modal_openProject_openButton" class="btn btn-primary">Открыть проект</button>
  </div>
</div>
<!-- END OF MODAL WINDOW 'OPEN PROJECT' -->

	<!-- MODAL WINDOW 'OPEN PROJECT FILE IS NOT CHOSEN ERROR' -->
	<div id="modal_openProject_error_fileNotChosen" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal_openProject_FileNotChosenLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="modal_openProject_FileNotChosenLabel"><i class="icon-exclamation-sign"></i> Ошибка!</h3>
		</div>
		<div class="modal-body">
			<p>Вы не выбрали файл проекта!</p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i> Закрыть</button>
		</div>
	</div>
	<!-- END OF MODAL WINDOW 'OPEN PROJECT FILE IS NOT CHOSEN ERROR' -->


</body>
</html>