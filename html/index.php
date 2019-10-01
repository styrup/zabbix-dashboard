<?php
error_reporting(-1);
ini_set("date.timezone", getenv('TZ'));
require "loadmodules.php";
$zabbixlink = "Please select host group(s)";
if (isset($_POST["selectHostGroup"])) {
  $hids = "";
  foreach ($_POST["selectHostGroup"] as $selhost) {
    $hids = $hids . "," . $selhost;
  }
  $hids = substr($hids, 1);
  if (isset($_POST["prios"])) {
    $pri = $_POST["prios"];
  }
  $headl = "";
  if (isset($_POST["zhead"])) {
    $headl = "$headl&showheadline=1";
  }

  $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  $zabbixlink = $protocol . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . "/triggers.php?hids=$hids&minpri=$pri$headl";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Zabbix Dashboard Constructor</title>
  <style type="text/css">
    #main,
    a:link {
      font-family: Verdana, Geneva, sans-serif;
      font-size: 12px;
    }

    #main .listhosts {
      width: 50%;
    }
  </style>
</head>

<body id="main">
  <h1>Zabbix Dashboard Constructor </h1>
  <p>Select host group:</p>
  <form id="form1" name="form1" method="post" action="">
    <p>
      <select name="selectHostGroup[]" size="30" multiple="multiple" class="listhosts" id="selectHostGroup">
        <?php
        $zbx = new zabbixModule();
        $api = $zbx->GetZabbixServerConnection();
        try {

          $hostGroups = $api->hostgroupGet(array(
            'output' => 'extend'
          ));
          function compareByName($a, $b)
          {
            return strcmp($a->name, $b->name);
          }
          usort($hostGroups, 'compareByName');
          foreach ($hostGroups as $hostGroup) {
            echo "<option value=\"$hostGroup->groupid\">$hostGroup->name</option>\n";
          }
        } catch (Exception $e) {
          echo $e->getMessage();
        }

        ?>
      </select>
      <label for="Prios"></label>
      <label for="prios2"><br />
        Select minimum alert level:<br />
      </label>
      <select name="prios" size="6" class="listhosts" id="prios">
        <option value="0">Not classified</option>
        <option value="1">Information</option>
        <option value="2" selected="selected">Warning</option>
        <option value="3">Average</option>
        <option value="4">High</option>
        <option value="5">Disaster</option>
      </select>
    </p>
    <p>
      <label for="zurl"></label>
      <input name="zurl" type="text" class="listhosts" id="zurl" value="<?php echo $zabbixlink; ?>" />
    </p>
    <p>
      <a href="<?php echo $zabbixlink; ?>" target="_blank"><?php echo $zabbixlink; ?></a>
    </p>
    <p>
      <label for="prios"></label>
      <input type="submit" name="Lavlink" id="Lavlink" value="Create link for dashboard" />
    </p>

  </form>
  <p>&nbsp;</p>
</body>

</html>