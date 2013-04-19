<?php

class Log_File extends Fuel\Core\Log{
	public static function write($level, $msg, $method = null){
		if ($level > \Config::get('log_threshold'))
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

		if (Config::get('profiling'))
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
			if(isset($backtrace[4])){
				$call .= isset($backtrace[4]['class']) ? $backtrace[4]['class'] : ' - ';
				$call .= isset($backtrace[4]['type']) ? $backtrace[4]['type'] : ' - ';
				$call .= isset($backtrace[4]['function']) ? $backtrace[4]['function'] : ' - ';
				$call .= isset($backtrace[3]['line']) ? ':'.$backtrace[3]['line'] : ' - ';
			}
		}

		$message .= $level.' '.(($level == 'info') ? ' -' : '-').' ';
		$message .= date(\Config::get('log_date_format'));
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
