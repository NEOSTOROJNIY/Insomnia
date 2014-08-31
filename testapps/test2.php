<html>
<head>
	<meta charset="utf8">
	<title>Test 2</title>
</head>
<body>
<?php
$host     = 'localhost';
$dbname   = 'sql_injection_test';
$user     = 'postgres';
$password = 'terentek1';
$dbconn = pg_connect("host=".$host." dbname=".$dbname." user=".$user." password=".$password)
	or die('Connection fails');
if(!isset($_GET['id']))
	echo "Empty!<br>";
else {
	$id = $_GET['id'];
	//settype($id, "integer");
	$query  = "SELECT * FROM atata WHERE id={$id}";
	$result = pg_query($dbconn, $query);
	if($result == true) {
		while($line = pg_fetch_array($result))
			echo "<div>ID: ".$line['id']." || Value: ".$line['text']."</div>";		
	}
}
?>
</body>
</html>