<?php

require_once __DIR__ . '/config.php';

class DWNLD_MODES
{
	static $DIRECT = 0;
	static $SERVER = 1;
	static $BOTH = 2;
}

if (!function_exists('getallheaders')) {
	function getallheaders()
	{
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}

function getRoute()
{
	return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

function genYtMp4Cmd(string $url, int $height = 0)
{
	$config = CONFIG['ytDlpOpts'] ? ' ' . CONFIG['ytDlpOpts'] : '';
	$url = escapeshellarg($url);
	$ql1 = $height ? "[height=$height]" : '';
	$ql2 = $height ? "[height<=$height]" : '';
	return "yt-dlp -f \"bestvideo{$ql1}[vcodec^=avc1][ext=mp4]+bestaudio[ext=m4a]/bestvideo[ext=mp4]+bestaudio[ext=m4a]/best{$ql2}\"{$config} --merge-output-format mp4 $url";
}


function genYtMp3Cmd(string $url)
{
	$config = CONFIG['ytDlpOpts'] ? ' ' . CONFIG['ytDlpOpts'] : '';
	$url = escapeshellarg($url);
	return "yt-dlp -x --audio-format mp3 --audio-quality 0 --embed-thumbnail{$config} --embed-metadata $url";
}

function grabFileName(string $pipeline)
{
	$downloadFile = '';
	if (stripos($pipeline, '[Merger] Merging formats into ') !== false) {
		$downloadFile = trim(trim(preg_replace(['#\[Merger\]\sMerging\sformats\sinto(\s)?#i', "#\n#"], '', $pipeline)), '"');
	}
	if (!$downloadFile && stripos($pipeline, 'has already been downloaded') !== false) {
		$downloadFile = trim(preg_replace(['#\[download\](\s)?#i', '#(\s)?has\salready\sbeen\sdownloaded#i', "#\n#"], '', $pipeline));
	}
	if (!$downloadFile && stripos($pipeline, '[ExtractAudio] Destination:') !== false) {
		$downloadFile = trim(preg_replace(['#\[ExtractAudio\]\sDestination:(\s)?#i', "#\n#"], '', $pipeline));
	}

	return $downloadFile;
}