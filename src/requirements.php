<?php

if (!file_exists(__DIR__ . '/config.php')) {
	echo 'Error: Configuration file doesn\'t exist. create one based on the example file.' . PHP_EOL;

	$ytdlp = exec('yt-dlp --version && ffmpeg -version', $ytOut, $ytStatus);
	if ($ytStatus !== 0) {
		echo 'Error: yt-dlp and ffmpeg are required' . PHP_EOL;
	}

	exit;
}