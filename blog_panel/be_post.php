<?php
    require "../_config.php";
    require "../vendor/blog-engine/php/Authentication/AuthController.php";
    use BlogEngine\Authentication\AuthController;

    $auth = new AuthController();
    $conn = $auth->Connection();

    $post = null;
    if(isset($_POST['edit'])){
        $get_post = "SELECT name, title, content, status, comments, added_at, updated_at FROM posts WHERE id_post = {$_POST['edit']}";
        $res_post = mysqli_fetch_row(mysqli_query($conn, $get_post));

        $post = [
            "Nazwa" => $res_post[0],
            "Tytuł" => $res_post[1],
            "Treść" => $res_post[2],
            "Status" => $res_post[3],
            "Komentarze" => $res_post[4],
            "Dodano" => $res_post[5],
            "Zaktualizowano" => $res_post[6]
        ];
    }
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8" />
        <title>Panel <?php echo BLOG_TITLE ?></title>
        <link rel="stylesheet" href="../vendor/blog-engine/blog.css" />
        <script src="../vendor/blog-engine/blog.js" defer></script>
    </head>
    <body>
        <div class="container pt-3">
            <h2 class="mb-5">Dodawanie posta</h2>
            <form method="POST" action="be_post.php">
                <div class="row mb-3">
                    <div class="col-md-8 d-grid">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="title" name="title" placeholder="Tytuł posta" <?php if($post != null) echo "value='{$post['Tytuł']}'" ?>>
                            <label for="title">Tytuł posta</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-grid">
                        <button type="submit" class="btn btn-lg btn-success" name="add">Dodaj posta</button>
                    </div>
                </div>
                <div class="row">
                    <button type="button" id="bold_button" onclick="insertAtCursor(document.getElementById('editor'), '****', true)">Bold</button>
                    <button type="button" id="bold_button" onclick="insertAtCursor(document.getElementById('editor'), '**', true)">Italic</button>
                    <button type="button" id="bold_button" onclick="insertAtCursor(document.getElementById('editor'), '#######', true)">Header</button>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card p-3" contenteditable="true" id="editor" style="min-height: 100vh">
                            <?php if($post != null) echo $post['Treść'] ?>
                        </div>
                    </div>
                    <div class="col-md-4">

                    </div>
                </div>
            </form>
        </div>
    </body>
</html>
