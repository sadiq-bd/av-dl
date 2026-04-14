<?php

$ytdlp = exec('yt-dlp --version', $ytOut, $ytStatus);
if ($ytStatus !== 0) {
    echo 'Error: yt-dlp not installed';
    exit;
}

$ffmpeg = exec('ffmpeg -version', $ffmpegOut, $ffmpegStatus);
if ($ffmpegStatus !== 0) {
    echo 'Error: ffmpeg not installed';
    exit;
}

