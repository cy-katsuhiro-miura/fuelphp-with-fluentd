<?php

namespace Fluentd\Log;

class File extends \Fluentd\Log
{

	public static function write($level, $msg, $method = null)
	{
		$log_threshold = \Config::get('log_threshold');
		$config = \Config::get('log',array());

		if( isset($config['drivers']['file']['log_threshold']))
		{
			$log_threshold = $config['drivers']['file']['log_threshold'];
		}
		if ($level > $log_threshold)
		{
			return false;
		}
		$levels = array(
			1  => 'Error',
			2  => 'Warning',
			3  => 'Debug',
			4  => 'Info',
		);
		$level = isset($levels[$level]) ? $levels[$level] : $level;

		if (\Config::get('profiling'))
		{
			\Console::log($method.' - '.$msg);
		}

		$filepath = \Config::get('log_path').date('Y/m').'/';

		if ( ! is_dir($filepath))
		{
			$old = umask(0);

			mkdir($filepath, \Config::get('file.chmod.folders', 0777), true);
			umask($old);
		}

		$filename = $filepath.date('Y-m-d').'_log';

		$message  = '';

		if ( ! $exists = file_exists($filename))
		{
			$message .= "<"."?php defined('COREPATH') or exit('No direct script access allowed'); ?".">".PHP_EOL.PHP_EOL;
		}

		if ( ! $fp = @fopen($filename, 'a'))
		{
			return false;
		}

		$call = '';
		if ( ! empty($method))
		{
			$call .= $method;
		}else{
			$backtrace = debug_backtrace();
			$i=0;
			for(;$i<count($backtrace);$i++){
				$backtrace[$i]['object'] = null;
				$break = false;

				if(isset($backtrace[$i]['class'])){
					if(!strstr($backtrace[$i]['class'],__NAMESPACE__)
						and !strstr($backtrace[$i]['class'],'Fuel\Core\Log')) {
	
						//
						if($level === 'Error'){
							if ($level == 'Error') //var_dump($backtrace);
						}
						$break = true;
					}
				}

				if($break){
					break;
				}
			}
			if(isset($backtrace[$i])){
				$call .= isset($backtrace[$i]['class'])    ? $backtrace[$i]['class']      : ' - ';
				$call .= isset($backtrace[$i]['type'])     ? $backtrace[$i]['type']       : ' - ';
				$call .= isset($backtrace[$i]['function']) ? $backtrace[$i]['function']   : ' - ';
				$call .= isset($backtrace[$i-1]['line'])   ? ':'.$backtrace[$i-1]['line'] : ' - ';
			}
		}

		$message .= $level.' '.(($level == 'info') ? ' -' : '-').' ';
		$message .= date(\Config::get('log_date_format'));
		$message .= ' - ' . 'ouid=' . parent::$opensocial_user_id;
		$message .= ' --> '.(empty($call) ? '' : $call.' - ').$msg.PHP_EOL;

		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);

		if ( ! $exists)
		{
			$old = umask(0);
			@chmod($filename, \Config::get('file.chmod.files', 0666));
			umask($old);
		}

		return true;
	}
}
