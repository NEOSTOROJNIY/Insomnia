<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>Blizzard Games - Игры от компании близзард Blizzard</title>
	<script type="text/javascript" src="js/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</head>
<body>

	<div class="container">
		<div class="row">
			<div class="span12 text-center pager"><h1>Blizzard Games - Игры от компании Blizzard</h1></div>
		</div class="row">

		<div class="row">
			<div class="span12 text-center">
				<?php if(isset($_GET['username'])) { echo "<hr><h5>Компания Blizzard приветствует посетителя<br><b><font color=\"red\">{$_GET['username']}</font></b>!</h5><hr>"; } ?>
			</div>
		</div>

		<div class="row">
			<div class="span4 text-center"><a href="vulnerable_app.php?id=1"><img src="img/d3_min.png" class="img-polaroid"></img></a></div>

			<div class="span4 text-center"><a href="vulnerable_app.php?id=2"><img src="img/sc2_min.png" class="img-polaroid"></img></a></div>

			<div class="span4 text-center"><a href="vulnerable_app.php?id=3"><img src="img/wow_min.png" class="img-polaroid"></img></a></div>
		</div>

		<div class="row">
			<div class="span1"></div>

			<div class="span10">

			<?php

			if( !isset($_GET['id']) )
				echo "<div class=\"pager\">Кликните по изображению игры для просмотра информации о ней.</div>";
			else {

				$dbhost     = "localhost";
				$dbuser     = "root";
				$dbpassword = "terentek1";
				$dbname     = "blizzardgames";


				$dbhandle = mysql_connect($dbhost, $dbuser, $dbpassword);

				if(!$dbhandle)
					echo "Ошибка соединения с базой данных: " . mysql_error();
				else {
					mysql_select_db($dbname);

					$res = mysql_query("SELECT name,description FROM games WHERE id={$_GET['id']}");
					if($res) {
						while($row = mysql_fetch_assoc($res))
							echo "<h4 class=\"text-center\">{$row['name']}</h4>{$row['description']}<br><br>";
					} else
						echo mysql_error();
				}
			}
			?>

			</div>
			<div class="span1"></div>

		</div>

		<div class="row">
			<div class="span4"></div>

			<div class="span4 text-center">

			<hr>
			<h5>Маленькое приветствие от Blizzard</h5>
			<hr>

			<form action="<?=$_SERVER['SCRIPT_NAME'];?>" method="GET">
				<textarea name="username" style="width:350px; height:80px;" placeholder="Ваше имя..."></textarea>
				<input class="btn" type="submit" value="Поприветствовать меня!">
			</form>

			</div>

			<div class="span4"></div>
		</div>

	</div>

</body>
</html>