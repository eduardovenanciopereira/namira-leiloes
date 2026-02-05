<?php
include("utils/liberar-api.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if ($access) {
        $sql = "SELECT is_admin FROM users WHERE id = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":uid" => $user_id
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $result_admin = (int) $result["is_admin"];
            if ($result_admin === 0) {
                $sql = "SELECT COUNT(*) AS total FROM favorites WHERE user_id = :uid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":uid" => $user_id
                ]);
                
                $result = $stmt->fetch();
                $quantity_favorites = $result ? $result["total"] : 0;
                
                $sql = "SELECT COUNT(*) AS total FROM admin_messages";
                $stmt = $pdo->query($sql);
                
                $result = $stmt->fetch();
                $quantity_messages = $result ? $result["total"] : 0;
                
                $json = [
                    "is_admin" => false,
                    "quantity_favorites" => $quantity_favorites,
                    "quantity_messages" => $quantity_messages
                ];
            }
            elseif ($result_admin === 1) {
                $sql = "SELECT COUNT(*) AS total FROM favorites WHERE user_id = :uid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":uid" => $user_id
                ]);
                
                $result = $stmt->fetch();
                $quantity_favorites = $result ? $result["total"] : 0;
                
                $sql = "SELECT COUNT(*) AS total FROM admin_messages";
                $stmt = $pdo->query($sql);
                
                $result = $stmt->fetch();
                $quantity_messages = $result ? $result["total"] : 0;
                
                $sql = "SELECT COUNT(*) AS total FROM contact_requests";
                $stmt = $pdo->query($sql);
                
                $result = $stmt->fetch();
                $quantity_contacts = $result ? $result["total"] : 0;
                
                $sql = "SELECT COUNT(*) AS total FROM blog_posts";
                $stmt = $pdo->query($sql);
                
                $result = $stmt->fetch();
                $quantity_blog_posts = $result ? $result["total"] : 0;
                
                $json = [
                    "is_admin" => true,
                    "quantity_favorites" => (string) $quantity_favorites,
                    "quantity_messages" => (string) $quantity_messages,
                    "quantity_contacts" => (string) $quantity_contacts,
                    "quantity_blog_posts" => (string) $quantity_blog_posts
                ];
            }
        }
        http_response_code(200);
        
        echo(json_encode($json));
        exit();
    } else {
        http_response_code(401);
    }
}
?>