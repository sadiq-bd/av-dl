<?php

if (!getenv('AVDL_DOWNLOAD_DIR')) {
	echo 'Error: AVDL_DOWNLOAD_DIR environment variable not set. (optional: set AVDL_AUTH="user:password" for authentication & set AVDL_YTDLP_OPTS for custom command line args for yt-dlp)' . PHP_EOL;

	$ytdlp = exec('yt-dlp --version && ffmpeg -version', $ytOut, $ytStatus);
	if ($ytStatus !== 0) {
		echo 'Error: yt-dlp and ffmpeg are required' . PHP_EOL;
	}

	exit;
}