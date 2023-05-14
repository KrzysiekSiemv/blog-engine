<?php
    if(file_exists("_config.php")) {
        require "vendor/blog-engine/php/Dictionary.php";
    }
    class CompileIt{
        public \BlogEngine\Dictionary $dictionary;
        public function __construct(public $src = __DIR__ . "/src/", public $htmls = [], public $csss = [], public $jss = [], public float $version = 0){
            if(file_exists("_config.php"))
                $this->dictionary = new BlogEngine\Dictionary();

            // POBIERANIE ZAWARTOŚCI FOLDERU src
            $this->src = __DIR__ . "/src/";
            $this->htmls = scandir($src . "views");
            $this->csss = scandir($src . "css");
            $this->jss = scandir($src . "js");

            $this->version = floatval(file_get_contents("version"));
        }

        public function main($argv, $argc) : void{
            if($argc > 1){
                switch($argv[1]){
                    case "compile":
                        if($argv[2] != null)
                            switch($argv[2]) {
                                case "dev":
                                    echo "
==========================
= KOMPILOWANIE DO 'dev'
==========================
";
                                    $this->versionUp("dev");
                                    $this->addCustoms("dev");
                                    $this->checkFolders("dev");
                                    $this->compileFiles("dev");
                                    break;
                                default:
                                    echo "
==========================
= KOMPILOWANIE DO 'pub'
==========================
";
                                    $this->versionUp("pub");
                                    $this->addCustoms("pub");
                                    $this->checkFolders("public");
                                    $this->compileFiles("public");
                                    break;
                            }
                        else {
                            echo "
==========================
= KOMPILOWANIE DO 'pub'
==========================
";
                            $this->versionUp("pub");
                            $this->addCustoms("pub");
                            $this->checkFolders("public");
                            $this->compileFiles("public");
                        }
                        break;
                    case "start-server":
                        if($argv[2] != null)
                            $this->runServer($argv[2]);
                        else
                            $this->runServer("pub");
                        break;
                    case "help":
                        $this->help();
                        break;
                }
            } else {
                echo "Brak parametru rozruchowego. Wpisz help, aby zobaczyć listę dostępnych komend. \n";
            }
        }

        /*
         *      SPRAWDZANIE CZY SĄ FOLDERY css I js
         */
        function checkFolders($folder) : void{
            if(!file_exists("$folder/css"))
                mkdir("$folder/css");

            if(!file_exists("$folder/js"))
                mkdir("$folder/js");
        }

        function compileFiles($folder) : void{
            // KOPIOWANIE PLIKÓW HTML DO PUBLICZNEGO FOLDERU
            foreach ($this->htmls as $html) {
                $file = $this->src . "views/" . $html;
                if(pathinfo($file, PATHINFO_EXTENSION) == "html") {
                    $name = basename($file);
                    $site = file_get_contents($file);
                    foreach (array_keys($this->dictionary->page_tags) as $page_tag) {
                        if ($page_tag == "{CSS}") {
                            for ($j = 2; $j < count($this->csss); $j++)
                                $this->dictionary->page_tags[$page_tag] .= "\n      <link rel='stylesheet' href='$folder/css/{$this->csss[$j]}?v{$this->version}' />";
                        }

                        if ($page_tag == "{JS}") {
                            for ($j = 2; $j < count($this->jss); $j++)
                                $this->dictionary->page_tags[$page_tag] .= "\n  <script src='$folder/js/{$this->jss[$j]}?v{$this->version}'></script>";
                        }

                        $site = str_replace($page_tag, $this->dictionary->page_tags[$page_tag], $site);
                    }

                    $site = str_replace("<body>", "<body>\n<?php include \$topbar; ?>", $site);

                    $site_file = fopen("$folder/" . $name, "w");
                    fwrite($site_file, $site);
                    fclose($site_file);

                    echo "* Plik '$html' przetworzono do folderu '$folder'!\n";
                }
            }

            // PRZENOSZENIE PLIKÓW CSS DO PUBLICZNEGO FOLDERU
            foreach ($this->csss as $css){
                $file = $this->src . "css/" . $css;
                if(pathinfo($file, PATHINFO_EXTENSION) == "css" || pathinfo($file, PATHINFO_EXTENSION) == "scss"){
                    $name = basename($file);;
                    $style = file_get_contents($file);

                    $style_file = fopen("$folder/css/" . $name, "w");
                    fwrite($style_file, $style);
                    fclose($style_file);

                    echo "* Plik '$css' przetworzono do folderu '$folder'!\n";
                }
            }

            // PRZENOSZENIE PLIKÓW JS DO PUBLICZNEGO FOLDERU
            foreach ($this->jss as $js){
                $file = $this->src . "js/" . $js;
                if(pathinfo($file, PATHINFO_EXTENSION) == "js"){
                    $name = basename($file);
                    $script = file_get_contents($file);

                    $script_file = fopen("$folder/js/" . $name, "w");
                    fwrite($script_file, $script);
                    fclose($script_file);

                    echo "* Plik '$js' przetworzono do folderu '$folder'!\n";
                }
            }
        }

        public function versionUp($type) : void{
            switch($type){
                case "pub":
                    $this->version++;
                    break;
                case "dev":
                    $this->version += 0.1;
                    break;
            }

            $verfile = fopen("version", "w+");
            fwrite($verfile, $this->version);
            fclose($verfile);
        }

        public function addCustoms($type) : void {
            $json = json_decode(file_get_contents("custom.json"), true);
            switch($type){
                case "dev":
                    foreach ($json["dev"]["css"] as $css){
                        $this->dictionary->page_tags["{CSS}"] .= "\n      <link rel='stylesheet' href='$css' />";
                        echo "* Dodano '$css' do styli w '$type'!\n";
                    }
                    foreach ($json["dev"]["js"] as $js){
                        $this->dictionary->page_tags["{JS}"] .= "\n  <script src='$js'></script>";
                        echo "* Dodano '$js' do skryptów w '$type'!\n";
                    }
                    break;
                case "pub":
                    foreach ($json["pub"]["css"] as $css){
                        $this->dictionary->page_tags["{CSS}"] .= "\n      <link rel='stylesheet' href='$css' />";
                        echo "* Dodano '$css' do styli w '$type'!\n";
                    }
                    foreach ($json["pub"]["js"] as $js){
                        $this->dictionary->page_tags["{JS}"] .= "\n  <script src='$js'></script>";
                        echo "* Dodano '$js' do skryptów w '$type'!\n";
                    }
                    break;
            }
        }

        public function runServer($type) : void{
            if(file_exists("_config.php")){

                $port = match ($type) {
                    "dev" => DEV_PORT,
                    default => PUB_PORT,
                };
            } else
                $port = 14071;

            echo "
=========================================
Uruchamianie serwera na adresie: 
* http://127.0.0.1:$port *
Aby zatrzymać serwer, naciśnij Ctrl+C
=========================================

";
            exec("php -S 127.0.0.1:$port -t " . __DIR__);
        }

        public function help() : void {
            echo "
===============================
= BLOG ENGINE v0.1
= Krzysztof KrzysiekSiemv Smaga 2023
===============================
Dostępne komendy dla narzędzia:
            
* start-server [pub/dev] - uruchom serwer dla projektu. Opcja 'dev' specjalna dla programisty, opcja 'pub' dla każdego użytkownika. Domyślnie 'pub'
* help - wyświetl listę komend
* compile [pub/dev] - skompiluj projekt strony z folderu 'src'. Opcja 'dev' specjalna dla programisty, opcja 'pub' dla każdego użytkownika. Domyślnie 'pub'
";
        }
    }

    $exec = new CompileIt();
    $exec->main($argv, $argc);