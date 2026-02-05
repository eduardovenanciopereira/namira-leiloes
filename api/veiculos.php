<?php
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    require("camada-01/lista-veiculos.php");

    // ===============================
    // PARÂMETROS DA ROTA (BLINDADOS)
    // ===============================
    $categoria         = isset($_ROUTE_PARAMS[0]) ? urldecode($_ROUTE_PARAMS[0]) : null;
    $uf                = isset($_ROUTE_PARAMS[1]) ? urldecode($_ROUTE_PARAMS[1]) : null;
    $cidade            = isset($_ROUTE_PARAMS[2]) ? urldecode($_ROUTE_PARAMS[2]) : null;
    $status            = isset($_ROUTE_PARAMS[3]) ? urldecode($_ROUTE_PARAMS[3]) : null;
    $min_valor         = $_ROUTE_PARAMS[4] ?? null;
    $max_valor         = $_ROUTE_PARAMS[5] ?? null;
    $ano_modelo        = $_ROUTE_PARAMS[6] ?? null;
    $busca_titulo      = isset($_ROUTE_PARAMS[7]) ? urldecode($_ROUTE_PARAMS[7]) : null;
    $numero_pagina     = (int) ($_ROUTE_PARAMS[8] ?? 1);
    $quantidade_pagina = (int) ($_ROUTE_PARAMS[9] ?? 10);

    // ===============================
    // NORMALIZAÇÃO DOS FILTROS TEXTO
    // ===============================
    $categoria    = ($categoria !== null && strtolower($categoria) === 'all') ? null : $categoria;
    $cidade       = ($cidade !== null && strtolower($cidade) === 'all') ? null : $cidade;
    $uf           = ($uf !== null && strtolower($uf) === 'all') ? null : $uf;
    $status       = ($status !== null && strtolower($status) === 'all') ? null : $status;
    $busca_titulo = ($busca_titulo !== null && strtolower($busca_titulo) === 'all') ? null : $busca_titulo;

    // ===============================
    // NORMALIZAÇÃO NUMÉRICA
    // ===============================
    $min_valor  = ($min_valor && $min_valor !== "0") ? (int) $min_valor : null;
    $max_valor  = ($max_valor && $max_valor !== "0") ? (int) $max_valor : null;
    $ano_modelo = ($ano_modelo && $ano_modelo !== "0") ? (int) $ano_modelo : null;

    // ===============================
    // BUSCA DOS DADOS
    // ===============================
    $json  = listar_veiculos();
    $datas = json_decode($json, true)['data'] ?? [];

    // ===============================
    // FILTRO DE TEXTO
    // ===============================
    $filtro_texto = array_filter($datas, function ($item) use (
        $categoria, $cidade, $uf, $status, $busca_titulo
    ) {

        if ($categoria !== null && strtolower($item['categoria'] ?? '') !== strtolower($categoria)) return false;
        if ($cidade !== null && strtolower($item['cidade'] ?? '') !== strtolower($cidade)) return false;

        if ($uf !== null) {
            $uf_item = $item['uf'] ?? null;
            if ($uf_item === null || strtoupper($uf_item) !== strtoupper($uf)) return false;
        }

        if ($status !== null && strtolower($item['status'] ?? '') !== strtolower($status)) return false;

        if ($busca_titulo !== null) {
            $titulo = strtolower((string) ($item['titulo'] ?? ''));
            if (stripos($titulo, strtolower($busca_titulo)) === false) return false;
        }

        return true;
    });

    // ===============================
    // FILTRO NUMÉRICO
    // ===============================
    $data_filter = array_filter($filtro_texto, function ($item) use (
        $min_valor, $max_valor, $ano_modelo
    ) {

        $valor_atual = isset($item['valor_atual']) ? (int) $item['valor_atual'] : null;
        $modelo      = isset($item['ano_modelo']) ? (int) $item['ano_modelo'] : null;

        if ($min_valor !== null && ($valor_atual === null || $valor_atual < $min_valor)) return false;
        if ($max_valor !== null && ($valor_atual === null || $valor_atual > $max_valor)) return false;
        if ($ano_modelo !== null && ($modelo === null || $modelo !== $ano_modelo)) return false;

        return true;
    });

    // ===============================
    // PAGINAÇÃO
    // ===============================
    $data_filter  = array_values($data_filter);
    $total_itens  = count($data_filter);
    $inicio_itens = ($numero_pagina - 1) * $quantidade_pagina;
    $all_data     = array_slice($data_filter, $inicio_itens, $quantidade_pagina);

    // ===============================
    // RESPOSTA FINAL
    // ===============================
    echo json_encode([
        'status'  => 'success',
        'paginas' => ceil($total_itens / $quantidade_pagina),
        'data'    => $all_data
    ]);
}
?>