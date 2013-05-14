#about
fuelphp$B$+$i(Bfluentd$B$rMxMQ$9$k$?$a$N%Q%C%1!<%8$G$9!#(B
logger$B$r3HD%$7$F!"(Bfluentd$B$K=PNO$G$-$k$h$&$K$7$F$$$^$9!#(B
$BKt!"(BORM$B%Q%C%1!<%8$r;HMQ$7$F$$$k>l9g!"(B
observer$B$r;XDj$9$k$3$H$G!"(Bdb$B$NJQ99MzNr>pJs$r(Bfluentd$B$K=PNO$G$-$^$9!#(B

#installation
##Download 
GitHub$B$+$i%=!<%9$r(BDownload$B$7$?8e!"(Bfuel/packages/$B$N2<$KE83+$7$F$/$@$5$$!#(B
	$ git clone git@github.com:katsuhiro-miura/fuelphp-with-fluentd.git 
	$ cp -r fuelphp-with-fluentd fuel/packages/fluentd

$B%5%V%b%8%e!<%k$G;XDj$7$F$$$?$@$$$F$b9=$$$^$;$s!#(B($B<+J,$N4D6-$G$O$&$^$/F0$-$^$;$s$G$7$?$,(B)
	$ git submodule add git@github.com:katsuhiro-miura/fuelphp-with-fluentd.git fuel/packages/fluentd
	$ git submodule update --init fuel/packages/fluentd

##Using fluent-logger-php
PHP$B$+$i(Bfluentd$B$X$N=PNO$O(Bfluentd$B$,Ds6!$7$F$$$k(B'fluent-logger-php'$B%i%$%V%i%j$rMxMQ$7$F$$$^$9!#(B
$B%Q%C%1!<%8$NCf?H$K$b4^$^$l$F$$$^$9$,!":G?7HG$O0J2<$+$i<hF@$7$F$/$@$5$$!#(B

	$ git clone https://github.com/fluent/fluent-logger-php.git
	$ cp -r src/Fluent vendor/

##Copy config file
config$B%U%!%$%k$G(Bpluentd$B$X$N=PNOJ}K!$rDj5A$7$F$$$^$9!#(B

	$ cp config/log.php app/config
	$ cp config/observer.php app/config


#How to use
##Edit config file
	$ vi app/config/config.php

	'packages' => array(
		'orm',
		'fluentd',
	),

##Edit log class file
Fluentd\Log$B$r7Q>5$7$?(BLog$B%/%i%9$r:n@.$7!"(B
$B3F%a%=%C%I$r;HMQ$7$F$/$@$5$$!#(B

	vi class/log.php

	<?php
	class Log.php extend Fluentd\Log {
	}

	<?php
	
	class SomeContrller extends Controller_template{
	
	function action_index()
	{
		\Log::debug(__FUNCTION__);
	}


##Edit ORM model file
ORM$B%Q%C%1!<%8$r;HMQ$7$F$$$k>l9g!"(B
Observer$B$r;XDj$9$k$3$H$G!"$=$N%?%$%_%s%0$G$N(Bdb$B$NCM$r(Bfluentd$B$K=PNO$7$^$9!#(B

	vi model/model.php
	
	class model {
	        protected static $_observers = array(
	                'Fluentd\Observer_Td' => array(
	                        'events' => array('after_save'),
	                ),
			);		
	}
