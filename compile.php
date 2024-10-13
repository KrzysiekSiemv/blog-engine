<?php
    require_once "vendor/blog-engine/php/Configuration.php";
    require_once "vendor/blog-engine/php/Authentication/AuthController.php";
    use BlogEngine\Configuration;
    use BlogEngine\Authentication\AuthController;

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
                    case "init":
                        $this->init();
                        break;
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
                    foreach (array_keys($this->dictionary->PageTags()) as $page_tag) {
                        if ($page_tag == "{CSS}") {
                            for ($j = 2; $j < count($this->csss); $j++)
                                $this->dictionary->PageTags()[$page_tag] .= "\n      <link rel='stylesheet' href='$folder/css/{$this->csss[$j]}?v{$this->version}' />";
                        }

                        if ($page_tag == "{JS}") {
                            for ($j = 2; $j < count($this->jss); $j++)
                                $this->dictionary->PageTags()[$page_tag] .= "\n  <script src='$folder/js/{$this->jss[$j]}?v{$this->version}'></script>";
                        }

                        $site = str_replace($page_tag, $this->dictionary->PageTags()[$page_tag], $site);
                    }

                    $site = str_replace("<body>", "<body>\n<?php echo \$topbar; ?>", $site);

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
                        $this->dictionary->PageTags()["{CSS}"] .= "\n      <link rel='stylesheet' href='$css' />";
                        echo "* Dodano '$css' do styli w '$type'!\n";
                    }
                    foreach ($json["dev"]["js"] as $js){
                        $this->dictionary->PageTags()["{JS}"] .= "\n  <script src='$js'></script>";
                        echo "* Dodano '$js' do skryptów w '$type'!\n";
                    }
                    break;
                case "pub":
                    foreach ($json["pub"]["css"] as $css){
                        $this->dictionary->PageTags()["{CSS}"] .= "\n      <link rel='stylesheet' href='$css' />";
                        echo "* Dodano '$css' do styli w '$type'!\n";
                    }
                    foreach ($json["pub"]["js"] as $js){
                        $this->dictionary->PageTags()["{JS}"] .= "\n  <script src='$js'></script>";
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

* init - uruchamia kreator bloga            
* start-server [pub/dev] - uruchom serwer dla projektu. Opcja 'dev' specjalna dla programisty, opcja 'pub' dla każdego użytkownika. Domyślnie 'pub'
* help - wyświetl listę komend
* compile [pub/dev] - skompiluj projekt strony z folderu 'src'. Opcja 'dev' specjalna dla programisty, opcja 'pub' dla każdego użytkownika. Domyślnie 'pub'
";
        }

        public function init() : void {
            $data = array();
            echo "Okeeej! Czas na konfiguracje serwera blogowego! Zacznijmy od początku.\n";
            $_POST['BLOG_AUTHOR'] = readline("Jak się nazywasz: ");
            echo "\n> Cześć, {$_POST['BLOG_AUTHOR']}! ";

            $_POST['BLOG_TITLE'] = readline("Jak będzie się ten blog nazywał: ");
            while($_POST['BLOG_TITLE'] == ""){
                $_POST['BLOG_TITLE'] = readline("Jeszcze raz... Jak się będzie nazywać ten blog: ");
            }

            $_POST['BLOG_DESC'] = readline("O czym jest ten blog? Jaki jest jego opis: ");
            while($_POST['BLOG_DESC'] == ""){
                $_POST['BLOG_DESC'] = readline("Jeszcze raz... O czym jest ten blgo? Jaki jest jego opis: ");
            }

            $_POST['BLOG_TAGS'] = readline("Jakie są słowa kluczowe dla tego bloga (oddziel przecinkiem): ");
            while($_POST['BLOG_TAGS'] == ""){
                $_POST['BLOG_TAGS'] = readline("Jeszcze raz... Jakie są słowa kluczowe dla tego bloga? Oddziel słowa przecinkiem: ");
            }

            $_POST['BLOG_DOMAIN'] = readline("Na jakiej domenie będzie stał blog? (Domyślnie http://localhost): ");
            if($_POST['BLOG_DOMAIN'] == "")
                $_POST['BLOG_DOMAIN'] = "http://localhost";

            echo "\nDobrze, przejdźmy do konfiguracji bazy danych. Nie będzie trudno\n";
            $_POST['DB_HOST'] = readline("Jaki jest adres serwera bazy danych? (Domyślnie: 127.0.0.1:3306): ");
            if($_POST['DB_HOST'] == "")
                $_POST['DB_HOST'] = "127.0.0.1:3306";

            $_POST['DB_USER'] = readline("Nazwa użytkownika (Domyślnie: root): ");
            if($_POST['DB_USER'] == "")
                $_POST['DB_USER'] = "root";

            $_POST['DB_PASS'] = readline("Hasło: ");

            $_POST['DB_NAME'] = readline("Baza danych: ");
            while($_POST['DB_NAME'] == "")
                $_POST['DB_NAME'] = readline("Podaj poprawną nazwę bazy danych: ");

            echo "\nUstawmy teraz odpowiednie porty\n";
            $_POST['PUB_PORT'] = readline("Port dla publicznego trybu serwera (Domyślnie: 8080): ");
            if($_POST['PUB_PORT'] == "")
                $_POST['PUB_PORT'] = "8080";

            $_POST['DEV_PORT'] = readline("Port dla deweloperskiego trybu serwera (Domyślnie: 8081): ");
            if($_POST['DEV_PORT'] == "")
                $_POST['DEV_PORT'] = "8081";

            echo "\nTeraz skonfigurujmy konto administratora dla bloga\n";
            $_POST['ADMIN_USER'] = readline("Nazwa użytkownika (Domyślnie: admin): ");
            if($_POST['ADMIN_USER'] == "")
                $_POST['ADMIN_USER'] = "admin";

            $_POST['ADMIN_EMAIL'] = readline("Adres e-mail: ");
            while($_POST['ADMIN_EMAIL'] == "")
                $_POST['ADMIN_EMAIL'] = readline("Podaj poprawny adres mailowy: ");

            $ADMIN_PASS = readline("Hasło: ");
            $ADMIN_REPASS = readline("Powtórz hasło: ");

            while($ADMIN_PASS != $ADMIN_REPASS){
                echo "\nBłędnie wpisano hasła. Wpisz ponownie hasło\n";
                $ADMIN_PASS = readline("Hasło: ");
                $ADMIN_REPASS = readline("Powtórz hasło: ");
            }

            $_POST['ADMIN_PASS'] = $ADMIN_PASS;

            if(strtolower(readline("Czy chcesz skonfigurować teraz serwer poczty? (Y/N) (Domyślnie: N): ")) == "y"){
                $_POST['MAIL_NAME'] = readline("Podaj adres, z którego będą wysyłane maile: ");
                while($_POST['MAIL_NAME'] == "")
                    $_POST['MAIL_NAME'] = readline("Podaj adres, z którego będą wysyłane maile: ");

                $_POST['MAIL_PASS'] = readline("Hasło: ");
                while($_POST['MAIL_PASS'] == "")
                    $_POST['MAIL_PASS'] = readline("Hasło: ");

                $_POST['MAIL_SRV'] = readline("Serwer poczty wychodzącej (SMTP): ");
                while($_POST['MAIL_SRV'] == "")
                    $_POST['MAIL_SRV'] = readline("Serwer poczty wychodzącej (SMTP): ");

                $_POST['MAIL_PORT'] = readline("Port serwera: ");
                while($_POST['MAIL_PORT'] == "")
                    $_POST['MAIL_PORT'] = readline("Port serwera: ");

                $MAIL_SSL = strtolower(readline("Czy serwer poczty wymaga certyfikatu SSL? (Y/n): "));
                while($MAIL_SSL != "y" && $MAIL_SSL != "n"){
                    $MAIL_SSL = strtolower(readline("Czy serwer poczty wymaga certyfikatu SSL? (Y/n): "));
                }

                if($MAIL_SSL == "y")
                    $MAIL_SSL = 1;
                else
                    $MAIL_SSL = 0;
            } else {
                $_POST['MAIL_NAME'] = "";
                $_POST['MAIL_PASS'] = "";
                $_POST['MAIL_SRV'] = "";
                $_POST['MAIL_PORT'] = "";
                $MAIL_SSL = 0;
            }

            $data = [
                "BLOG_TITLE" => $_POST['BLOG_TITLE'], "BLOG_DESC" => $_POST['BLOG_DESC'], "BLOG_AUTHOR" => $_POST['BLOG_AUTHOR'], "BLOG_TAGS" => $_POST['BLOG_TAGS'], "BLOG_DOMAIN" => $_POST['BLOG_DOMAIN'], "BLOG_ICON" => $file,
                "DB_HOST" => $_POST['DB_HOST'], "DB_USER" => $_POST['DB_USER'], "DB_PASS" => $_POST['DB_PASS'], "DB_NAME" => $_POST['DB_NAME'],
                "PUB_PORT" => $_POST['PUB_PORT'], "DEV_PORT" => $_POST['DEV_PORT'],
                "ADMIN_USER" => $_POST['ADMIN_USER'], "ADMIN_EMAIL" => $_POST['ADMIN_EMAIL'], "ADMIN_PASS" => $_POST['ADMIN_PASS'],
                "MAIL_NAME" => $_POST['MAIL_NAME'], "MAIL_PASS" => $_POST['MAIL_PASS'], "MAIL_SRV" => $_POST['MAIL_SRV'], "MAIL_PORT" => $_POST['MAIL_PORT'], "MAIL_SSL" => $MAIL_SSL
            ];

            $configuration = new Configuration();
            $auth = new AuthController();

            $configuration->SendData($data);

            include "_config.php";

            $auth->CreateDatabase();

            echo "Pomyślnie skonfigurowano!";
        }
    }

    $exec = new CompileIt();
    $exec->main($argv, $argc);