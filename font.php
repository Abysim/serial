<?php
header("Cache-Control: private, max-age=5");
header("Access-Control-Allow-Origin: *");
//header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");

$origin = trim($_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '', '/');

$font = intval($_GET['id'] ?? 0);
$response = null;

if (
    $font > 0
    && (
        $origin == "https://www.podcastics.com"
        || $origin == "https://serial.in.ua"
        || $origin == "https://www.serial.in.ua"
    ) && $_SERVER['HTTP_HOST'] == 'css.serial.in.ua'
) {
    $ch = curl_init();
    curl_setopt(
        $ch,
        CURLOPT_URL,
        'https://rentafont.com/web_fonts/webfontcss/MjMzNTI2b3JkZXIyMjg3ODU=?fonts='
        . $font
        . '&formats=woff2&by_style=1&by_id=0'
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $headers = [
        'Accept: ' . $_SERVER['HTTP_ACCEPT'],
        'Accept-Encoding: identity',
        'Accept-Language: ' . $_SERVER['HTTP_ACCEPT_LANGUAGE'],
        'Host: rentafont.com',
        'Origin: ' . $origin,
        'Referer: ' . $_SERVER['HTTP_REFERER'],
        'User-Agent: ' . $_SERVER['HTTP_USER_AGENT'],
    ];

  //  file_put_contents('log.txt', json_encode($headers) ."\n", FILE_APPEND);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

  //  file_put_contents('log.txt', $response ."\n", FILE_APPEND);
}

if (is_string($response) && ($start = strpos($response, ";base64,")) && ($end = strpos($response, ") format('woff2')"))) {
    $offset = $start + 8;
//    header("Content-Type: font/woff2");
    echo base64_decode(substr($response,  $offset, $end - $offset));
} else {
    header("HTTP/2 402 Payment Required");
}
