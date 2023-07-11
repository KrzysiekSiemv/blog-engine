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

    if(isset($_POST['name']) && isset($_POST['title']) && isset($_POST['content'])){
        $id = $_POST['id'] ?? 0;
        if($postcon->AutoSave($id, $_POST['name'], $_POST['title'], $_POST['content'], (isset($_POST['tags'])?$_POST['tags']:null), $_POST['commentStatus']))
            echo "Zapisano zmiany!";
        else
            echo "Błąd w zapisie! Spróbuj ponownie później";
    }