<?php
#error_reporting(E_ALL);
#ini_set('display_errors', '1');
require "lib/AlertItem.php";
require "modules/zabbixModule.php";

if (isset($_GET["timeout"])){
        $tout = $_GET["timeout"];
} else {
	$tout = 300;
}
// Her kommmer module loads
$array = array();

$hids = $_GET["hids"];
if (isset($hids)) {
	$whost = $hids; 
	if (isset($_GET["minpri"])){
	        $minpri = $_GET["minpri"];
	} else {
	        $minpri = 2;
	}
        $zbx = new zabbixModule();
        $array = array_merge($array,$zbx->GetAlerts($hids,$minpri));
}

//cisco port errors from Graylog alerts
if (isset($_GET["ciscoport"])) {
        $ucs = new cportModule(); 
        $array = array_merge($array,$ucs->GetAlerts());
}

//UCS alerts
if (isset($_GET["ucs"])) {
        $ucs = new ucsModule(); 
        $array = array_merge($array,$ucs->GetAlerts());
}

//struxureware alerts
if (isset($_GET["struxureware"])) {
        $strux = new struxurewareModule(); 
        $array = array_merge($array,$strux->GetAlerts());
}
//dummy alerts
if (isset($_GET["dummy"])) {
        $dum = new dummyModule(); 
        $array = array_merge($array,$dum->GetAlerts());
}

if (count($array) == 0) {
	$cssmain = "maingreeen";
}else {
	$cssmain = "main";
}
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="<?php echo $tout;?>;url=<?php echo $protocol.$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER['REQUEST_URI'];?>" />
<title>List triggers</title>
<style type="text/css">
#main {
        font-family: Verdana, Geneva, sans-serif;
        margin: 2px;
        background-color:#CCC;
        font-size:xx-large;
}

#maingreeen {
        font-family: Verdana, Geneva, sans-serif;
        margin: 2px;
        background-color:#CCE2CC;
        font-size:xx-large;
}

#maingreeen {
        font-family: Verdana, Geneva, sans-serif;
        margin: 2px;
        background-color:#CCE2CC;
        font-size:xx-large;
	background-image:url('its-all-good-clipart-8.jpg');
    	background-repeat:no-repeat;
    	background-attachment:fixed;
    	background-position:center;
}


#main table {
		background-color:#FFF
}


#main .listhosts {
	width: 50%;
}
	
#main .st2 {
        background-color:#EFEFCC;
}

#main .st4 {
        background-color:#FF8888;
}

#main .st3 {
        background-color:#DDAAAA;
}

#main .st0 {
        background-color:#CCE2CC;
}


#main .st1 {
        background-color:#CCE2CC;
}

#main .st5 {
        background-color:#FF0000;
}
#main .hright {
        text-align:left;
        font-size:smaller;
        border-bottom: 1px solid black;
        background-color:#CCCCEF;
}


</style>
</head>
<body id="<?php echo $cssmain;?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
        function sort_alert_date($a,$b){
                return $a->Time < $b->Time;

        }
        usort($array,"sort_alert_date");
        foreach($array as $Alert) {
	        echo "  <tr class=\"st$Alert->Sev\">\n";
                $atime = date("j/n H:i",$Alert->Time);
    	        echo "<td scope=\"col\">$atime $Alert->Host: $Alert->Desc</td>\n";
	        echo "  </tr>\n";
	}
?>
</table>
</body>
</html>
