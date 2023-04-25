<?php
    require "vendor/blog-engine/php/Dictionary.php";
    $dictionary = new \BlogEngine\Dictionary();

            $src = __DIR__ . "/src/";
            $html = scandir($src . "views");
            $css = scandir($src . "css");
            $js = scandir($src . "js");

            for($i = 2; $i < count($html); $i++){
                $site = file_get_contents($src . "views/" . $html[$i]);
                foreach (array_keys($dictionary->page_tags) as $page_tag){
                    if($page_tag = "{CSS}"){
                        for($j = 2; $j < count($css); $j++)
                            $dictionary->page_tags[$page_tag] .= "<link rel='stylesheet' href='public/css/{$css[$j]}' />";
                    }

                    if($page_tag = "{JS}"){
                        for($j = 2; $j < count($js); $j++)
                            $dictionary->page_tags[$page_tag] .= "<script src='public/js/{$js[$j]}'></script>";
                    }

                    $site = str_replace($page_tag, $dictionary->page_tags[$page_tag], $site);
                }

                echo $site;
            }