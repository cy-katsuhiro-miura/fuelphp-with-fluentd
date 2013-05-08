<?php

namespace Fluentd;

class Observer_Td extends \Orm\Observer {

	public static $td_config;

	public function __construct($class)
	{
		\Config::load('observer', true);
		$ob_config = \Config::get('observer');
		self::$td_config = $ob_config['td'];
	}

	public function after_save(\Orm\Model $obj)
	{

		$save_data = array();
		foreach(array_keys($obj->properties()) as $p){
			$save_data[$p] = $obj->{$p};
		}

		$host     = empty(self::$td_config['host'])     ? null      : self::$td_config['host'];
		$port     = empty(self::$td_config['port'])     ? null      : self::$td_config['port'];
		$options  = empty(self::$td_config['options'])  ? array()   : self::$td_config['options'];
		$packer   = empty(self::$td_config['packer'])   ? null      : self::$td_config['packer'];
		$database = empty(self::$td_config['database']) ? 'default' : self::$td_config['database'];
		$table_name = $obj->table();

		\Fluent\Autoloader::register();
		$logger = new \Fluent\Logger\FluentLogger($host,$port,$options,$packer);
		$res = $logger->post('td.'.$database.'.'.$table_name,$save_data);

	}
}
