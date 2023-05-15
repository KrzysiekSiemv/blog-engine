<?php
    require "../../../../_config.php";
    require "../Authentication/AuthController.php";
    use BlogEngine\Authentication\AuthController;

    $auth = new AuthController();
    $conn = $auth->Connection();

    if(isset($_POST['title']) && isset($_POST['content']) && isset($_POST['name'])){

    }

    $conn->close();