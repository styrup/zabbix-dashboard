<?php
require_once 'lib/ZabbixApi.class.php';

use ZabbixApi\ZabbixApi;

class zabbixModule
{
	public function GetZabbixServerConnection()
	{
		$zabbixServer = getenv('ZABBIX_SERVER');
		if (empty($zabbixServer)) {
			echo "ZABBIX_SERVER not defined.";
			return;
		}

		$zabbixUser = getenv('ZABBIX_USER');
		if (empty($zabbixUser)) {
			echo "ZABBIX_USER not defined.";
			return;
		}

		$zabbixPassword = getenv('ZABBIX_PASSWORD');
		if (empty($zabbixUser)) {
			echo "ZABBIX_PASSWORD not defined.";
			return;
		}

		try {
			$nossl = array("verify_peer" => false, "verify_peer_name" => false,);
			// connect to Zabbix API
			$api = new ZabbixApi($zabbixServer . "/api_jsonrpc.php", $zabbixUser, $zabbixPassword, '', '', '', $nossl);
		} catch (Exception $e) {

			// Exception in ZabbixApi catched
			echo "Error: " . $e->getMessage();
		}

		return $api;
	}

	public function GetAlerts($hids, $minpri)
	{
		$api = $this->GetZabbixServerConnection();
		$webhids = explode(",", $hids);
		try {
			$parms = array(
				'output' => array('triggerid', 'description', 'priority'),
				'filter' => array('value' => '1'),
				'groupids' => $webhids,
				'min_severity' => $minpri,
				'sortfield' => 'lastchange',
				'sortorder' => 'DESC',
				'selectHosts' => 'extend',
				'selectGroups' => 'extend',
				'selectItems' => 'extend',
				'selectFunctions' => 'extend',
				'monitored' => 'true',
				'withLastEventUnacknowledged' => 'true',
				'expandDescription' => 'true',
				'expandComment' => 'true',
				'expandExpression' => 'true'
			);
			if (!isset($webhids)) {
				unset($parms['groupids']);
				$hostgroups = "All";
			} else {


				$parms1 = array(
					'output' => 'extend',
					'groupids' => $webhids
				);
				$myhost = $api->hostgroupGet($parms1);
				$hostgroups = "";
				foreach ($myhost as $hgroup) {
					$hostgroups = $hostgroups . " " . $hgroup->name;
				}
			}
			$zabbixTriggers = $api->triggerGet($parms);
		} catch (Exception $e) {
			// Exception in ZabbixApi catched
			echo $e->getMessage();
		}

		$arr = array();

		foreach ($zabbixTriggers as $zabbixTrigger) {
			$desc = $zabbixTrigger->description;
			$zfunctions = $zabbixTrigger->functions;
			$itemindex = 1;
			foreach ($zfunctions as $zfunction) {
				$zitemid =  $zfunction->itemid;
				foreach ($zabbixTrigger->items as $zfuncitem) {
					if ($zfuncitem->itemid == $zitemid) {
						$zneedle = "{ITEM.VALUE" . $itemindex . "}";
						$zreplace = $zfuncitem->lastvalue;
						$desc = str_replace($zneedle, $zreplace, $desc);
					}
				}
				$itemindex++;
			}
			$host  = $zabbixTrigger->hosts[0]->name;
			$hostID = $zabbixTrigger->hosts[0]->hostid;
			$tpri = $zabbixTrigger->priority;
			$mParms = array('output' => 'extend', 'hostids' => $hostID);
			$UserMacros = $api->usermacroGet($mParms);
			foreach ($UserMacros as $UserMacro) {
				$desc = str_replace($UserMacro->macro, $UserMacro->value, $desc);
			}
			$times = date("j/n H:i", $zabbixTrigger->lastchange);
			$text = str_replace(array("{HOSTNAME}", "{HOST.NAME}"), $host, $desc);

			$arr[] = new AlertItem($host, $text, $zabbixTrigger->lastchange, $tpri);
		}
		return $arr;
	}
}
