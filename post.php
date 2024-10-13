<?php
    require "_config.php";
    require "vendor/blog-engine/php/Authentication/AuthController.php";
    require "vendor/blog-engine/php/Posts/PostController.php";

    use BlogEngine\Authentication\AuthController;
    use BlogEngine\Posts\PostController;
    
    session_start();

    $auth = new AuthController();
    $site = "";

    if(file_exists("_config.php")){
        $topbar = "";
        if($_SERVER['SERVER_PORT'] == DEV_PORT) {
            if(isset($_SESSION['uToken_BE'])){
                if($auth->CheckRole($_SESSION['uToken_BE']) == "administrator")
                    require "vendor/blog-engine/php/Sites/Views/be-topbar.php";
            }
            $site = file_get_contents("dev/post.html");
        } else if($_SERVER['SERVER_PORT'] == PUB_PORT) {
            if(isset($_SESSION['uToken_BE'])){
                if($auth->CheckRole($_SESSION['uToken_BE']) == "administrator")
                    require "vendor/blog-engine/php/Sites/Views/be-topbar.php";
            }
            $site = file_get_contents("public/post.html");
        } else
            echo "<h1>Uruchom ponownie serwer</h1>";
    } else
        require "be_firstrun.php";

    if(isset($_GET['id'])){
        $post = new PostController($auth);
        $data = $post->DownloadSinglePost($_GET['id']);

        

        echo $post->ConvertToSite($data, $site);
        print_r($data);
    } else {
        header("location: index.php?no_post");
    }
?>