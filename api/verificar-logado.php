<?php
$config = parse_ini_file(__DIR__ . '/../.env');
require_once(__DIR__ . '/config/db.php');

function verifyToken($token, $storedHash) {
    global $config;

    $derived = hash_hmac('sha256', $token, $config["INTERNAL_SECRET"]);

    return password_verify($derived, $storedHash);
}

function generateHash($token) {
    global $config;
    $derived = hash_hmac('sha256', $token, $config["INTERNAL_SECRET"]);
    return password_hash($derived, PASSWORD_DEFAULT);
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $json = [
        "is_logged" => false
    ];
    
    $refresh_token = $_COOKIE['refresh_token'] ?? null;
    $user_id = $_COOKIE['uid'] ?? null;
    
    if ($refresh_token && $user_id) {
        $sql = "SELECT token_hash, expires_at FROM user_tokens WHERE token_type = 'refresh' AND user_id = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":uid" => $user_id
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $token_hash_db = $result["token_hash"];
            $expires_at = $result["expires_at"];
            if (verifyToken($refresh_token, $token_hash_db) && strtotime($expires_at) >= time()) {
                $access_token = bin2hex(random_bytes(32));
                $access_hash = generateHash($access_token);
                
                $access_expires  = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                
                $sql_access = "UPDATE user_tokens SET token_hash = :token_hash, expires_at = :expires_at WHERE user_id = :user_id AND token_type = 'access'";
                $stmt = $pdo->prepare($sql_access);
                $stmt->execute([
                    ':user_id'    => $user_id,
                    ':token_hash' => $access_hash,
                    ':expires_at' => $access_expires
                ]);
                
                setcookie(
                    "access_token",
                    $access_token,
                    [
                        'expires' => time() + 300,
                        'path' => '/',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]
                );
                
                $json["is_logged"] = true;
            }
        }
    }
    
    http_response_code($json["is_logged"] ? 200 : 401);
    echo json_encode($json);
    exit();
}
?>