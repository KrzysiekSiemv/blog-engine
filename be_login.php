<?php
    require_once "_config.php";
    require "vendor/blog-engine/php/Authentication/AuthController.php";

    session_start();

    use BlogEngine\Authentication\AuthController;
    $auth = new AuthController();

    if(isset($_POST['logmein'])){
        $remember = ($_POST['remember'] ?? false);
        $auth->Authenticate($_POST['login'], $_POST['password'], $remember);
    }
    if(isset($_SESSION['uToken_BE'])){
        if($auth->CheckRole($_SESSION['uToken_BE']) == "administrator" || $auth->CheckRole($_SESSION['uToken_BE']) == "moderator")
            header("location: be_panel.php");
        else
            header("location: index.php");
    } else
        header("location: index.php");
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8" />
        <title>Logowanie do panelu <?php echo BLOG_TITLE ?></title>
        <link rel="stylesheet" href="vendor/blog-engine/blog.css" />
        <script src="vendor/blog-engine/blog.js" defer></script>
    </head>
    <body class="login">
        <div class="login-page">
            <form method="POST" action="be_login.php">
                <h1>Blog Engine</h1>
                <h5 class="mb-4">Logowanie do panelu <?php echo BLOG_TITLE ?></h5>
                <label for="login">Nazwa użytkownika</label>
                <input name="login" type="text" class="form-control mb-3" id="login" />
                <label for="password">Hasło</label>
                <input name="password" type="password" class="form-control mb-3" id="password" />
                <div class="row">
                    <div class="col">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember" />
                            <label for="remember">Zapamiętaj na 7 dni</label>
                        </div>
                    </div>
                    <div class="col text-end">
                        <button type="submit" name="logmein" class="btn btn-success">Zaloguj się</button>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>
