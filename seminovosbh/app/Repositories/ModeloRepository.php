<?php

namespace App\Repositories;

class ModeloRepository {
    public function modelosList($value) {
        $url            = "https://www.seminovosbh.com.br/json/modelos/buscamodelo/marca/{$value}/data.js?v3.47.11-hk";
        $modelosJson    = file_get_contents($url);
        return json_decode($modelosJson, true);
    }
}