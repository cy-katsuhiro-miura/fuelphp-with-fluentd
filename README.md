#about
fuelphpからfluentdを利用するためのパッケージです。
loggerを拡張して、fluentdに出力できるようにしています。
又、ORMパッケージを使用している場合、
observerを指定することで、dbの変更履歴情報をfluentdに出力できます。

#installation
##Download 
GitHubからソースをDownloadした後、fuel/packages/の下に展開してください。
	$ git clone git@github.com:katsuhiro-miura/fuelphp-with-fluentd.git 
	$ cp -r fuelphp-with-fluentd fuel/packages/fluentd

サブモジュールで指定していただいても構いません。(自分の環境ではうまく動きませんでしたが)
	$ git submodule add git@github.com:katsuhiro-miura/fuelphp-with-fluentd.git fuel/packages/fluentd
	$ git submodule update --init fuel/packages/fluentd

##Using fluent-logger-php
PHPからfluentdへの出力はfluentdが提供している'fluent-logger-php'ライブラリを利用しています。
パッケージの中身にも含まれていますが、最新版は以下から取得してください。

	$ git clone https://github.com/fluent/fluent-logger-php.git
	$ cp -r src/Fluent vendor/

##Copy config file
configファイルでpluentdへの出力方法を定義しています。

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
Fluentd\Logを継承したLogクラスを作成し、
各メソッドを使用してください。

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
ORMパッケージを使用している場合、
Observerを指定することで、そのタイミングでのdbの値をfluentdに出力します。

	vi model/model.php
	
	class model {
	        protected static $_observers = array(
	                'Fluentd\Observer_Td' => array(
	                        'events' => array('after_save'),
	                ),
			);		
	}
