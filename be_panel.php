<?php
    require "_config.php";
    require "vendor/blog-engine/php/Authentication/AuthController.php";
    use BlogEngine\Authentication\AuthController;

    $auth = new AuthController();
    $conn = $auth->Connection();

    if(!isset($_COOKIE['uToken_BE']))
        header("location: be_login.php");

    $site = ($_POST['site'] ?? "home");

    $elements = array();
    $dashboard_tabs_dir = "vendor/blog-engine/php/Dashboard/Tabs";
    $dashboard_tabs = scandir($dashboard_tabs_dir);

    foreach ($dashboard_tabs as $dashboard_tab){
        if(str_contains($dashboard_tab, ".php")) {
            $file = "$dashboard_tabs_dir/$dashboard_tab";
            $name = fgets(fopen($file, 'r'));
            $name = substr($name, strlen("<!-- "), strlen($name) - (strlen("<!-- ") + strlen(" -->")));
            $filename = basename($file, ".php");

            array_push($elements, [
                "Nazwa" => $name,
                "Plik" => $filename
            ]);
        }
    }
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8" />
        <title>Panel <?php echo BLOG_TITLE ?></title>
        <link rel="stylesheet" href="vendor/blog-engine/blog.css" />
        <script src="vendor/blog-engine/blog.js" defer></script>
    </head>
    <body>
        <div class="container">
            <h1 class="mt-3">Zarządzanie <?php echo BLOG_TITLE ?></h1>
            <form method="POST" action="be_panel.php">
                <ul class="nav nav-tabs mb-5">
                    <?php
                        foreach ($elements as $element){
                            $active = ($site == $element['Plik']?" active":"");
                            echo "<li class='nav-item'><button class='nav-link$active' name='site' value='{$element['Plik']}'>{$element['Nazwa']}</button></li>";
                        }
                    ?>
                </ul>
            </form>
            <?php include "$dashboard_tabs_dir/$site.php"; ?>
        </div>
    </body>
</html>
