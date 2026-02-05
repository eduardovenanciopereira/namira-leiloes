<?php
$config = parse_ini_file(__DIR__ . '/../../.env');
require_once(__DIR__ . '/../config/db.php');

function verifyToken($token, $storedHash) {
    global $config;
    $derived = hash_hmac('sha256', $token, $config["INTERNAL_SECRET"]);
    return password_verify($derived, $storedHash);
}

function generateHash($token) {
    global $config;
    $derived = hash_hmac('sha256', $token, $config["INTERNAL_SECRET"]);
    $hash = password_hash($derived, PASSWORD_DEFAULT);
    return $hash;
}

$access = false;

$refresh_token = $_COOKIE['refresh_token'] ?? null;
$access_token = $_COOKIE['access_token'] ?? null;
$user_id = $_COOKIE['uid'] ?? null;

if ($refresh_token && $access_token && $user_id) {
    $sql = "SELECT token_hash, expires_at FROM user_tokens WHERE token_type = 'refresh' AND user_id = :uid";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":uid" => $user_id
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        if (verifyToken($refresh_token, $result["token_hash"]) && strtotime($result["expires_at"]) >= time()) {
            $sql = "SELECT token_hash, expires_at FROM user_tokens WHERE token_type = 'access' AND user_id = :uid";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":uid" => $user_id
            ]);
        
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
            if($result) {
                if (verifyToken($access_token, $result["token_hash"]) && strtotime($result["expires_at"]) >= time()) {
                    $access = true;
                } else {
                    $access_token = bin2hex(random_bytes(32));
                    $access_hash = generateHash($access_token);
                    
                    $access_expires  = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                    
                    $sql_access = "UPDATE user_tokens 
                    SET token_hash = :token_hash, expires_at = :expires_at 
                    WHERE user_id = :user_id AND token_type = 'access'";
                    $stmt = $pdo->prepare($sql_access);
                    $stmt->execute([
                        ':user_id'    => $user_id,
                        ':token_hash' => $access_hash,
                        ':expires_at' => $access_expires
                    ]);
                    
                    // Cookies continuam iguais
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
                    
                    $access = true;
                }
            }
        }
    }
}
?>