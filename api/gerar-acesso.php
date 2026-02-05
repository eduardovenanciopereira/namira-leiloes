<?php
$config = parse_ini_file(__DIR__ . '/../.env');
require_once(__DIR__ . '/config/db.php');

function verifyToken($token, $storedHash) {
    global $config;
    $derived = hash_hmac('sha256', $token, $config["INTERNAL_SECRET"]);
    return password_verify($derived, $storedHash);
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $json = [
        "is_valid" => false
    ];

    $access_token = $_COOKIE['access_token'] ?? null;
    $user_id = $_COOKIE['uid'] ?? null;

    if ($access_token && $user_id) {
        $sql = "SELECT token_hash, expires_at FROM user_tokens WHERE token_type = 'access' AND user_id = :uid";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":uid" => $user_id
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            if (
                verifyToken($access_token, $result["token_hash"]) &&
                strtotime($result["expires_at"]) >= time()
            ) {
                $json["is_valid"] = true;
            }
        }
    }

    http_response_code($json["is_valid"] ? 200 : 401);
    echo json_encode($json);
    exit;
}
?>