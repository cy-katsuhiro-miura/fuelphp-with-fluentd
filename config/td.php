<?php 
$td_conf =  array(
	'driver' => 'file',
	//'driver' => 'td',

	'file' => array(),
	'td' => array(
		'database' => 't_kjs',
		'host' => 'unix:///var/run/td-agent/td-agent.sock',
		//'port' => null,
		//'options' => array(),
		//'packer' => null,

		//TODO Fuel::L_INFOとかで出力するもの指定
		'copy_driver' => array('file'),
	),
);
if(ENVIRONMENT == ENVIRONMENT_DEVELOPMENT ){
	$log_conf['td']['driver'] = 'file';
}elseif(ENVIRONMENT === ENVIRONMENT_TESTING){
	if(PLATFORM === PLATFORM_MOBAGE){
		$log_conf['td']['driver'] = 'file';
		$log_conf['td']['database'] = 'td.t_kjs_mbga';
	}
}elseif( ENVIRONMENT === ENVIRONMENT_PRODUCTION) {
	if(PLATFORM === PLATFORM_MOBAGE){
		$log_conf['td']['driver'] = 'file';
		$log_conf['td']['database'] = 'td.kjs-mbga';
	}
}

return $log_conf;
