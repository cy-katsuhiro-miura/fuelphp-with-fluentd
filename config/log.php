<?php 
$log_conf =  array(
	'driver' => 'file',
	//'driver' => 'td',

	'drivers' => array(
		'file' => array(),
		'td' => array(
			'database' => 't_kjs_mbga',
			'host' => 'unix:///var/run/td-agent/td-agent.sock',
			//'port' => null,
			//'options' => array(),
			//'packer' => null,
	
			//TODO Fuel::L_INFOとかで出力するもの指定
			'copy_driver' => array('file'),
		),
	),
);
if(ENVIRONMENT == ENVIRONMENT_DEVELOPMENT ){
	$log_conf['driver'] = 'file';
}elseif(ENVIRONMENT === ENVIRONMENT_TESTING){
	if(PLATFORM === PLATFORM_MOBAGE){
		$log_conf['driver'] = 'td';
		$log_conf['td']['database'] = 't-kjs-mbga';
	}
}elseif( ENVIRONMENT === ENVIRONMENT_PRODUCTION) {
	if(PLATFORM === PLATFORM_MOBAGE){
		$log_conf['driver'] = 'td';
		$log_conf['td']['database'] = 'kjs-mbga';
	}
}

return $log_conf;
