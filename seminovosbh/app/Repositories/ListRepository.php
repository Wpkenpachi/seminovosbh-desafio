<?php

namespace App\Repositories;

class ListRepository {
    private $htmlContent;
    private $final_array = [];

    public function buildUrl (array $params) {
        $url = "https://www.seminovosbh.com.br/resultadobusca/index/veiculo/carro/marca/{$params['marca']}/modelo/{$params['modelo']}/valor2/2000000/ano1/1990/ano2/2020/usuario/todos";
        return $url;
    }

    public function carsList(string $url) {
        $this->htmlContent = file_get_contents($url);
        $this->getDls();
        return $this->final_array;
    }

    private function getDls () {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($this->htmlContent, false);
        $doc->formatOutput = true;
        $topoConteudoBusca = $doc->getElementById("topoConteudoBusca");
        $dls = $topoConteudoBusca->getElementsByTagName("dl");
        foreach ($dls as $index => $dl) {
            if ($index != 0) {
                $has_dts = $this->getDts($dl);
                if (count($has_dts)) {
                    $this->final_array[] = $this->getDts($dl);
                }
            }
        }
    }

    private function getDts ($ctx_dl) {
        $dts = [];
        foreach($ctx_dl->childNodes as $index => $ctx) {
            if ($ctx->nodeName == "dt") {
                $img_src = $this->getImg($ctx);
                if ($img_src) {
                    $dts["img"] = $img_src;
                }
            }

            if ($ctx->nodeName == "dd") {
                $dts = array_merge($dts, $this->getTituloAndPreco($ctx));
            }

            if ($ctx->nodeName == "div") {
                $dts = array_merge($dts, $this->getAdicionalInfos($ctx));
            }
        }

        return $dts;
    }

    private function getTituloAndPreco ($ctx_dd) {
        $a = null;

        $h4 = function ($ctx_a) {
            $h4_content = null;
            $span_price = null;
            foreach ($ctx_a->childNodes as $child_h4) {
                if ($child_h4->nodeName == "h4") {
                    $h4_content = $child_h4->textContent;

                    // Getting span with price
                    foreach ( $child_h4->childNodes as $child_span ) {
                        if ($child_span->nodeName == "span") {
                            $span_price = $child_span->textContent;
                        }
                    }
                }
            }

            return ['title' => $h4_content, 'price' => $span_price];
        };

        foreach ($ctx_dd->childNodes as $child_a) {
            if ($child_a->nodeName == "a") {
                $a = $child_a;
            }
        }

        return $h4($a);
    }

    private function getImg ($ctx_dt) {
        $a = null;

        $img = function($ctx_a) {
            $img_url = null;
            if ($ctx_a->childNodes) {
                foreach($ctx_a->childNodes as $child_img) {
                    if ($child_img->nodeName == "img") {
                        foreach ($child_img->attributes as $attr_src) {
                            if ($attr_src->name == "src") {
                                $img_url = $attr_src->value;
                            }
                        }
                    }
                }
            }

            return $img_url;
        };

        foreach ($ctx_dt->childNodes as $child_a) {
            if ($child_a->nodeName == "a") {
                $a = $child_a;
            }
        }

        return $a ? $img($a) : "";
    }

    private function getAdicionalInfos ($ctx_div) {
        $aditionalInfos = [];

        $getPs = function ($ctx_sub_divs) {
            $ps = [];

            if ($ctx_sub_divs->childNodes) {
                foreach ($ctx_sub_divs->childNodes as $p) {
                    if ($p->nodeName == "p") {
                        $ps[] = $p->textContent;
                    }
                }
            }


            return $ps;
        };

        $getSpans = function ($ctx_sub_divs) {
            $spans = [];

            if ($ctx_sub_divs->childNodes) {
                foreach ($ctx_sub_divs->childNodes as $span) {
                    if ($span->nodeName == "span") {
                        $spans[] = $span->textContent;
                    }
                }
            }

            return $spans;
        };
        
        foreach ($ctx_div->childNodes as $child_div) {
            if ($child_div->nodeName == "dd" ) {
                // Getting DD's with aditional infos
                foreach( $child_div->childNodes as $index => $childAditionalInfos) {
                    if ( in_array($childAditionalInfos->nodeName, ['p', 'span']) ) {
                        if ($index != 0) { // ignoring the first div of bg-nitro-mais-home, because its a img/button 'buy'
                            $aditionalInfos['aditionalInfo'][] = $childAditionalInfos->textContent;
                        }
                    }
                }
            }
        }

        return $aditionalInfos;
    }
}