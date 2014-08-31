<html>
<head>
	<meta charset="utf8">
	<title>SQL Injection Test</title>
</head>
<body>

<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpassword = "terentek1";
$dbname = "test";

$dbhandle = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
if(mysqli_connect_errno()) 
	echo "error";
else
{
	if( !isset($_GET['id']) )
		echo "Empty";
	else
	{
		$res = mysqli_query($dbhandle, "SELECT * FROM sql_injection_test WHERE id = {$_GET['id']}");
		if($res)
		{
			while($row = mysqli_fetch_assoc($res))
				echo "<div>Number: {$row['id']} ||| Info: {$row['desc']}</div>";
		}
		else
			echo mysqli_error($dbhandle);
	}
}
?>


<!--
<div>
GET<br>
<form id="form2" name="atata">
azaza: <input type="text" name="azaza" value=""><br>
bazaza: <input type="text" name="bazaza" value=""><br>
<select name="zuzana">
	<option value="1">1</option>
	<option value="2">2</option>
</select>
<input type="hidden" name="skolko" value="55">
<textarea name="comment" cols="40" rows="3">asdasd</textarea>
<input type="radio" name="pepe" value="uzuzu">Uzuzu!
<input type="submit" value="Давай GET">
</form>
</div>
-->
<div>
POST<br><!-- ТУТ В ПАРСЕРЕ ПРОБЛЕМА (В месте action, где URL принимает вид неполного URL. если это так, то надо взять доменное имя от текущего и прибавить этот неполный URL --> 

<form id="form1" name="popabol" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="GET">
a: <input type="text" name="a" value=""><br>
b: <input type="text" name="b" value=""><br>
<!--<select multiple name="chack[]">
	<option value="1">1</option>
	<option value="2">2</option>
</select>-->
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