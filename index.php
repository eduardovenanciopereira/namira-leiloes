<?php
// =============== Arquivo de rotas ===============
// =============== Por: Eduardo Venancio ===============



// Remover cache do navegador e sempre atualizar o código.
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Pegamos a URL acessada
$ago_url = rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");

// Criamos uma lista que divide a URL por barras
$parts = explode("/", $ago_url);

// Verificamos se a URL pertence à API.
// Se o segundo índice [1] for "api":
// - A rota base será formada por /api + nome do recurso.
// - Os demais segmentos serão tratados como parâmetros.
// Caso contrário:
// - A rota será tratada como interface (front-end).
$params = [];

if (($parts[1] ?? null) === "api") {
    $part1 = "/api";
    $part2 = "/" . ($parts[2] ?? "");
    
    $route_base = rtrim($part1 . $part2, "/");
    // Apartir do quarto [3] índice, criamos uma nova lista, porque esses dados serão parâmetros.
    $params = array_slice($parts, 3);
} else {
    $route_base = "/" . ($parts[1] ?? "");
    $route_base = rtrim($route_base, "/");
    $route_base = $route_base ?: "/";
    // Apartir do terceiro [2] índice, faremos a mesma coisa explicada no comentário anterior.
    $params = array_slice($parts, 2);
}

// Pegamos o diretório que o arquivo "index" está e criamos uma lista de rotas.
$path_api = __DIR__ . "/api/";
$path_web = __DIR__ . "/src/";

$routes = array(
    // Rotas back-end / API
    "/api/imoveis" => $path_api . "imoveis.php",
    "/api/veiculos" => $path_api . "veiculos.php",
    "/api/filtros-imoveis" => $path_api . "filtros-imoveis.php",
    "/api/filtros-veiculos" => $path_api . "filtros-veiculos.php",
    "/api/imagem-mgl" => $path_api . "imagem-mgl.php",
    "/api/entrar" => $path_api . "entrar-conta.php",
    "/api/registro" => $path_api . "registro-conta.php",
    "/api/gerar-acesso" => $path_api . "gerar-acesso.php",
    "/api/logado" => $path_api . "verificar-logado.php",
    "/api/detalhes" => $path_api . "detalhes-conta.php",
    // Rotas front-end / Interface
    "/" => $path_web . "home.html",
    "/imoveis" => $path_web . "imoveis.html",
    "/veiculos" => $path_web . "veiculos.html",
    "/entrar" => $path_web . "entrar.html",
    "/registro" => $path_web . "registro.html"
);

if (isset($routes[$route_base])) {
    $file = $routes[$route_base];
    
    // Se caso o arquivo que deve ser aberto conforme resultado da lista "routes" tiver extensão ".html", devemos adicionar um header com o tipo de arquivo de resposta.
    if (pathinfo($file, PATHINFO_EXTENSION) === "html") {
        header("Content-Type: text/html; charset=UTF-8");
        $html = file_get_contents($file);
        $script = '
<script type="text/javascript" charset="utf-8">
window.ROUTE = {
    params: ' . json_encode($params) . '
};
</script>';
        if ($params) {
            echo str_replace("<!-- ROUTE_DATA -->", $script, $html);
        } else {
            echo str_replace("<!-- ROUTE_DATA -->", "", $html);
        }
        exit;
    }
    
    header("Content-Type: application/json; charset=UTF-8");
    // Podemos criar os parâmetros, já resolvemos essa questão da linha 14 a 29.
    $_ROUTE_PARAMS = $params;
    require($file);
    exit();
} else {
    header("Content-Type: text/html; charset=UTF-8");
    http_response_code(404);
    readfile($path_web . "404.html");
}

?>