<?php
    require "../_config.php";
    require "../vendor/blog-engine/php/Authentication/AuthController.php";
    require "../vendor/blog-engine/php/Posts/PostController.php";

    use BlogEngine\Posts\PostController;
    use BlogEngine\Authentication\AuthController;

    session_start();

    if(!isset($_SESSION['uToken_BE']))
        header("location: ../be_login.php?goto=be_panel.php?s=posts");

    $auth = new AuthController();
    $conn = $auth->Connection();

    $postcon = new PostController($auth);

    $post = null;
    $tags = [];
    $now = Date("Y-m-d H:i", strtotime("now"));
    if(isset($_POST['edit'])){
        $get_post = "SELECT id_post, name, title, content, comments, added_at, updated_at, CASE WHEN status = 'public' THEN '<b>Publiczny</b>' WHEN status = 'archive' THEN '<i>Schowany</i>' ELSE '<i>Wersja robocza</i>' END AS widocznosc FROM posts WHERE id_post = {$_POST['edit']}";
        $res_post = mysqli_fetch_row(mysqli_query($conn, $get_post));

        $post = [
            "ID" => $res_post[0],
            "Nazwa" => $res_post[1],
            "Tytuł" => $res_post[2],
            "Treść" => $res_post[3],
            "Komentarze" => $res_post[4],
            "Dodano" => $res_post[5],
            "Zaktualizowano" => $res_post[6],
            "Widocznosc" => $res_post[7]
        ];

        $get_tags = "SELECT id_tag, show_name, slug, CASE WHEN (SELECT 1 FROM tag_to_post WHERE id_post = {$post['ID']} and tag_to_post.id_tag = tags.id_tag) = 1 THEN TRUE ELSE FALSE END AS selected FROM tags GROUP BY id_tag;";
        $res_tags = mysqli_query($conn, $get_tags);
        while($row = mysqli_fetch_assoc($res_tags)){
            array_push($tags, [
                "ID" => $row['id_tag'],
                "Nazwa" => $row['show_name'],
                "Kod" => $row['slug'],
                "Aktywne" => $row['selected']
            ]);
        }
    } else {
        $get_tags = "SELECT id_tag, show_name, slug FROM tags;";
        $res_tags = mysqli_query($conn, $get_tags);
        while($row = mysqli_fetch_assoc($res_tags)){
            array_push($tags, [
                "ID" => $row['id_tag'],
                "Nazwa" => $row['show_name'],
                "Kod" => $row['slug'],
                "Aktywne" => false
            ]);
        }
    }

    if(isset($_POST['add'])){
        $postcon->CreatePost($_POST['title'], $_POST['content'], $_POST['name'], $_POST['tags'], $_POST['comments_status'], (isset($_POST['now_time'])?true:$_POST['add_time']), "public");
    }
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8" />
        <title>Panel <?php echo BLOG_TITLE ?></title>
        <link rel="stylesheet" href="../vendor/blog-engine/blog.css" />
        <link rel="stylesheet" href="../vendor/summernote/summernote-lite.min.css" />

        <script src="../vendor/blog-engine/blog.js" defer></script>
        <script src="../vendor/blog-engine/js/jquery-3.7.0.min.js"></script>
        <script src="../vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../vendor/summernote/summernote-lite.min.js" defer></script>

    </head>
    <body>
        <div class="container pt-3">
            <h2 class="mb-5"><button class="btn btn-primary" onclick="location.href='../be_panel.php?s=posts'">Wróć</button> <?php echo ($post != null?"Modyfikowanie posta: '{$post['Tytuł']}'":"Dodawanie nowego posta") ?></h2>
            <form method="POST" action="be_post.php">
                <div class="row mb-3">
                    <div class="col-md-8 d-grid">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="title" name="title" placeholder="Tytuł posta" <?php if($post != null) echo "value='{$post['Tytuł']}'" ?>>
                            <label for="title">Tytuł posta</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex btn btn-group">
                        <?php echo ($post != null?"<button type='submit' class='btn btn-success' name='update' value='{$post['ID']}'>Zaktualizuj</button>":"<button type='submit' class='btn btn-success' name='add'>Dodaj</button>") ?>
                        <button type="submit" class="btn btn-primary" name="draft">Zapisz jako wersja robocza</button>
                        <button type="submit" class="btn btn-danger" name="delete">Usuń</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <input type="hidden" id="content" name="content" value="<?php if($post != null) echo $post['Treść'] ?>" />
                        <div id="summernote"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Ustawienia posta</h4>
                                <div class='form-check mb-3'><input type='checkbox' id="autosave" class='form-check-input' checked><label for='autosave'>Automatyczne zapisywanie posta do wersji roboczej co 1 minutę</label> </div>
                                <h5>Status posta</h5>
                                <p><?php echo ($post != null?$post['Widocznosc']:"W trakcie tworzenia") ?></p>
                                <h5>Kto może publikować komentarze</h5>
                                <select name="comments_status" class="form-select mb-3">
                                    <option value="open" <?php if($post != null && $post['Komentarze'] == 'open') echo "selected" ?>>Każdy</option>
                                    <option value="registered" <?php if($post != null && $post['Komentarze'] == 'registered') echo "selected" ?>>Zalogowani</option>
                                    <option value="closed" <?php if($post != null && $post['Komentarze'] == 'closed') echo "selected" ?>>Nikt</option>
                                </select>
                                <h5>Nazwa linku</h5>
                                <input type="text" name="name" id="name" value="<?php if($post != null) echo $post['Nazwa'] ?>" class="form-control mb-3" pattern="^[a-zA-Z0-9_-]*$"/>
                                <h5>Tagi posta</h5>
                                <div id="tags" class="d-flex">
                                    <?php
                                        foreach ($tags as $tag){
                                            $checked = ($tag['Aktywne']?" checked":"");
                                            echo "
                                                <div class='form-check mb-3'>
                                                    <input type='checkbox' class='form-check-input' name='tags[]' id='tag{$tag['ID']}' value='{$tag['ID']}'$checked>
                                                    <label for='tag{$tag['ID']}'>{$tag['Nazwa']} ({$tag['Kod']})</label>
                                                </div>
                                            ";
                                        }
                                    ?>
                                </div>
                                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#newTag">Utwórz nowy tag</button>
                                <h5>Data dodania</h5>
                                <?php
                                    echo ($post != null?
                                        "<p class='mb-3'>" . date("d M Y H:i:s", strtotime($post['Dodano'])) . "</p>
                                        ":
                                        "<input type='datetime-local' class='form-control mb-1' name='add_date' value='$now'>
                                            <div class='form-check'>
                                                <input type='checkbox' name='now_time' class='form-check-input' checked>
                                                <label for='now_time'>Godzina dodania równa godzinie naciśnięcia przycisku</label> 
                                            </div>");
                                ?>

                                <div class="modal fade" id="newTag" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Tworzenie nowego tagu</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col">
                                                        <h6>Nazwa wyświetlana taga</h6>
                                                        <input type="text" id="show_name" class="form-control mb-3" placeholder="Typowe Posty" />
                                                    </div>
                                                    <div class="col">
                                                        <h6>Nazwa kodowa tagu</h6>
                                                        <input type="text" id="slug" class="form-control mb-3" pattern="^[a-zA-Z0-9_-]*$" placeholder="typowe_posty" />
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="addtag">Utwórz tag</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </body>
    <script>
        let title = document.getElementById('title');
        let insert = document.getElementById('content');
        $(document).ready(function(e){
            $('#summernote').summernote({
                height: 600,
                minHeight: null,
                maxHeight: null,
                focus: true,
                value: "no"
            }).summernote('pasteHTML', '<?php if($post != null) echo $post['Treść'] ?>');
        });

        $('#summernote').on('summernote.keyup', function(we, e) {
            insert.value = $('#summernote').summernote('code');
        });

        document.getElementById('addtag').addEventListener('click', function (e){
            createTag(document.getElementById('show_name').value, document.getElementById('slug').value, document.getElementById('tags'));
        }, true);

        title.addEventListener('keyup', function (e){
            if(title.value.length < 60)
                document.getElementById('name').value = title.value.toLowerCase().replace(" ", "_");
        }, true);
    </script>
</html>
