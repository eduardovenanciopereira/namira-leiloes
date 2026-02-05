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

    $name = $data["name_person"] ?? null;
    $email = $data["email_account"] ?? null;
    $password = $data["password_account"] ?? null;
    $phone = $data["number_phone"] ?? null;

    if (!empty($name) && !empty($email) && !empty($password) && !empty($phone)) {
        $sql = "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, name_person VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, number_phone VARCHAR(20) NOT NULL UNIQUE, is_admin TINYINT(1) NOT NULL DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $pdo->exec($sql);
        $sql = "CREATE TABLE IF NOT EXISTS user_tokens (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, token_hash CHAR(255) NOT NULL UNIQUE, token_type ENUM('access','refresh') NOT NULL, expires_at DATETIME NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $pdo->exec($sql);
        if (preg_match("/^[A-Za-zÀ-ÿ]+([ '-][A-Za-zÀ-ÿ]+)*$/", $name)) {
            if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
                if (preg_match("/^\+?\d{0,3}\s?\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/", $phone)) {
                    if (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $password)) {
                        $sql = "SELECT * FROM users WHERE email = :email";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':email' => $email
                        ]);
                        if (!$stmt->fetch()) {
                            $sql = "SELECT * FROM users WHERE number_phone = :number_phone";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute([
                                ':number_phone' => $phone
                            ]);
                            if (!$stmt->fetch()) {
                                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                                $sql = "INSERT INTO users (name_person, email, password, number_phone, is_admin) VALUES (:name_person, :email, :password, :number_phone, :is_admin);";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([
                                    ':name_person' => $name,
                                    ':email'       => $email,
                                    ':password'    => $password_hash,
                                    ':number_phone'=> $phone,
                                    ':is_admin' => 0
                                ]);
                                
                                $lastId = $pdo->lastInsertId();
                                
                                $refresh_token = bin2hex(random_bytes(32));
                                $refresh_hash = generateHash($refresh_token);
                                
                                $access_token = bin2hex(random_bytes(32));
                                $access_hash = generateHash($access_token);
                                
                                $refresh_expires = date('Y-m-d H:i:s', strtotime('+7 days'));
                                $access_expires  = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                                
                                $sql_refresh = "INSERT INTO user_tokens (user_id, token_hash, token_type, expires_at) VALUES (:user_id, :token_hash, :token_type, :expires_at)";
                                $stmt = $pdo->prepare($sql_refresh);
                                $stmt->execute([
                                    ':user_id'    => $lastId,
                                    ':token_hash' => $refresh_hash,
                                    ':token_type' => 'refresh',
                                    ':expires_at' => $refresh_expires
                                ]);
                                $sql_access = "INSERT INTO user_tokens (user_id, token_hash, token_type, expires_at) VALUES (:user_id, :token_hash, :token_type, :expires_at)";
                                $stmt = $pdo->prepare($sql_access);
                                $stmt->execute([
                                    ':user_id'    => $lastId,
                                    ':token_hash' => $access_hash,
                                    ':token_type' => 'access',
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
                                    "message" => "Parabéns! Seu cadastro foi realizado com sucesso e agora você tem acesso completo a todos os recursos disponíveis na nossa plataforma."
                                ];
                            } else {
                                $json = [
                                    "success" => false,
                                    "message" => "Já existe uma conta cadastrada com este número de telefone. Faça login ou use outro número."
                                ];
                            }
                        } else {
                            $json = [
                                "success" => false,
                                "message" => "Identificamos que este e-mail já está vinculado a uma conta existente. Tente fazer login ou utilize um outro e-mail para criar uma nova conta."
                            ];
                        }
                    } else {
                        $json = [
                            "success" => false,
                            "message" => "A senha deve ter pelo menos 8 caracteres, incluindo letras maiúsculas, minúsculas, números e um caractere especial."
                        ];
                    }
                } else {
                    $json = [
                        "success" => false,
                        "message" => "Parece que o número que você digitou não é válido. Verifique o DDD e os dígitos e tente novamente."
                    ];
                }
            } else {
                $json = [
                    "success" => false,
                    "message" => "Parece que o e-mail digitado não é válido. Verifique o formato (ex: usuario@dominio.com) e tente novamente."
                ];
            }
        } else {
            $json = [
                "success" => false,
                "message" => "Parece que o nome digitado não é válido. Use apenas letras e espaços, sem números ou caracteres especiais."
            ];
        }
    }
    http_response_code($json["success"] ? 200 : 400);
    echo json_encode($json);
    exit();
}
?>