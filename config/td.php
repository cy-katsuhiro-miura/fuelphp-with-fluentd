<?php 
$td_conf =  array(
		'database' => 'default',
		//'host' => 'localhost',
		'host' => 'unix:///var/run/td-agent/td-agent.sock',
		//'port' => null,
		//'options' => array(),
		//'packer' => null,
);

if(ENVIRONMENT == ENVIRONMENT_DEVELOPMENT ){
	if(PLATFORM === PLATFORM_MOBAGE){
		$td_conf['database'] = 'td.dev-kjs-mbga';
	}
}elseif(ENVIRONMENT === ENVIRONMENT_TESTING){
	if(PLATFORM === PLATFORM_MOBAGE){
		$td_conf['database'] = 'td.t-kjs-mbga';
	}
}elseif( ENVIRONMENT === ENVIRONMENT_PRODUCTION) {
	if(PLATFORM === PLATFORM_MOBAGE){
		$td_conf['database'] = 'td.kjs-mbga';
	}
}

return $td_conf;
