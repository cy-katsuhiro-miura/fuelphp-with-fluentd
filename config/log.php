<?php 
$log_conf =  array(
	'driver' => 'file',

	'drivers' => array(
		'file' => array(
				'log_threshold'    => Fuel::L_INFO,
				'log_path' => DOCROOT.'../logs/',
				),
		'copy' => array('file','td'),
		'td' => array(
			'log_threshold'    => Fuel::L_INFO,
			'database' => 'default',
			'host'     => 'localhost',
			'port' => 8888,
			//'host' => 'unix:///var/run/td-agent/td-agent.sock',
			//'port' => null,
			//'options' => array(),
			//'packer' => null,
		),
	),
);

return $log_conf;
