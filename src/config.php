<?php

define('CONFIG', [
	'downloadDir' => getenv('AVDL_DOWNLOAD_DIR'), // directory must have write permission by the web server process
	'basicAuth' =>
	getenv('AVDL_AUTH') ? [
		explode(':', getenv('AVDL_AUTH'))[0] => explode(':', getenv('AVDL_AUTH'))[1]
	] : [],
	'ytDlpOpts' => getenv('AVDL_YTDLP_OPTS') ?? ''
]);
