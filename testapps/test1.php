<html>
<head>
	<meta charset="utf8">
	<title>Test 1</title>
</head>
<body>
<?php
$dbhost     = "localhost";
$dbuser     = "root";
$dbpassword = "terentek1";
$dbname     = "test";
$dbhandle = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
if(mysqli_connect_errno())
	echo "Connection Error";
else
{
	if( !isset($_GET['id']) )
		echo "Empty";
	else
	{
		$res = mysqli_query($dbhandle, "SELECT * FROM sql_injection_test WHERE id = {$_GET['id']} GROUP BY id");
		if($res) {
			while($row = mysqli_fetch_assoc($res))
				echo "<div>Number: {$row['id']} ||| Info: {$row['desc']}</div>";
		} else
			echo mysqli_error($dbhandle);
	}
}
?>
<div>
Profile Data:
<form id="form1" name="popabol" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="POST">
First name: <input type="text" name="first_name" value=""><br>
Second name: <input type="text" name="second_name" value=""><br>
<input type="submit" value="Давай POST">
</form>
</div>
<hr>
<hr>
<?php
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
foreach($_POST as $key => $value) {
	if(is_array($value)) {
		foreach($value as $k => $val)
			echo $k. " = " . $val . "<br>";
	} else
		echo $key . " = " . $value . "<br>";
}
?>
</body>
</html>