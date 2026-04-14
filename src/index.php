<?php

require_once __DIR__ . '/requirements.php';
require_once __DIR__ . '/utils.php';

// basic auth
require_once __DIR__ . '/basicAuth.php';

switch (getRoute()) {
	case '/':
		require __DIR__ . '/ui.php';
		break;
	case '/download':
		require __DIR__ . '/download.php';
		break;
	case '/getFile':
		require __DIR__ . '/getFile.php';
		break;
	default:
		header('HTTP/1.1 404 Not Found');
		break;
}