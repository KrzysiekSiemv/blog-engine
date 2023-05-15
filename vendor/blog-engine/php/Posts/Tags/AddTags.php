<?php
    require "../../../../../_config.php";
    require "../../Authentication/AuthController.php";
    use BlogEngine\Authentication\AuthController;

    $auth = new AuthController();
    $conn = $auth->Connection();

    if(isset($_POST['show_name']) && isset($_POST['slug'])){
        $check_if_exists = mysqli_query($conn, "SELECT id_tag FROM tags WHERE show_name = '{$_POST['show_name']}' AND slug = '{$_POST['slug']}'");
        if(mysqli_num_rows($check_if_exists) == 0){
            // Add a tag
            mysqli_query($conn, "INSERT INTO tags VALUES(NULL, '{$_POST['show_name']}', '{$_POST['slug']}')");

            // Podaj ID Taga
            $get_tag = mysqli_query($conn, "SELECT * FROM tags WHERE show_name = '{$_POST['show_name']}' AND slug = '{$_POST['slug']}'");
            if($row = mysqli_fetch_row($get_tag)){
                $value = [
                    "id" => $row[0],
                    "show_name" => $row[1],
                    "slug" => $row[2]
                ];
                echo json_encode($value);
            }
        } else {
            echo mysqli_fetch_row($check_if_exists)[0];
        }
    }

    $conn->close();