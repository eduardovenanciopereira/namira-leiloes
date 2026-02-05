<?php

// =============== Lista de veículos ===============
// =============== Por: Eduardo Venancio ===============



// ========================================================

function normalizar_mgl(string $json_curl, string $categoria): array
{
    $dados = json_decode($json_curl, true);

    if (json_last_error() !== JSON_ERROR_NONE || empty($dados['Lotes'])) {
        return [];
    }

    $resultado = [];

    foreach ($dados['Lotes'] as $lote) {

        // Valor atual e status em tempo real
        $valor_atual = null;
        $status_lote = null;

        if (!empty($lote['GetLoteRealTime'][0])) {
            $rt = $lote['GetLoteRealTime'][0];
            $valor_atual = $rt['Vencedor']['Valor'] ?? 0;
            $status_lote = $rt['StatusLote'] ?? null;
        }

        // Imagens
        $imagens = [];
        if (!empty($lote['Fotos'])) {
            foreach ($lote['Fotos'] as $foto) {
                if (!empty($foto['Foto'])) {
                    $imagens[] = 'https://www.mgl.com.br/imagens/1300x1300/' . $foto['Foto'];
                }
            }
        }

        // Ano (extraído do texto)
        $ano_fabricacao = null;
        $ano_modelo = null;

        if (!empty($lote['Lote'])) {
            if (preg_match('/\b(\d{4})(?:\/(\d{4}))?\b/', $lote['Lote'], $m)) {
                $ano_fabricacao = $m[1] ?? null;
                $ano_modelo = $m[2] ?? $m[1] ?? null;
            }
        }

        $resultado[] = [
            'id_externo' => !empty($lote['ID_Leiloes_Lote']) ? $lote['ID_Leiloes_Lote'] : null,
            'titulo' => !empty($lote['Lote'])
                ? strtoupper($lote['Lote'])
                : null,
        
            'categoria' => $categoria ?: null,
        
            'cidade' => !empty($lote['Cidade'])
                ? ucfirst($lote['Cidade'])
                : null,
        
            'uf' => !empty($lote['UF'])
                ? strtoupper($lote['UF'])
                : null,
        
            'valor_inicial' => isset($valor_atual)
                ? (int) $valor_atual
                : null,
        
            'valor_atual' => isset($lote['ValorInicialPrimeiraPraca'])
                ? (int) $lote['ValorInicialPrimeiraPraca']
                : null,
        
            'status' => isset($lote['IsEncerrado'])
                ? ($lote['IsEncerrado'] ? 'Encerrado' : 'Aberto')
                : null,
        
            'ano_fabricacao' => (int) $ano_fabricacao ?? null,
            'ano_modelo'     => (int) $ano_modelo ?? null,
        
            'link_veiculo' => !empty($lote['ID_Leiloes_Lote'])
                ? 'https://www.mgl.com.br/lote/veiculos-corporativo/' . $lote['ID_Leiloes_Lote']
                : null,
        
            'imagens' => !empty($imagens)
                ? $imagens
                : []
    ];
    }
    
    unset($dados, $json_curl);

    return $resultado;
}

function normalizar_sodresantoro(string $json_curl, string $categoria): array
{
    $dados = json_decode($json_curl, true);

    if (json_last_error() !== JSON_ERROR_NONE || empty($dados['results'])) {
        return [];
    }

    $resultado = [];

    foreach ($dados['results'] as $item) {

        // Cidade e UF
        $cidade = null;
        $uf = null;

        if (!empty($item['lot_location'])) {
            if (preg_match('/^(.*?)\/([a-z]{2})$/i', trim($item['lot_location']), $m)) {
                $cidade = trim($m[1]);
                $uf = strtoupper($m[2]);
            } else {
                $cidade = $item['lot_location'];
            }
        }
        
        $imagens = $item['lot_pictures'];
        $chave = array_search('https://photos.sodresantoro.', $imagens);
        if ($chave !== false) {
            unset($imagens[$chave]);
        }

        $resultado[] = [
            'id_externo' => (!empty($item['auction_id']) && !empty($item['lot_id'])) ? $item['auction_id'] . '-' . $item['lot_id'] : null,
            'titulo' => !empty($item['lot_title'])
                ? strtoupper($item['lot_title'])
                : null,
        
            'categoria' => $categoria ?: null,
        
            'cidade' => !empty($cidade)
                ? ucfirst($cidade)
                : null,
        
            'uf' => !empty($uf)
                ? strtoupper($uf)
                : null,
        
            'valor_inicial' => isset($item['bid_initial'])
                ? (int) $item['bid_initial']
                : null,
        
            'valor_atual' => isset($item['bid_actual'])
                ? (int) $item['bid_actual']
                : null,
        
            'status' => !empty($item['lot_status'])
                ? ucwords($item['lot_status'])
                : null,
        
            'ano_fabricacao' => (int) $item['lot_year_manufacture'] ?? null,
            'ano_modelo'     => (int) $item['lot_year_model'] ?? null,
        
            'link_veiculo' => (!empty($item['auction_id']) && !empty($item['lot_id']))
                ? 'https://leilao.sodresantoro.com.br/leilao/' . $item['auction_id'] . '/lote/' . $item['lot_id'] . '/'
                : null,
        
            'imagens' => !empty($imagens)
                ? $imagens
                : []
        ];
    }
    
    unset($dados, $json_curl);

    return $resultado;
}

function headers_mgl(string $rvtToken): array
{
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36';
    $headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
    //$headers[] = 'Accept-Encoding: gzip, deflate, br, zstd';
    $headers[] = 'Sec-Ch-Ua: \"Chromium\";v=\"127\", \"Not)A;Brand\";v=\"99\", \"Microsoft Edge Simulate\";v=\"127\", \"Lemur\";v=\"127\"';
    $headers[] = 'Accept-Language: pt-BR';
    $headers[] = 'Sec-Ch-Ua-Mobile: ?1';
    $headers[] = 'Sec-Ch-Ua-Arch: \"\"';
    $headers[] = 'Content-Type: application/json; charset=UTF-8';
    $headers[] = 'Sec-Ch-Ua-Full-Version: \"127.0.6533.144\"';
    $headers[] = 'Sec-Ch-Ua-Platform-Version: \"15.0.0\"';
    $headers[] = 'X-Requested-With: XMLHttpRequest';
    $headers[] = '__rvt: ' . $rvtToken;
    $headers[] = 'Sec-Ch-Ua-Full-Version-List: \"Chromium\";v=\"127.0.6533.144\", \"Not)A;Brand\";v=\"99.0.0.0\", \"Microsoft Edge Simulate\";v=\"127.0.6533.144\", \"Lemur\";v=\"127.0.6533.144\"';
    $headers[] = 'Sec-Ch-Ua-Bitness: \"\"';
    $headers[] = 'Sec-Ch-Ua-Model: \"24116RACCG\"';
    $headers[] = 'Sec-Ch-Ua-Platform: \"Android\"';
    $headers[] = 'Origin: https://www.mgl.com.br';
    $headers[] = 'Sec-Fetch-Site: same-origin';
    $headers[] = 'Sec-Fetch-Mode: cors';
    $headers[] = 'Sec-Fetch-Dest: empty';
    $headers[] = 'Referer: https://www.mgl.com.br/';
    $headers[] = 'Priority: u=1, i';
    return $headers;
}

function cookies_mgl() {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://www.mgl.com.br/busca/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies/mgl-cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies/mgl-cookies.txt');
    
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36';
    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
    //$headers[] = 'Accept-Encoding: gzip, deflate, br, zstd';
    $headers[] = 'Upgrade-Insecure-Requests: 1';
    $headers[] = 'Sec-Ch-Ua: \"Chromium\";v=\"127\", \"Not)A;Brand\";v=\"99\", \"Microsoft Edge Simulate\";v=\"127\", \"Lemur\";v=\"127\"';
    $headers[] = 'Sec-Ch-Ua-Mobile: ?1';
    $headers[] = 'Sec-Ch-Ua-Full-Version: \"127.0.6533.144\"';
    $headers[] = 'Sec-Ch-Ua-Arch: \"\"';
    $headers[] = 'Sec-Ch-Ua-Platform: \"Android\"';
    $headers[] = 'Sec-Ch-Ua-Platform-Version: \"15.0.0\"';
    $headers[] = 'Sec-Ch-Ua-Model: \"24116RACCG\"';
    $headers[] = 'Sec-Ch-Ua-Bitness: \"\"';
    $headers[] = 'Sec-Ch-Ua-Full-Version-List: \"Chromium\";v=\"127.0.6533.144\", \"Not)A;Brand\";v=\"99.0.0.0\", \"Microsoft Edge Simulate\";v=\"127.0.6533.144\", \"Lemur\";v=\"127.0.6533.144\"';
    $headers[] = 'Accept-Language: pt-BR';
    $headers[] = 'Sec-Fetch-Site: none';
    $headers[] = 'Sec-Fetch-Mode: navigate';
    $headers[] = 'Sec-Fetch-Dest: document';
    $headers[] = 'Priority: u=0, i';

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    
    $html = html_entity_decode($result, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $html = preg_replace('/\s+/', ' ', $html);
    
    $p1 = explode('value="', $html);
    $p2 = explode('"', $p1[1]);
    $token = $p2[0];
    
    return $token;
}

function cookies_sodresantoro() {
    //Pegar cookies direto no site
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://www.sodresantoro.com.br/veiculos/lotes?lot_category=carros&sort=auction_date_init_asc');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies/sodresantoro-cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies/sodresantoro-cookies.txt');
    
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36';
    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
    //$headers[] = 'Accept-Encoding: gzip, deflate, br, zstd';
    $headers[] = 'Cache-Control: max-age=0';
    $headers[] = 'Sec-Ch-Ua: \"Chromium\";v=\"127\", \"Not)A;Brand\";v=\"99\", \"Microsoft Edge Simulate\";v=\"127\", \"Lemur\";v=\"127\"';
    $headers[] = 'Sec-Ch-Ua-Mobile: ?1';
    $headers[] = 'Sec-Ch-Ua-Platform: \"Android\"';
    $headers[] = 'Accept-Language: pt-BR';
    $headers[] = 'Upgrade-Insecure-Requests: 1';
    $headers[] = 'Sec-Fetch-Site: same-origin';
    $headers[] = 'Sec-Fetch-Mode: navigate';
    $headers[] = 'Sec-Fetch-User: ?1';
    $headers[] = 'Sec-Fetch-Dest: document';
    $headers[] = 'Priority: u=0, i';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    curl_exec($ch);
}

function slugify($text) {

    $map = [
        'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a',
        'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
        'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
        'ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
        'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
        'ç'=>'c',
        'Á'=>'a','À'=>'a','Ã'=>'a','Â'=>'a','Ä'=>'a',
        'É'=>'e','È'=>'e','Ê'=>'e','Ë'=>'e',
        'Í'=>'i','Ì'=>'i','Î'=>'i','Ï'=>'i',
        'Ó'=>'o','Ò'=>'o','Õ'=>'o','Ô'=>'o','Ö'=>'o',
        'Ú'=>'u','Ù'=>'u','Û'=>'u','Ü'=>'u',
        'Ç'=>'c'
    ];

    $text = strtr($text, $map);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    
    unset($map);

    return trim($text, '-');
}

function listar_veiculos() {
    $cache_file = __DIR__ . '/cache/lista_veiculos.json';
    $cache_ttl  = 60 * 60 * 24 * 5; // 5 dias
    
    if (file_exists($cache_file)) {
        $cache_time = filemtime($cache_file);
        if (time() - $cache_time < $cache_ttl) {
            return file_get_contents($cache_file);
            exit;
        }
    }
    
    $mgl_token = cookies_mgl();
    
    // Site: https://www.mgl.com.br
    // Categoria: Carros
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://www.mgl.com.br/ApiFeatures/GetBusca/0/1/0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"RangeValores\":0,\"Scopo\":0,\"IgnoreScopo\":0,\"OrientacaoBusca\":0,\"Mapa\":\"\",\"Busca\":\"\",\"ID_Categoria\":88,\"ID_Estado\":0,\"ID_Cidade\":0,\"Bairro\":\"\",\"ID_Regiao\":0,\"ValorMinSelecionado\":0,\"ValorMaxSelecionado\":0,\"CFGs\":\"\",\"Pagina\":0,\"sInL\":\"\",\"Ordem\":0,\"QtdPorPagina\":9999,\"SubStatus\":[],\"ID_Leiloes_Status\":[],\"PaginaIndex\":1,\"BuscaProcesso\":\"\",\"NomesPartes\":\"\",\"CodLeilao\":\"\",\"TiposLeiloes\":[],\"PracaAtual\":0,\"DataAbertura\":\"\",\"DataEncerramento\":\"\",\"CamposDinamicos\":[],\"Filtro\":{}}");
    
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies/mgl-cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies/mgl-cookies.txt');
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, headers_mgl($mgl_token));
    
    $result = curl_exec($ch);
    
    $mgl_carros = normalizar_mgl($result, "Carros");
    
    unset($result);
    
    // Site: https://www.mgl.com.br
    // Categoria: Motos
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://www.mgl.com.br/ApiFeatures/GetBusca/1/1/0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"RangeValores\":0,\"Scopo\":0,\"IgnoreScopo\":0,\"OrientacaoBusca\":0,\"Mapa\":\"\",\"Busca\":\"\",\"ID_Categoria\":116,\"ID_Estado\":0,\"ID_Cidade\":0,\"Bairro\":\"\",\"ID_Regiao\":0,\"ValorMinSelecionado\":0,\"ValorMaxSelecionado\":0,\"CFGs\":\"\",\"Pagina\":0,\"sInL\":\"\",\"Ordem\":0,\"QtdPorPagina\":9999,\"SubStatus\":[],\"ID_Leiloes_Status\":[],\"PaginaIndex\":1,\"BuscaProcesso\":\"\",\"NomesPartes\":\"\",\"CodLeilao\":\"\",\"TiposLeiloes\":[],\"PracaAtual\":0,\"DataAbertura\":\"\",\"DataEncerramento\":\"\",\"CamposDinamicos\":[],\"Filtro\":{}}");
    
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies/mgl-cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies/mgl-cookies.txt');
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, headers_mgl($mgl_token));
    
    $result = curl_exec($ch);
    
    $mgl_motos = normalizar_mgl($result, "Motos");
    
    unset($result);
    
    // Site: https://www.mgl.com.br
    // Categoria: Caminhonetes
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://www.mgl.com.br/ApiFeatures/GetBusca/1/1/0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"RangeValores\":0,\"Scopo\":0,\"IgnoreScopo\":0,\"OrientacaoBusca\":0,\"Mapa\":\"\",\"Busca\":\"\",\"ID_Categoria\":117,\"ID_Estado\":0,\"ID_Cidade\":0,\"Bairro\":\"\",\"ID_Regiao\":0,\"ValorMinSelecionado\":0,\"ValorMaxSelecionado\":0,\"CFGs\":\"\",\"Pagina\":0,\"sInL\":\"\",\"Ordem\":0,\"QtdPorPagina\":9999,\"SubStatus\":[],\"ID_Leiloes_Status\":[],\"PaginaIndex\":1,\"BuscaProcesso\":\"\",\"NomesPartes\":\"\",\"CodLeilao\":\"\",\"TiposLeiloes\":[],\"PracaAtual\":0,\"DataAbertura\":\"\",\"DataEncerramento\":\"\",\"CamposDinamicos\":[],\"Filtro\":{}}");
    
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies/mgl-cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies/mgl-cookies.txt');
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, headers_mgl($mgl_token));
    
    $result = curl_exec($ch);
    
    $mgl_caminhonetes = normalizar_mgl($result, "Caminhonetes");
    
    unset($result);
    
    // ========================================================
    cookies_sodresantoro();
    
    // Site: https://www.sodresantoro.com.br
    // Categoria: Carros
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://www.sodresantoro.com.br/api/search-lots');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"indices\":[\"veiculos\",\"judiciais-veiculos\"],\"query\":{\"bool\":{\"filter\":[{\"bool\":{\"should\":[{\"bool\":{\"must\":[{\"term\":{\"auction_status\":\"online\"}}]}},{\"bool\":{\"must\":[{\"term\":{\"auction_status\":\"aberto\"}}],\"must_not\":[{\"terms\":{\"lot_status_id\":[5,7]}}]}},{\"bool\":{\"must\":[{\"term\":{\"auction_status\":\"encerrado\"}},{\"terms\":{\"lot_status_id\":[6]}}]}}],\"minimum_should_match\":1}},{\"bool\":{\"should\":[{\"bool\":{\"must_not\":{\"term\":{\"lot_status_id\":6}}}},{\"bool\":{\"must\":[{\"term\":{\"lot_status_id\":6}},{\"term\":{\"segment_id\":1}}]}}],\"minimum_should_match\":1}},{\"bool\":{\"should\":[{\"bool\":{\"must_not\":[{\"term\":{\"lot_test\":true}}]}}],\"minimum_should_match\":1}}]}},\"post_filter\":{\"bool\":{\"filter\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"from\":0,\"size\":9999,\"aggs\":{\"lot_financeable\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_financeable\":{\"terms\":{\"field\":\"lot_financeable\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_category\":{\"filter\":{\"bool\":{\"must\":[]}},\"aggs\":{\"lot_category\":{\"terms\":{\"field\":\"lot_category\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_origin\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_origin\":{\"terms\":{\"field\":\"lot_origin\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_sinister\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_sinister\":{\"terms\":{\"field\":\"lot_sinister\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_brand\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_brand\":{\"terms\":{\"field\":\"lot_brand\",\"size\":500,\"order\":{\"_key\":\"asc\"}},\"aggs\":{\"lot_model\":{\"terms\":{\"field\":\"lot_model\",\"size\":1500,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":1}}}}}},\"client_name\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"client_name\":{\"terms\":{\"field\":\"client_name\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_year_model\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_year_model\":{\"stats\":{\"field\":\"lot_year_model\"}}}},\"lot_km\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_km\":{\"stats\":{\"field\":\"lot_km\"}}}},\"lot_fuel\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_fuel\":{\"terms\":{\"field\":\"lot_fuel\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_transmission\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_transmission\":{\"terms\":{\"field\":\"lot_transmission\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_optionals\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_optionals\":{\"terms\":{\"field\":\"lot_optionals\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_location\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_location\":{\"terms\":{\"field\":\"lot_location\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_praca_label\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"carros\"]}}]}},\"aggs\":{\"lot_praca_label\":{\"terms\":{\"field\":\"lot_praca_label\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}}},\"sort\":[{\"lot_status_id_order\":{\"order\":\"asc\"}},{\"auction_date_init\":{\"order\":\"asc\"}}]}");
    
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies/sodresantoro-cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies/sodresantoro-cookies.txt');
    
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36';
    $headers[] = 'Accept: application/json';
    //$headers[] = 'Accept-Encoding: gzip, deflate, br, zstd';
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Sec-Ch-Ua: \"Chromium\";v=\"127\", \"Not)A;Brand\";v=\"99\", \"Microsoft Edge Simulate\";v=\"127\", \"Lemur\";v=\"127\"';
    $headers[] = 'Sec-Ch-Ua-Platform: \"Android\"';
    $headers[] = 'Accept-Language: pt-BR';
    $headers[] = 'Sec-Ch-Ua-Mobile: ?1';
    $headers[] = 'Origin: https://www.sodresantoro.com.br';
    $headers[] = 'Sec-Fetch-Site: same-origin';
    $headers[] = 'Sec-Fetch-Mode: cors';
    $headers[] = 'Sec-Fetch-Dest: empty';
    $headers[] = 'Referer: https://www.sodresantoro.com.br/veiculos/lotes?lot_category=carros&sort=auction_date_init_asc';
    $headers[] = 'Priority: u=1, i';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    $sodresantoro_carros = normalizar_sodresantoro($result, "Carros");
    
    unset($result);
    
    // Site: https://www.sodresantoro.com.br
    // Categoria: Motos
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://www.sodresantoro.com.br/api/search-lots');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"indices\":[\"veiculos\",\"judiciais-veiculos\"],\"query\":{\"bool\":{\"filter\":[{\"bool\":{\"should\":[{\"bool\":{\"must\":[{\"term\":{\"auction_status\":\"online\"}}]}},{\"bool\":{\"must\":[{\"term\":{\"auction_status\":\"aberto\"}}],\"must_not\":[{\"terms\":{\"lot_status_id\":[5,7]}}]}},{\"bool\":{\"must\":[{\"term\":{\"auction_status\":\"encerrado\"}},{\"terms\":{\"lot_status_id\":[6]}}]}}],\"minimum_should_match\":1}},{\"bool\":{\"should\":[{\"bool\":{\"must_not\":{\"term\":{\"lot_status_id\":6}}}},{\"bool\":{\"must\":[{\"term\":{\"lot_status_id\":6}},{\"term\":{\"segment_id\":1}}]}}],\"minimum_should_match\":1}},{\"bool\":{\"should\":[{\"bool\":{\"must_not\":[{\"term\":{\"lot_test\":true}}]}}],\"minimum_should_match\":1}}]}},\"post_filter\":{\"bool\":{\"filter\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"from\":0,\"size\":9999,\"aggs\":{\"lot_financeable\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_financeable\":{\"terms\":{\"field\":\"lot_financeable\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_category\":{\"filter\":{\"bool\":{\"must\":[]}},\"aggs\":{\"lot_category\":{\"terms\":{\"field\":\"lot_category\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_origin\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_origin\":{\"terms\":{\"field\":\"lot_origin\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_sinister\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_sinister\":{\"terms\":{\"field\":\"lot_sinister\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_brand\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_brand\":{\"terms\":{\"field\":\"lot_brand\",\"size\":500,\"order\":{\"_key\":\"asc\"}},\"aggs\":{\"lot_model\":{\"terms\":{\"field\":\"lot_model\",\"size\":1500,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":1}}}}}},\"client_name\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"client_name\":{\"terms\":{\"field\":\"client_name\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_year_model\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_year_model\":{\"stats\":{\"field\":\"lot_year_model\"}}}},\"lot_km\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_km\":{\"stats\":{\"field\":\"lot_km\"}}}},\"lot_fuel\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_fuel\":{\"terms\":{\"field\":\"lot_fuel\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_transmission\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_transmission\":{\"terms\":{\"field\":\"lot_transmission\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_optionals\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_optionals\":{\"terms\":{\"field\":\"lot_optionals\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_location\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_location\":{\"terms\":{\"field\":\"lot_location\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_praca_label\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"motos\"]}}]}},\"aggs\":{\"lot_praca_label\":{\"terms\":{\"field\":\"lot_praca_label\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}}},\"sort\":[{\"lot_status_id_order\":{\"order\":\"asc\"}},{\"auction_date_init\":{\"order\":\"asc\"}}]}");
    
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies/sodresantoro-cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies/sodresantoro-cookies.txt');
    
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36';
    $headers[] = 'Accept: application/json';
    //$headers[] = 'Accept-Encoding: gzip, deflate, br, zstd';
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Sec-Ch-Ua: \"Chromium\";v=\"127\", \"Not)A;Brand\";v=\"99\", \"Microsoft Edge Simulate\";v=\"127\", \"Lemur\";v=\"127\"';
    $headers[] = 'Sec-Ch-Ua-Platform: \"Android\"';
    $headers[] = 'Accept-Language: pt-BR';
    $headers[] = 'Sec-Ch-Ua-Mobile: ?1';
    $headers[] = 'Origin: https://www.sodresantoro.com.br';
    $headers[] = 'Sec-Fetch-Site: same-origin';
    $headers[] = 'Sec-Fetch-Mode: cors';
    $headers[] = 'Sec-Fetch-Dest: empty';
    $headers[] = 'Referer: https://www.sodresantoro.com.br/veiculos/lotes?lot_category=motos&sort=auction_date_init_asc';
    $headers[] = 'Priority: u=1, i';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    $sodresantoro_motos = normalizar_sodresantoro($result, "Motos");
    
    unset($result);
    
    // Site: https://www.sodresantoro.com.br
    // Categoria: Caminhonetes
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://www.sodresantoro.com.br/api/search-lots');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"indices\":[\"veiculos\",\"judiciais-veiculos\"],\"query\":{\"bool\":{\"filter\":[{\"bool\":{\"should\":[{\"bool\":{\"must\":[{\"term\":{\"auction_status\":\"online\"}}]}},{\"bool\":{\"must\":[{\"term\":{\"auction_status\":\"aberto\"}}],\"must_not\":[{\"terms\":{\"lot_status_id\":[5,7]}}]}},{\"bool\":{\"must\":[{\"term\":{\"auction_status\":\"encerrado\"}},{\"terms\":{\"lot_status_id\":[6]}}]}}],\"minimum_should_match\":1}},{\"bool\":{\"should\":[{\"bool\":{\"must_not\":{\"term\":{\"lot_status_id\":6}}}},{\"bool\":{\"must\":[{\"term\":{\"lot_status_id\":6}},{\"term\":{\"segment_id\":1}}]}}],\"minimum_should_match\":1}},{\"bool\":{\"should\":[{\"bool\":{\"must_not\":[{\"term\":{\"lot_test\":true}}]}}],\"minimum_should_match\":1}}]}},\"post_filter\":{\"bool\":{\"filter\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"from\":0,\"size\":9999,\"aggs\":{\"lot_financeable\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_financeable\":{\"terms\":{\"field\":\"lot_financeable\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_category\":{\"filter\":{\"bool\":{\"must\":[]}},\"aggs\":{\"lot_category\":{\"terms\":{\"field\":\"lot_category\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_origin\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_origin\":{\"terms\":{\"field\":\"lot_origin\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_sinister\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_sinister\":{\"terms\":{\"field\":\"lot_sinister\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_brand\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_brand\":{\"terms\":{\"field\":\"lot_brand\",\"size\":500,\"order\":{\"_key\":\"asc\"}},\"aggs\":{\"lot_model\":{\"terms\":{\"field\":\"lot_model\",\"size\":1500,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":1}}}}}},\"client_name\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"client_name\":{\"terms\":{\"field\":\"client_name\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_year_model\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_year_model\":{\"stats\":{\"field\":\"lot_year_model\"}}}},\"lot_km\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_km\":{\"stats\":{\"field\":\"lot_km\"}}}},\"lot_fuel\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_fuel\":{\"terms\":{\"field\":\"lot_fuel\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_transmission\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_transmission\":{\"terms\":{\"field\":\"lot_transmission\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_optionals\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_optionals\":{\"terms\":{\"field\":\"lot_optionals\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_location\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_location\":{\"terms\":{\"field\":\"lot_location\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}},\"lot_praca_label\":{\"filter\":{\"bool\":{\"must\":[{\"terms\":{\"lot_category\":[\"caminhões\"]}}]}},\"aggs\":{\"lot_praca_label\":{\"terms\":{\"field\":\"lot_praca_label\",\"size\":1000,\"order\":{\"_key\":\"asc\"},\"min_doc_count\":0}}}}},\"sort\":[{\"lot_status_id_order\":{\"order\":\"asc\"}},{\"auction_date_init\":{\"order\":\"asc\"}}]}");
    
    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ . '/cookies/sodresantoro-cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ . '/cookies/sodresantoro-cookies.txt');
    
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36';
    $headers[] = 'Accept: application/json';
    //$headers[] = 'Accept-Encoding: gzip, deflate, br, zstd';
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Sec-Ch-Ua: \"Chromium\";v=\"127\", \"Not)A;Brand\";v=\"99\", \"Microsoft Edge Simulate\";v=\"127\", \"Lemur\";v=\"127\"';
    $headers[] = 'Sec-Ch-Ua-Platform: \"Android\"';
    $headers[] = 'Accept-Language: pt-BR';
    $headers[] = 'Sec-Ch-Ua-Mobile: ?1';
    $headers[] = 'Origin: https://www.sodresantoro.com.br';
    $headers[] = 'Sec-Fetch-Site: same-origin';
    $headers[] = 'Sec-Fetch-Mode: cors';
    $headers[] = 'Sec-Fetch-Dest: empty';
    $headers[] = 'Referer: https://www.sodresantoro.com.br/veiculos/lotes?lot_category=caminh%C3%B5es&sort=auction_date_init_asc';
    $headers[] = 'Priority: u=1, i';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    $sodresantoro_caminhonetes = normalizar_sodresantoro($result, "Caminhonetes");
    
    unset($result);
    
    // Juntar todas informações
    $all_data = array_merge($mgl_carros, $mgl_motos, $mgl_caminhonetes, $sodresantoro_carros, $sodresantoro_motos, $sodresantoro_caminhonetes);
    
    $all_data = array_values($all_data);
    
    foreach ($all_data as $index => &$data) {
        $slug = slugify($all_data[$index]['titulo']);
        $data = array_merge(
            ['id' => $index + 1],
            ['slug' => $slug],
            $data
        );
    }
    unset($data);
    
    $response = [
        'status' => 'success',
        'total'  => count($all_data),
        'data'   => $all_data
    ];
    
    $json = json_encode($response);
    
    file_put_contents($cache_file, $json);
    
    return $json;
}
?>