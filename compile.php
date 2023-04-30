<?php
    require "vendor/blog-engine/php/Dictionary.php";
    class CompileIt{
        public \BlogEngine\Dictionary $dictionary;
        public function __construct(public $src = __DIR__ . "/src/", public $htmls = [], public $csss = [], public $jss = [], public $version = 0){
            $this->dictionary = new BlogEngine\Dictionary();

            // POBIERANIE ZAWARTOŚCI FOLDERU src
            $this->src = __DIR__ . "/src/";
            $this->htmls = scandir($src . "views");
            $this->csss = scandir($src . "css");
            $this->jss = scandir($src . "js");

            //$this->version =
        }

        public function main() : void{
            $this->checkFolders();
            $this->compileFiles();

            echo "Done";
        }

        /*
         *      SPRAWDZANIE CZY SĄ FOLDERY css I js
         */
        function checkFolders() : void{
            if(!file_exists("public/css"))
                mkdir("public/css");

            if(!file_exists("public/js"))
                mkdir("public/js");
        }

        function compileFiles() : void{
            // KOPIOWANIE PLIKÓW HTML DO PUBLICZNEGO FOLDERU
            foreach ($this->htmls as $html) {
                $file = $this->src . "views/" . $html;
                if(pathinfo($file, PATHINFO_EXTENSION) == "html") {
                    $name = basename($file);
                    $site = file_get_contents($file);
                    foreach (array_keys($this->dictionary->page_tags) as $page_tag) {
                        echo $page_tag;
                        if ($page_tag == "{CSS}") {
                            $this->dictionary->page_tags[$page_tag] .= "<link rel='stylesheet' href='vendor/blog-engine/blog.css' />";
                            for ($j = 2; $j < count($this->csss); $j++)
                                $this->dictionary->page_tags[$page_tag] .= "\n      <link rel='stylesheet' href='public/css/{$this->csss[$j]}' />";
                        }

                        if ($page_tag == "{JS}") {
                            $this->dictionary->page_tags[$page_tag] .= "<script src='vendor/blog-engine/blog.js'></script>";
                            for ($j = 2; $j < count($this->jss); $j++)
                                $this->dictionary->page_tags[$page_tag] .= "\n  <script src='public/js/{$this->jss[$j]}'></script>";
                        }

                        $site = str_replace($page_tag, $this->dictionary->page_tags[$page_tag], $site);
                    }

                    $site_file = fopen("public/" . $name, "w");
                    fwrite($site_file, $site);
                    fclose($site_file);
                }
            }

            // PRZENOSZENIE PLIKÓW CSS DO PUBLICZNEGO FOLDERU
            foreach ($this->csss as $css){
                $file = $this->src . "css/" . $css;
                if(pathinfo($file, PATHINFO_EXTENSION) == "css" || pathinfo($file, PATHINFO_EXTENSION) == "scss"){
                    $name = basename($file);;
                    $style = file_get_contents($file);

                    $style_file = fopen("public/css/" . $name, "w");
                    fwrite($style_file, $style);
                    fclose($style_file);
                }
            }

            // PRZENOSZENIE PLIKÓW JS DO PUBLICZNEGO FOLDERU
            foreach ($this->jss as $js){
                $file = $this->src . "js/" . $js;
                if(pathinfo($file, PATHINFO_EXTENSION) == "js"){
                    $name = basename($file);
                    $script = file_get_contents($file);

                    $script_file = fopen("public/js/" . $name, "w");
                    fwrite($script_file, $script);
                    fclose($script_file);
                }
            }
        }
    }

    $exec = new CompileIt();
    $exec->main();