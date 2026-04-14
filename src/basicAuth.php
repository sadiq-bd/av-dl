<?php

require_once __DIR__ . '/config.php';

function unauthenticated() {
    http_response_code(401);
    header('www-authenticate: basic');
    exit;
}

$allHeaders = array_change_key_case(getallheaders(), CASE_LOWER);

if (empty($allHeaders['authorization'])) {
    unauthenticated();
}

$authenticated = false;

foreach ($config->basicAuth as $key => $val) {
    $auth = explode(' ', $allHeaders['authorization'], 2)[1];
    if ($auth && $auth === base64_encode($key . ':' . $val)) {
        $authenticated = true;
        break;
    } 
}

if (!$authenticated) {
    unauthenticated();
}

