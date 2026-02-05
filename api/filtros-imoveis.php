<?php
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    require("camada-01/lista-imoveis.php");
    
    $json  = listar_imoveis();
    $datas = json_decode($json, true);
    $datas = $datas["data"];
    
    $dados_ufs = [];
    $dados_cidades = [];
    $dados_bairros = [];
    $dados_modalidade_venda = [];
    
    foreach ($datas as $data) {
        in_array($data["uf"], $dados_ufs) ?: $dados_ufs[] = $data["uf"];
        in_array($data["cidade"], $dados_cidades[$data["uf"]] ?: $dados_cidades) ?: $dados_cidades[$data["uf"]][] = $data["cidade"];
        in_array($data["bairro"], $dados_bairros[$data["cidade"]] ?: $dados_bairros) ?: $dados_bairros[$data["cidade"]][] = $data["bairro"];
        in_array($data["modalidade_venda"], $dados_modalidade_venda) ?: $dados_modalidade_venda[] = $data["modalidade_venda"];
    }
    
    $response = [
        "ufs" => $dados_ufs,
        "cidades" => $dados_cidades,
        "bairros" => $dados_bairros,
        "modalidades_venda" => $dados_modalidade_venda
    ];
    
    $json = json_encode($response);
    
    echo($json);
}
?>