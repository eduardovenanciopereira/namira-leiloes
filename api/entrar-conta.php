<?php
$config = parse_ini_file(__DIR__ . '/../.env');
require_once(__DIR__ . '/config/db.php');

function generateHash($token) {
    global $config;
    $derived = hash_hmac('sha256', $token, $config["INTERNAL_SECRET"]);
    $hash = password_hash($derived, PASSWORD_DEFAULT);
    return $hash;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rawBody = file_get_contents("php://input");
    $data = json_decode($rawBody, true);

    $email = $data["email_account"] ?? null;
    $password = $data["password_account"] ?? null;

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT id, email, password FROM users WHERE email = :email LIMIT 1;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":email" => $email
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $user_id = $result["id"];
            $password_hash = $result["password"];
            
            if (password_verify($password, $password_hash)) {
                $refresh_token = bin2hex(random_bytes(32));
                $refresh_hash = generateHash($refresh_token);
                
                $access_token = bin2hex(random_bytes(32));
                $access_hash = generateHash($access_token);
                
                $refresh_expires = date('Y-m-d H:i:s', strtotime('+7 days'));
                $access_expires  = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                
                // Atualiza token refresh
                $sql_refresh = "UPDATE user_tokens
                SET token_hash = :token_hash, expires_at = :expires_at 
                WHERE user_id = :user_id AND token_type = 'refresh'";
                $stmt = $pdo->prepare($sql_refresh);
                $stmt->execute([
                    ':user_id'    => $user_id,
                    ':token_hash' => $refresh_hash,
                    ':expires_at' => $refresh_expires
                ]);
                
                // Atualiza token access
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
                
                setcookie(
                    "refresh_token",
                    $refresh_token,
                    [
                        'expires' => time() + 7*24*60*60,
                        'path' => '/',
                        'secure' => false,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]
                );
                
                setcookie('uid', $user_id, [
                    'secure' => false,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
                
                $json = [
                    "success" => true,
                    "message" => "Login realizado com sucesso! Sua identidade foi verificada e o acesso à sua conta foi liberado. Agora você pode utilizar todos os recursos disponíveis na plataforma com total segurança."
                ];
            } else {
                $json = [
                    "success" => false,
                    "message" => "A senha informada está incorreta. Verifique se foi digitada corretamente, respeitando letras maiúsculas e minúsculas, e tente novamente."
                ];
            }
        } else {
            $json = [
                "success" => false,
                "message" => "O e-mail informado não foi encontrado em nosso sistema. Verifique se ele foi digitado corretamente e tente novamente."
            ];
        }
    }
    http_response_code($json["success"] ? 200 : 400);
    echo json_encode($json);
    exit();
}
?>