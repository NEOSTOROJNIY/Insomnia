<html>
<head>
	<meta charset="utf8">
	<title>Postgres SQL Injection Test</title>

	<script type="text/javascript">
	ASDF1234
	</script>
</head>
<body>
<div onclick="alert('ASDF1234');">ASDF1234</div>


<form id="form1" name="popabol" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="GET">
a: <input type="text" name="a" value=""><br>
b: <input type="text" name="b" value=""><br>
<input type="submit" value="Давай POST">
</form>


<?php
$host = 'localhost';
$dbname = 'sql_injection_test';
$user = 'postgres';
$password = 'terentek1';

$dbconn = pg_connect("host=".$host." dbname=".$dbname." user=".$user." password=".$password)
	or die('PGSQL CONNECTION ERROR:' . pg_last_error());

if(!isset($_GET['id']))
	echo "Empty!<br>";
else {
	$query = "SELECT * FROM atata WHERE id={$_GET['id']}";
	$result = pg_query($dbconn, $query) or die("Query error: " . pg_last_error());

	while($line = pg_fetch_array($result)) {
		echo "<div>ID: ".$line['id']." || Value: ".$line['text']."</div>";
	}
}


foreach($_GET as $key => $value)
{
	if(is_array($value)) {
		foreach($value as $k => $val) {
			if($k === "a" || $k === "b")
				echo $k . " = " . $val . "<br>";
		}
	} else {
		if($key === "a" || $key === "b")
			echo $key . " = " . $value . "<br>";
	}
}

foreach($_POST as $key => $value)
{
	if(is_array($value)) {
		foreach($value as $k => $val)
			echo $k. " = " . $val . "<br>";
	} else {
		echo $key . " = " . $value . "<br>";
	}
}


?>

</body>
</html>