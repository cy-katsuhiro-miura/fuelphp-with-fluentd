<?php 
$observer_conf =  array(
	'td' => array(
		'database' => 'default',
		//'host' => 'unix:///var/run/td-agent/td-agent.sock',
		//'port' => null,
		'host' => 'localhost',
		'port' => 8888,
		//'options' => array(),
		//'packer' => null,
	),
);

return $observer_conf;
