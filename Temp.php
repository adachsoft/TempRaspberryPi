<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Temp
 *
 * @author arek
 */
class Temp
{

	public static function getSensors()
	{
		$str = file_get_contents('/sys/bus/w1/devices/w1_bus_master1/w1_master_slaves');
		$dev_ds18b20 = preg_split("/\\r\\n|\\r|\\n/", $str);
		var_dump($dev_ds18b20);
	}
}
