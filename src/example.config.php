<?php

define('CONFIG', [
	'downloadDir' => '/home/user/Downloads/av-dl', // directory must have write permission by the web server process
	'basicAuth' => [
		'user' => '1234'
	],
	'ytDlpOpts' => '--js-runtimes node'
]);