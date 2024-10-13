<!-- Posty -->
<?php
    $posts = array();

    // Pobierz posty
    $get_posts_query = "SELECT id_post, name, title, content, views, added_at, updated_at, CASE WHEN status = 'public' THEN '<b>Publiczny</b>' WHEN status = 'archive' THEN '<i>Schowany</i>' ELSE '<i>Wersja robocza</i>' END AS widocznosc FROM posts";
    $get_posts_res = mysqli_query($conn, $get_posts_query);

    while($row = mysqli_fetch_assoc($get_posts_res)){
        array_push($posts, [
            "ID" => $row['id_post'],
            "Nazwa" => $row['name'],
            "Tytuł" => $row['title'],
            "Treść" => $row['content'], 0, 120,
            "Wyswietlenia" => $row['views'],
            "Dodano" => $row['added_at'],
            "Widoczność" => $row['widocznosc']
        ]);
    }
?>

<button type="button" name="add_post" class="btn btn-success mb-3" onclick="location.href='blog_panel/be_post.php';">Dodaj nowy post</button>
<table class="table table-stripped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tytuł</th>
            <th>Zawartość</th>
            <th>Wyświetlenia</th>
            <th>Widoczność</th>
            <th>Dodano</th>
            <th>Akcja</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($posts as $post){
                if(strlen($post['Treść']) > 256){
                    $post['Treść'] = $auth->ToHTML(substr($post['Treść'], 0, 253) . "...");
                }
                echo "
                <tr>
                    <th>{$post['Nazwa']}</th>
                    <td>{$post['Tytuł']}</td>
                    <td>{$post['Treść']}</td>
                    <td>{$post['Wyswietlenia']}</td>
                    <td>{$post['Widoczność']}</td>
                    <td>{$post['Dodano']}</td>
                    <td>
                        <div class='d-flex'>
                            <form method='POST' action='blog_panel/be_post.php'>
                                <button class='btn btn-primary ms-2' type='submit' name='edit' value='{$post['ID']}'>Edytuj</button>
                            </form>
                            <form method='POST' action='vendor/blog-engine/php/Posts/Calls/PostManagement.php'>
                                <div class='d-flex'>
                                    <button class='btn btn-primary ms-2' type='submit' name='see' value='{$post['ID']}'>Zobacz</button>
                                    <button class='btn btn-danger ms-2' type='submit' name='delete' value='{$post['ID']}'>Usuń</button>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
                ";
            }
        ?>
    </tbody>
</table>
