<?php
    require_once "vendor/blog-engine/php/Configuration.php";
    require_once "vendor/blog-engine/php/Authentication/AuthController.php";
    require_once 'vendor/autoload.php';

    if(file_exists("_config.php"))
        require "_config.php";

    session_start();

    use BlogEngine\Authentication\AuthController;
    use BlogEngine\Configuration;

    $auth = new AuthController();
    $configuration = new Configuration();

    if(isset($_COOKIE['uToken_BE']))
        $_SESSION['uToken_BE'] = $_COOKIE['uToken_BE'];

    if(isset($_POST['config-me'])) {
        // Wczytywanie boolean'a
        // Generował błąd na stronie, ponieważ nie wykrywało domyślnie MAIL_SSL, dlatego jest sprawdzany czy jest w $_POST;
        if(isset($_POST['MAIL_SSL']))
            $ssl = ($_POST['MAIL_SSL'] == "on"?1:0);
        else
            $ssl = 0;

        if(isset($_FILES['BLOG_ICON'])){
            if(strtolower(pathinfo(basename($_FILES['BLOG_ICON']['name']),PATHINFO_EXTENSION)) == "png") {
                if (move_uploaded_file($_FILES['BLOG_ICON']['tmp_name'], "resources/favicon.png")) {
                    $file = "resources/favicon.png";
                }
                    $file = "";
            }
                $file = "";
        } else
            $file = "";

        // Wczytywanie danych do tablicy
        $data = [
            "BLOG_TITLE" => $_POST['BLOG_TITLE'], "BLOG_DESC" => $_POST['BLOG_DESC'], "BLOG_AUTHOR" => $_POST['BLOG_AUTHOR'], "BLOG_TAGS" => $_POST['BLOG_TAGS'], "BLOG_DOMAIN" => $_POST['BLOG_DOMAIN'], "BLOG_ICON" => $file,
            "DB_HOST" => $_POST['DB_HOST'], "DB_USER" => $_POST['DB_USER'], "DB_PASS" => $_POST['DB_PASS'], "DB_NAME" => $_POST['DB_NAME'],
            "PUB_PORT" => $_POST['PUB_PORT'], "DEV_PORT" => $_POST['DEV_PORT'],
            "ADMIN_USER" => $_POST['ADMIN_USER'], "ADMIN_EMAIL" => $_POST['ADMIN_EMAIL'], "ADMIN_PASS" => $_POST['ADMIN_PASS'],
            "MAIL_NAME" => $_POST['MAIL_NAME'], "MAIL_PASS" => $_POST['MAIL_PASS'], "MAIL_SRV" => $_POST['MAIL_SRV'], "MAIL_PORT" => $_POST['MAIL_PORT'], "MAIL_SSL" => $ssl
        ];

        // Tworzenie pliku konfiguracyjnego
        $configuration->SendData($data);

        // Dodanie pliku konfiguracyjnego po utworzeniu
        include "_config.php";

        // Zbudowanie całej bazy
        $auth->CreateDatabase();

        // Czyszczenie i ładowanie całej strony
        $_POST = array();

        // Dodawanie do ciasteczek UUID administratora, by miał dostęp do bazy
        $conn = $auth->Connection();

        $res = mysqli_query($conn, "SELECT login_token FROM users WHERE id_user = 1");
        if($row = mysqli_fetch_row($res)){
            // setcookie("uToken_BE", $row[0], time()+3600*24, "/");
            $_SESSION['uToken_BE'] = $row[0];
        }

        $auth->Close($conn);
    }

    if(file_exists("_config.php")){
        $topbar = "";
        if($_SERVER['SERVER_PORT'] == DEV_PORT) {
            if(isset($_SESSION['uToken_BE'])){
                if($auth->CheckRole($_SESSION['uToken_BE']) == "administrator")
                    $topbar = "vendor/blog-engine/php/Sites/Views/be-topbar.php";
            }
            require "dev/index.html";
        } else if($_SERVER['SERVER_PORT'] == PUB_PORT) {
            if(isset($_SESSION['uToken_BE'])){
                if($auth->CheckRole($_SESSION['uToken_BE']) == "administrator")
                    $topbar = "vendor/blog-engine/php/Sites/Views/be-topbar.php";
            }
            require "public/index.html";
        } else
            echo "<h1>Uruchom ponownie serwer</h1>";
    } else
        require "be_firstrun.php";