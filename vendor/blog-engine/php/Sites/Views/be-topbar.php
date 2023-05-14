<?php
    $display_name = "";

    $conn = $auth->Connection();
    $res = mysqli_query($conn, "SELECT display_name FROM users WHERE login_token = '{$_SESSION['uToken_BE']}';") ;
    while($row = mysqli_fetch_row($res)){
        $display_name = $row[0];
    }
?>
<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <div class="container-fluid">
        <a href="#" class="navbar-brand">Witaj, <?php echo $display_name ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="be_panel.php">Wejdź do panelu</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Dodaj nowy wpis</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Moderuj komentarze</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Zobacz statystyki</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Wyloguj z panelu</a> </li>
            </ul>
        </div>
    </div>
</nav>