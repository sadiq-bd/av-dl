<?php

require_once __DIR__ . '/config.php';

if (empty($_GET['file'])) {
	http_response_code(404);
	exit;
}

$file = rtrim(CONFIG['downloadDir'], '/') . '/' . basename($_GET['file']);

if (!file_exists($file) && is_dir($file)) {
	http_response_code(404);
	exit;
}

$size = filesize($file);
$start = 0;
$end = $size - 1;

// Check for Range header
if (isset($_SERVER['HTTP_RANGE'])) {
	if (preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
		$start = intval($matches[1]);
		if (!empty($matches[2])) {
			$end = intval($matches[2]);
		}
	}

	http_response_code(206);
	header("Content-Range: bytes $start-$end/$size");
}

$length = $end - $start + 1;

header("Content-Type: application/octet-stream");
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header("Accept-Ranges: bytes");
header("Content-Length: $length");

$fp = fopen($file, "rb");
fseek($fp, $start);

$buffer = 8192;

while (!feof($fp) && ($pos = ftell($fp)) <= $end) {
	if ($pos + $buffer > $end) {
		$buffer = $end - $pos + 1;
	}
	echo fread($fp, $buffer);
	flush();
}

fclose($fp);

if (!isset($_GET['keep'])) {
	unlink($file);
}