<?php
function cookies_mgl() {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://www.mgl.com.br/busca/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/camada-01/cookies/mgl-cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/camada-01/cookies/mgl-cookies.txt');
    
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36';
    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
    //$headers[] = 'Accept-Encoding: gzip, deflate, br, zstd';
    $headers[] = 'Upgrade-Insecure-Requests: 1';
    $headers[] = 'Sec-Ch-Ua: \"Chromium\";v=\"127\", \"Not)A;Brand\";v=\"99\", \"Microsoft Edge Simulate\";v=\"127\", \"Lemur\";v=\"127\"';
    $headers[] = 'Sec-Ch-Ua-Mobile: ?1';
    $headers[] = 'Sec-Ch-Ua-Full-Version: \"127.0.6533.144\"';
    $headers[] = 'Sec-Ch-Ua-Arch: \"\"';
    $headers[] = 'Sec-Ch-Ua-Platform: \"Android\"';
    $headers[] = 'Sec-Ch-Ua-Platform-Version: \"15.0.0\"';
    $headers[] = 'Sec-Ch-Ua-Model: \"24116RACCG\"';
    $headers[] = 'Sec-Ch-Ua-Bitness: \"\"';
    $headers[] = 'Sec-Ch-Ua-Full-Version-List: \"Chromium\";v=\"127.0.6533.144\", \"Not)A;Brand\";v=\"99.0.0.0\", \"Microsoft Edge Simulate\";v=\"127.0.6533.144\", \"Lemur\";v=\"127.0.6533.144\"';
    $headers[] = 'Accept-Language: pt-BR';
    $headers[] = 'Sec-Fetch-Site: none';
    $headers[] = 'Sec-Fetch-Mode: navigate';
    $headers[] = 'Sec-Fetch-Dest: document';
    $headers[] = 'Priority: u=0, i';

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    
    $html = html_entity_decode($result, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $html = preg_replace('/\s+/', ' ', $html);
    
    $p1 = explode('value="', $html);
    $p2 = explode('"', $p1[1]);
    $token = $p2[0];
    
    return $token;
}

$token = cookies_mgl();

$imageUrl = "https://www.mgl.com.br/imagens/1300x1300/" . $_ROUTE_PARAMS[0];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $imageUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/camada-01/cookies/mgl-cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/camada-01/cookies/mgl-cookies.txt');

/* HEADERS COERENTES PARA IMAGEM */
$headers = [];
$headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36';
$headers[] = '__rvt: ' . $token;
$headers[] = 'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8';
$headers[] = 'Accept-Language: pt-BR,pt;q=0.9';
$headers[] = 'Referer: https://www.mgl.com.br/';
$headers[] = 'Cache-Control: no-cache';
$headers[] = 'Pragma: no-cache';

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

/* RETORNO CORRETO PARA IMG */
if ($httpCode === 200 && strpos($contentType, 'image/') === 0) {
    header("Content-Type: {$contentType}");
    echo $result;
    exit;
}

http_response_code(404);
?>