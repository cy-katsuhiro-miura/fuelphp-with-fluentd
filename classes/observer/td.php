<?php

namespace Fluentd;

class Observer_Td extends \Orm\Observer {

	public $td_config;

	public function __construct($class)
	{
		\Config::load('td', true);
		$this->td_config = \Config::get('td');
	}
	public function after_save(\Orm\Model $obj)
	{

		$save_data = array();
		foreach(array_keys($obj->properties()) as $p){
			$save_data[$p] = $obj->{$p};
		}

		$host     = empty($this->td_config['td']['host'])     ? null      : $this->td_config['td']['host'];
		$port     = empty($this->td_config['td']['port'])     ? null      : $this->td_config['td']['port'];
		$options  = empty($this->td_config['td']['options'])  ? array()   : $this->td_config['td']['options'];
		$packer   = empty($this->td_config['td']['packer'])   ? null      : $this->td_config['td']['packer'];
		$database = empty($this->td_config['td']['database']) ? 'default' : $this->td_config['td']['database'];
		$table_name = $obj->table();

		\Fluent\Autoloader::register();
		$logger = new \Fluent\Logger\FluentLogger($host,$port,$options,$packer);
		$res = $logger->post('td.'.$database.'.'.$table_name,$save_data);

	}
}
