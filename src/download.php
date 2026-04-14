<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/utils.php';

if (!empty($_GET['url'])) {

	$url = $_GET['url'];
	if (!filter_var($url, FILTER_VALIDATE_URL)) {
		header('content-type: text/plain');
		http_response_code(400);
		echo 'INVALID URL';
		exit();
	}

	$downloadType = $_GET['type'];
	$downloadQuality = (int)$_GET['quality'] ?? 0;
	$downloadMode = (int)$_GET['mode'] ?? 0;

	$cmd = "";

	switch ($downloadType) {
		case 'mp3':
			$cmd = genYtMp3Cmd($url);
			break;
		case 'mp4':
		default:
			$cmd = genYtMp4Cmd($url, $downloadQuality);
			break;
	}


	$downloadFile = '';

	set_time_limit(0);

	header("Content-Type: text/event-stream");
	header("Cache-Control: no-cache");
	header("Connection: keep-alive");

	while (ob_get_level() > 0)
		ob_end_flush();
	ob_implicit_flush(true);

	$process = proc_open($cmd, [
		1 => ["pipe", "w"],
		2 => ["pipe", "w"]
	], $pipes, CONFIG['downloadDir']);

	stream_set_blocking($pipes[1], false);
	stream_set_blocking($pipes[2], false);

	while (true) {
		if (connection_aborted())
			break;

		foreach ([1 => 'stdout', 2 => 'stderr'] as $i => $type) {
			$line = fgets($pipes[$i]);
			if ($line !== false) {

				// grab file name
				if (!$downloadFile) {
					$downloadFile = grabFileName($line);
				}

				echo "data: " . json_encode([
					'type' => $type,
					'data' => $line
				]) . "\n\n";
			}
		}

		if (feof($pipes[1]) && feof($pipes[2]))
			break;

		usleep(100000);
	}

	fclose($pipes[1]);
	fclose($pipes[2]);
	proc_close($process);

	echo "data: " . json_encode([
		'type' => 'stdout',
		'data' => 'File: ' . $downloadFile
	]) . "\n\n";

	if ($downloadMode === DWNLD_MODES::$DIRECT || $downloadMode === DWNLD_MODES::$BOTH) {
		$isKeep = '';
		if ($downloadMode === DWNLD_MODES::$BOTH) {
			$isKeep = '&keep=true';
		}
		echo "data: " . json_encode([
			'type' => 'download_url',
			'data' => '/getFile?file=' . rawurlencode($downloadFile) . $isKeep
		]) . "\n\n";
	}
}

echo "data: " . json_encode([
	'type' => 'event',
	'data' => 'close'
]) . "\n\n";