<?php
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    require("camada-01/lista-veiculos.php");

    $json  = listar_veiculos();
    $datas = json_decode($json, true);
    $datas = $datas["data"] ?? [];

    $categorias = [];
    $ufs = []; // categorias → lista de UFs
    $cidades = [];

    $status_ufs = [];      // UF → status → true (estrutura padronizada)
    $status_cidades = [];  // UF → cidade → lista de status
    $anos_ufs = [];        // UF → status → lista de anos
    $anos_cidades = [];    // UF → cidade → status → lista de anos

    foreach ($datas as $data) {

        $categoria  = $data["categoria"] ?? null;
        $uf         = $data["uf"] ?? null;
        $cidade     = $data["cidade"] ?? null;
        $stat       = $data["status"] ?? null;
        $ano        = $data["ano_modelo"] ?? null;

        if (!$categoria || !$uf) continue;

        // -------------------------------
        // Categorias
        // -------------------------------
        if (!in_array($categoria, $categorias)) {
            $categorias[] = $categoria;
        }

        // -------------------------------
        // UFs por Categoria
        // -------------------------------
        if (!isset($ufs[$categoria])) $ufs[$categoria] = [];
        if (!in_array($uf, $ufs[$categoria])) {
            $ufs[$categoria][] = $uf;
        }

        // -------------------------------
        // Cidades por UF
        // -------------------------------
        if (!isset($cidades[$uf])) $cidades[$uf] = [];
        if ($cidade && !in_array($cidade, $cidades[$uf])) {
            $cidades[$uf][] = $cidade;
        }

        // -------------------------------
        // STATUS
        // -------------------------------
        // Status por UF (padronizado, chave UF → lista de status)
        if ($stat) {
            if (!isset($status_ufs[$uf])) $status_ufs[$uf] = [];
            if (!in_array($stat, $status_ufs[$uf])) {
                $status_ufs[$uf][] = $stat;
            }
        }

        // Status refinado por cidade
        if ($cidade) {
            if (!isset($status_cidades[$uf])) $status_cidades[$uf] = [];
            if (!isset($status_cidades[$uf][$cidade])) $status_cidades[$uf][$cidade] = [];
            if ($stat && !in_array($stat, $status_cidades[$uf][$cidade])) {
                $status_cidades[$uf][$cidade][] = $stat;
            }
        }

        // -------------------------------
        // ANOS MODELO
        // -------------------------------
        // Anos por UF → filho = status
        if (!isset($anos_ufs[$uf])) $anos_ufs[$uf] = [];
        if ($stat && !isset($anos_ufs[$uf][$stat])) $anos_ufs[$uf][$stat] = [];
        if ($ano && $stat && !in_array($ano, $anos_ufs[$uf][$stat])) {
            $anos_ufs[$uf][$stat][] = $ano;
        }

        // Anos por cidade → filho = status
        if ($cidade) {
            if (!isset($anos_cidades[$uf])) $anos_cidades[$uf] = [];
            if (!isset($anos_cidades[$uf][$cidade])) $anos_cidades[$uf][$cidade] = [];
            if ($stat && !isset($anos_cidades[$uf][$cidade][$stat])) $anos_cidades[$uf][$cidade][$stat] = [];
            if ($ano && $stat && !in_array($ano, $anos_cidades[$uf][$cidade][$stat])) {
                $anos_cidades[$uf][$cidade][$stat][] = $ano;
            }
        }
    }

    $response = [
        "categorias" => $categorias,
        "ufs" => $ufs,                 // categorias → lista de UFs
        "cidades" => $cidades,         // UF → lista de cidades
        "status_ufs" => $status_ufs,   // UF → lista de status
        "status_cidades" => $status_cidades, // UF → cidade → lista de status
        "anos_ufs" => $anos_ufs,       // UF → status → lista de anos
        "anos_cidades" => $anos_cidades // UF → cidade → status → lista de anos
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>