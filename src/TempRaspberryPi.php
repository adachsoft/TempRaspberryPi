<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace AdachSoft\RaspberryPi;

/**
 * Description of Temp
 *
 * @author Arkadiusz Adach
 */
class TempRaspberryPi
{

	const FILE_LIST_OF_DEVICES = '/sys/bus/w1/devices/w1_bus_master1/w1_master_slaves';

	private $sensors = [];
	private $mapSensors = [];
	private $lastTimeExec = false;

	/**
	 * Construct for class
	 */
	public function __construct()
	{
		$this->getSensors();
	}

	public function getLastTimeExec()
	{
		return $this->lastTimeExec;
	}

	/**
	 * Get all devices
	 */
	public function getSensors()
	{
		$str = file_get_contents(static::FILE_LIST_OF_DEVICES);
		$devs = preg_split("/\\r\\n|\\r|\\n/", $str);

		$this->sensors = [];
		foreach ($devs as $key => $val) {
			if (!empty($val)) {
				$this->sensors[] = $val;
			}
		}

		return $this->sensors;
	}

	public function setMap($key, $devId)
	{
		if (isset($this->mapSensors[$key])) {
			$this->throwException('zvxcd');
		}
		$this->mapSensors[$key] = $devId;
	}

	public function getDeviceId($key)
	{
		if (preg_match("/^[0-9]$/i", $key)) {
			if (!isset($this->sensors[$key])) {
				$this->throwException('Unknown device: ' . $key);
			}
			return $this->sensors[$key];
		} elseif (in_array($key, $this->sensors)) {
			return $key;
		}

		return $this->mapSensors[$key];
	}

	/**
	 * Read the temperature of the device in degrees Celsius
	 * @param int|string $sensor
	 * @return float
	 */
	public function readTemp($sensor)
	{
		$timeStart = microtime(true);
		$this->lastTiemExec = false;
		$dev = $this->getDeviceId($sensor);

		$temp = 0;
		$temp_path = "/sys/bus/w1/devices/$dev/w1_slave";
		$str = file_get_contents($temp_path);
		if (preg_match('|t\=([0-9]+)|mi', $str, $m)) {
			$temp = (float) ($m[1] / 1000);
		} else {
			return false;
		}

		$timeEnd = microtime(true);
		$this->lastTimeExec = $timeEnd - $timeStart;
		return $temp;
	}

	protected function throwException($message)
	{
		throw new Exception($message);
	}
}
