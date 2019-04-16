<?php
libxml_use_internal_errors(true);
$doc = new \DOMDocument();
$content = file_get_contents("https://www.seminovosbh.com.br/resultadobusca/index/veiculo/carro/marca/BMW/modelo/1756/valor2/2000000/ano1/1990/ano2/2020/usuario/todos");
$doc->loadHTML($content, false);
$doc->formatOutput = true;
$topoConteudoBusca = $doc->getElementById("topoConteudoBusca");
$dls = $topoConteudoBusca->getElementsByTagName("dl");

foreach ($dls as $index => $dl) {
    if ($index != 0) {
        // $content = preg_replace('/\s{2,}|\n/', ' ', $dl->nodeValue);
        // echo $content . "\n";
        print_r( getdts($dl) );
        die;
    }

}

function getdts ($ctx_dl) {
    $dts = [];
    //echo "<". $ctx_dl->nodeName . ">";
    foreach($ctx_dl->childNodes as $index => $ctx) {
        if ($ctx->nodeName == "dt") {
            $dts["img"] = getImg($ctx);
        }

        if ($ctx->nodeName == "dd") {
            $dts = array_merge($dts, getTituloAndPreco($ctx));
        }

        if ($ctx->nodeName == "div") {
            $dts = array_merge($dts, getAdicionalInfos($ctx));
        }
    }
    //echo "\n</". $ctx_dl->nodeName . ">\n\n";
    return $dts;
}

function getTituloAndPreco ($ctx_dd) {
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

function getImg ($ctx_dt) {
    $a = null;

    $img = function($ctx_a) {
        $img_url = null;
        foreach($ctx_a->childNodes as $child_img) {
            if ($child_img->nodeName == "img") {
                foreach ($child_img->attributes as $attr_src) {
                    // $img_url = $attr_src->getNamedItem("src")->textContent;
                    if ($attr_src->name == "src") {
                        $img_url = $attr_src->value;
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

    return $img($a);
}

function getAdicionalInfos ($ctx_div) {
    $aditionalInfos = [];

    $checkClass = function ($ctx_div_attrs) {
        $hasRightClass = false;
        foreach ($ctx_div_attrs as $index => $attr_class) {
            if ($attr_class->name == "class" && $attr_class->value == "bg-nitro-mais-home") {
                $hasRightClass = true;
            } else {
                echo "nao tem nenhuma div com classe bg-nitro-mais-home";
            }
        }

        return $hasRightClass;
    };

    $getPs = function ($ctx_sub_divs) {
        // var_dump($ctx_sub_divs);die;
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