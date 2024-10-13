<?php
    require "../../../../../_config.php";
    require "../../Authentication/AuthController.php";
    require "../PostController.php";
    
    use BlogEngine\Posts\PostController;
    use BlogEngine\Authentication\AuthController;

    session_start();

    $auth = new AuthController();
    $conn = $auth->Connection();    

    $postcon = new PostController($auth);

    if(isset($_POST['see'])){
        header("location: /post.php?id={$_POST['see']}");
    }

    if(isset($_POST['delete'])){
        if($postcon->DeletePost($_POST['delete'])){
            header("location: /be_panel.php?s=posts");
            $auth->Log("Usunięto post o ID: {$_POST['delete']}", "Information", $auth->GetID($_SESSION['uToken_BE']));
        } else
            $auth->Log("Nie udało się usunąć posta o ID: {$_POST['delete']}.", "Error", $auth->GetID($_SESSION['uToken_BE']));
    }