<?php
set_time_limit(0);
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// =============== Lista de imóveis ===============
// =============== Por: Eduardo Venancio ===============

function remove_accent($text) {
    $map = [
        'Ú'=>'u','Ù'=>'u','Û'=>'u','Ü'=>'u'
    ];

    $text = strtr($text, $map);
    return $text;
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

function image_link($id) {
    $id = trim((string)$id);
    $default_url = "https://venda-imoveis.caixa.gov.br/fotos/F";
    $num_id = strlen($id);
    if ($num_id == 13) {
        $name_image = $id . "21.jpg";
    } else {
        $quantity_zero = 13 - $num_id;
        $zeros = str_repeat("0", max("0", $quantity_zero));
        $name_image = $zeros . $id . "21.jpg";
    }
    
    $url_image = $default_url . $name_image;
    
    unset($id, $default_url, $num_id, $name_image, $quantity_zero, $zeros);
    
    return $url_image;
}

function listar_imoveis() {
    $cache_file = __DIR__ . '/cache/lista_imoveis.json';
    $cache_ttl  = 60 * 60 * 24 * 5; // 5 dias
    
    if (file_exists($cache_file)) {
        $cache_time = filemtime($cache_file);
        if (time() - $cache_time < $cache_ttl) {
            return file_get_contents($cache_file);
            exit;
        }
    }
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, 'https://venda-imoveis.caixa.gov.br/listaweb/Lista_imoveis_geral.csv?523997705');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
    $headers = array();
    $headers[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Mobile Safari/537.36';
    $headers[] = 'Accept-Encoding: identity';
    $headers[] = 'Cache-Control: no-cache';
    $headers[] = 'Pragma: no-cache';
    $headers[] = 'Sec-Fetch-Site: none';
    $headers[] = 'Sec-Fetch-Mode: navigate';
    $headers[] = 'Sec-Fetch-Dest: empty';
    $headers[] = 'Referer: https://venda-imoveis.caixa.gov.br/sistema/download-lista.asp';
    $headers[] = 'Accept-Language: pt-BR';
    $headers[] = 'Priority: u=4, i';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);

    curl_close($ch);
    unset($ch);

    $result = mb_convert_encoding($result, 'UTF-8', 'ISO-8859-1');
    
    // normaliza quebras de linha
    $result = str_replace("\r", "", $result);
    
    $rows = explode("\n", $result);
    $rows = array_slice($rows, 4);

    unset($result);
    
    $all_data = [];
    $id = 0;
    foreach ($rows as $row) {
        $row = trim($row);
        if ($row === '') continue;
    
        $columns = explode(";", $row);
        
        // Chamados a função para gerar o link das imagens
        $id_default = $columns[0];
        $url_image = image_link($id_default);
        
        $columns = array_slice($columns, 1);
    
        // DEBUG TEMPORÁRIO
        if (count($columns) < 9) {
            continue;
        }
        
        $id++;
        $slug = slugify($columns[3]);
        
        $valor_avaliacao = str_replace(['.', ','], ['', '.'], trim($columns[4]));
        $valor_venda = str_replace(['.', ','], ['', '.'], trim($columns[5]));
        $porcentagem = explode('.', trim($columns[6]))[0];
        
        $raw_address = trim($columns[3]);

        // Quebra por vírgula
        $parts = array_map('trim', explode(',', $raw_address));
        
        // Rua (remove conteúdo entre parênteses)
        $road = preg_replace('/\s*\(.*?\)/', '', $parts[0]);
        $road = ucwords(strtolower($road));
        
        // Número (se existir)
        $number_address = '';
        if (isset($parts[1])) {
            $number_address = strtolower($parts[1]);
        
            // remove textos comuns
            $number_address = str_replace(
                ['n.', 'nº', 'no', 'numero', ' ', 'sn', 's/n'],
                '',
                $number_address
            );
        
            // se não sobrar número válido, ignora
            if (!is_numeric($number_address)) {
                $number_address = '';
            }
        }
        
        // Cidade e UF
        $city = ucwords(strtolower(trim($columns[1])));
        $uf   = strtoupper(trim($columns[0]));
        
        // Titulo
        $type_item = explode(", ", trim($columns[7]))[0];
        $title_item = trim($type_item) . " em " . $city;
        
        // Montagem final
        $search_maps = $road;
        
        if ($number_address !== '') {
            $search_maps .= ", " . $number_address;
        }
        
        $search_maps .= ", " . $city . " - " . $uf;
        
        $maps_url = "https://www.google.com/maps/search/?api=1&query=" . urlencode($search_maps);
        
        $all_data[] = [
            'id' => $id,
            'slug' => $slug,
            'imagem' => $url_image,
            'titulo' => ucwords(strtolower(trim($title_item))),
        
            'uf' => strtoupper(trim($columns[0])),
            'cidade' => ucwords(strtolower(trim($columns[1]))),
            'bairro' => ucwords(strtolower(trim($columns[2]))),
            'endereco' => ucwords(strtolower(trim($columns[3]))),
        
            'valor_avaliacao' => (int) $valor_avaliacao,
            'valor_venda' => (int) $valor_venda,
            'desconto_percentual' => (int) $porcentagem,
        
            'descricao' => ucfirst(strtolower(trim($columns[7]))),
            'modalidade_venda' => str_replace(" -", "", remove_accent(ucfirst(strtolower(trim($columns[8]))))),
        
            'link_detalhe' => trim($columns[9]),
            'google_maps' => $maps_url
        ];
        
        unset(
            $columns,
            $row,
            $parts,
            $raw_address,
            $road,
            $number_address,
            $city,
            $uf,
            $search_maps,
            $maps_url,
            $slug,
            $type_item,
            $title_item,
            $valor_avaliacao,
            $valor_venda,
            $porcentagem,
            $url_image,
            $id_default
        );
    }
    
    unset($rows, $headers, $cache_ttl, $cache_time);
    
    $response = [
        'status' => 'success',
        'total'  => count($all_data),
        'data'   => $all_data
    ];
    
    $json = json_encode($response);
    
    unset($response, $all_data);
    
    file_put_contents($cache_file, $json);
    
    unset($cache_file);
    
    return $json;
}
?>