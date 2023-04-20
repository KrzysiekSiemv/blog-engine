<?php
    if(isset($_GET['s']) && isset($_GET['u']) && isset($_GET['p']) && isset($_GET['d'])){
        $conn = mysqli_connect($_GET['s'], $_GET['u'], $_GET['p'], $_GET['d']);
        if($conn){
            echo "Nawiązano połączenie z bazą danych!";
            mysqli_close($conn);
        } else {
            echo "Połączenie z bazą nie przeszło pomyślnie: " . mysqli_connect_errno() . ": " . mysqli_connect_error();
        }
    }
