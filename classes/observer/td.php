<?php
require_once APPPATH.'vendor/Fluent/Autoloader.php';
use Fluent\Logger\FluentLogger;
class Observer_Td extends Orm\Observer {

	public function after_save(Orm\Model $obj){
		//ここでいっかい\Log::debug()しないと初回log_confが読めない
		//あとで直す
		\Log::debug(__FUNCTION__);

		$save_data = array();
		foreach(array_keys($obj->properties()) as $p){
			$save_data[$p] = $obj->{$p};
		}

		//TODO 別Configちゃんと持つ＆Developの時どうするんですか？
		$log_config = \Config::get('log');

		$host = empty($log_config['td']['host']) ? null : $log_config['td']['host'];
		$port = empty($log_config['td']['port']) ? null : $log_config['td']['port'];
		$options = empty($log_config['td']['options']) ? array() : $log_config['td']['options'];
		$packer = empty($log_config['td']['packer']) ? null : $log_config['td']['packer'];
		$database = empty($log_config['td']['database']) ? 'default' : $log_config['td']['database'];
		$table_name = $obj->table();

		Fluent\Autoloader::register();
		$logger = new FluentLogger($host,$port,$options,$packer);
		$res = $logger->post('td.'.$database.'.'.$table_name,$save_data);

	}
}
