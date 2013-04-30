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
			//'host' => 'unix:///var/run/td-agent/td-agent.sock',
			//'port' => null,
			//'options' => array(),
			//'packer' => null,
		),
	),
);

if(ENVIRONMENT == ENVIRONMENT_DEVELOPMENT ){
	$log_conf['driver'] = 'file';
}elseif(ENVIRONMENT === ENVIRONMENT_TESTING){
	if(PLATFORM === PLATFORM_MOBAGE){
		$log_conf['driver'] = 'copy';
		$log_conf['td']['database'] = 't-kjs-mbga';
	}
}elseif( ENVIRONMENT === ENVIRONMENT_PRODUCTION) {
	if(PLATFORM === PLATFORM_MOBAGE){
		$log_conf['driver'] = 'copy';
		$log_conf['td']['database'] = 'kjs-mbga';
	}
}

return $log_conf;
