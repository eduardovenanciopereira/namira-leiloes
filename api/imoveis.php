<?php
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    require("camada-01/lista-imoveis.php");

    $uf               = urldecode($_ROUTE_PARAMS[0]) ?? null;
    $cidade           = urldecode($_ROUTE_PARAMS[1]) ?? null;
    $bairro           = urldecode($_ROUTE_PARAMS[2]) ?? null;
    $min_avaliacao    = $_ROUTE_PARAMS[3] ?? null;
    $max_avaliacao    = $_ROUTE_PARAMS[4] ?? null;
    $min_porcentagem  = $_ROUTE_PARAMS[5] ?? null;
    $max_porcentagem  = $_ROUTE_PARAMS[6] ?? null;
    $modalidade_venda = urldecode($_ROUTE_PARAMS[7]) ?? null;
    $numero_pagina    = (int) ($_ROUTE_PARAMS[8] ?? 1);
    $quantidade_pagina = (int) ($_ROUTE_PARAMS[9] ?? 10);

    // Transformar 'all' em null para filtros de texto
    $uf               = ($uf !== null && strtolower($uf) === 'all') ? null : $uf;
    $cidade           = ($cidade !== null && strtolower($cidade) === 'all') ? null : $cidade;
    $bairro           = ($bairro !== null && strtolower($bairro) === 'all') ? null : $bairro;
    $modalidade_venda = ($modalidade_venda !== null && strtolower($modalidade_venda) === 'all') ? null : $modalidade_venda;

    // Transformar "0" ou string vazia em null, caso contrário converter para inteiro
    $min_avaliacao    = ($min_avaliacao !== null && $min_avaliacao !== "" && $min_avaliacao !== "0") ? (int)$min_avaliacao : null;
    $max_avaliacao    = ($max_avaliacao !== null && $max_avaliacao !== "" && $max_avaliacao !== "0") ? (int)$max_avaliacao : null;
    $min_porcentagem  = ($min_porcentagem !== null && $min_porcentagem !== "" && $min_porcentagem !== "0") ? (int)$min_porcentagem : null;
    $max_porcentagem  = ($max_porcentagem !== null && $max_porcentagem !== "" && $max_porcentagem !== "0") ? (int)$max_porcentagem : null;

    $json  = listar_imoveis();
    $datas = json_decode($json, true)['data'];

    // Primeiro array_filter: filtros de texto
    $filtro_texto = array_filter($datas, function ($item) use ($uf, $cidade, $bairro, $modalidade_venda) {
        if ($uf !== null && strtoupper($item['uf']) !== strtoupper($uf)) return false;
        if ($cidade !== null && strtolower($item['cidade']) !== strtolower($cidade)) return false;
        if ($bairro !== null && strtolower($item['bairro']) !== strtolower($bairro)) return false;
        if ($modalidade_venda !== null && strtolower($item['modalidade_venda']) !== strtolower($modalidade_venda)) return false;
        return true;
    });
    
    // Segundo array_filter: filtros numéricos (valor_venda e desconto_percentual)
    $data_filter = array_filter($filtro_texto, function ($item) use ($min_avaliacao, $max_avaliacao, $min_porcentagem, $max_porcentagem) {
        $valor_venda = (int) $item['valor_venda'];
        $desconto_percentual = (int) $item['desconto_percentual'];
    
        if ($min_avaliacao !== null && $valor_venda < $min_avaliacao) return false;
        if ($max_avaliacao !== null && $valor_venda > $max_avaliacao) return false;
        if ($min_porcentagem !== null && $desconto_percentual < $min_porcentagem) return false;
        if ($max_porcentagem !== null && $desconto_percentual > $max_porcentagem) return false;
    
        return true;
    });

    $data_filter = array_values($data_filter); // Reindex

    $total_itens = count($data_filter);
    $inicio_itens = ($numero_pagina - 1) * $quantidade_pagina;

    $all_data = array_slice($data_filter, $inicio_itens, $quantidade_pagina);

    $response = [
        'status' => 'success',
        'paginas'=> ceil($total_itens / $quantidade_pagina),
        'data'   => $all_data
    ];

    echo json_encode($response);
}
?>