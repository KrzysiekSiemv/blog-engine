<?php
namespace BlogEngine\Posts {
    use BlogEngine\Authentication\AuthController;
    use Cassandra\Date;

    class PostController {
        public AuthController $auth;
        public function __construct($auth)
        {
            $this->auth = $auth;
        }

        public function CreatePost($title, $content, $name, $tags, $commentStatus, $add_time, $status){
            $srv = $this->auth->Connection();
            $date = (is_bool($add_time)?"NOW()":Date("'y-m-d H:i:s'", strtotime($add_time)));
            $status = ($status == "public"?"public":"draft");
            $id_user = $this->auth->GetID($_SESSION['uToken_BE']);

            $last_id_post = mysqli_fetch_row(mysqli_query($srv, "SELECT MAX(id_post) FROM posts;"))[0] + 1;

            $insert_post = "INSERT INTO posts VALUES($last_id_post, $id_user, '$name', '$title', '$content', '$status', '$commentStatus', 0, $date, null);";
            mysqli_query($srv, $insert_post);

            foreach ($tags as $tag){
                mysqli_query($srv, "INSERT INTO tag_to_post VALUES($tag, $last_id_post)");
            }

            $srv->close();
            header("location: ../../../be_panel.php?s=posts");
        }

        public function DraftPost($id = 0, $title = '', $content = '', $name = '', $tags = [], $commentStatus = '', $add_time = true){
            $srv = $this->auth->Connection();

            $date = (is_bool($add_time)?"NOW":Date("'Y-m-d H:i:s'", strtotime($add_time)));
            $id_user = $this->auth->GetID($_SESSION['uToken_BE']);

            if($id == 0){
                $id = mysqli_fetch_row(mysqli_query($srv, "SELECT MAX(id_post) FROM posts;"))[0] + 1;

                $insert_post = "INSERT INTO posts VALUES($id, $id_user, '$name', '$title', '$content', 'draft', '$commentStatus', 0, NOW(), null);";
                mysqli_query($srv, $insert_post);

                foreach ($tags as $tag){
                    mysqli_query($srv, "INSERT INTO tag_to_post VALUES($tag, $id)");
                }
            } else {
                $update_post = "UPDATE posts SET id_post = $id, name = '$name', title = '$title', content = '$content', status = 'draft', comments = '$commentStatus' WHERE id_post = $id;";
                mysqli_query($srv, $update_post);
                mysqli_query($srv, "DELETE FROM tag_to_post WHERE id_post = $id");

                foreach ($tags as $tag){
                    mysqli_query($srv, "INSERT INTO tag_to_post VALUES($tag, $id)");
                }
            }

            $srv->close();
            header("location: ../../../be_panel.php?s=posts");
        }

        public function UpdatePost($id, $title, $content, $name, $tags, $commentStatus){
            $srv = $this->auth->Connection();

            $date = (is_bool($add_time)?"NOW":Date("'Y-m-d H:i:s'", strtotime($add_time)));
            $id_user = $this->auth->GetID($_SESSION['uToken_BE']);

            $update_post = "UPDATE posts SET id_post = $id, name = '$name', title = '$title', content = '$content', status = 'public', comments = '$commentStatus', updated_at = NOW() WHERE id_post = $id;";
            mysqli_query($srv, $update_post);
            mysqli_query($srv, "DELETE FROM tag_to_post WHERE id_post = $id");

            foreach ($tags as $tag){
                mysqli_query($srv, "INSERT INTO tag_to_post VALUES($tag, $id)");
            }

            $srv->close();
            header("location: ../../../be_panel.php?s=posts");
        }

        public function DeletePost($id){
            $srv = $this->auth->Connection();
            mysqli_query($srv, "DELETE FROM tag_to_post WHERE id_post = $id");
            mysqli_query($srv, "DELETE FROM posts WHERE id_post = $id");
            $srv->close();
            header("location: ../../../be_panel.php?s=posts");
        }

        public function AutoSave($id, $name, $title, $content, $tags = null, $commentStatus = "open") : bool {
            $srv = $this->auth->Connection();
            $response = false;
            $id_user = $this->auth->GetID($_SESSION['uToken_BE']);

            $post = "";
            if($id == null || $id == 0){
                $post = "INSERT INTO posts VALUES(null, $id_user, '$name', '$title', '$content', 'draft', '$commentStatus', 0, NOW(), null)";
            } else {
                $post = "UPDATE posts SET name = '$name', title = '$title', content = '$content', status = 'draft', comments = '$commentStatus' WHERE id_post = $id";
            }

            if(mysqli_query($srv, $post))
                $response = true;

            $srv->close();

            return $response;
        }

        public function DownloadPost($year, $month, $day, $name) : array {
            $srv = $this->auth->Connection();
            $post = array();

            $get_post = "SELECT users.display_name AS autor, posts.title AS tytul, posts.content AS tresc, posts.views AS wyswietlenia, posts.added_at AS dodano, (SELECT GROUP_CONCAT(CONCAT(comments.author, ";;", comments.content, ";;", comments.added_at)) FROM comments WHERE comments.id_post = posts.id_post GROUP BY comments.id_comment) AS komentarze FROM posts, users WHERE users.id_user = posts.id_user AND YEAR(added_at) = '$year' AND MONTH(added_at) = '$month' AND DAY(added_at) = '$day' AND posts.name = '$name';";
            $res_post = mysqli_query($srv, $get_post);

            if($row = mysqli_fetch_row($res_post)){
                array_push($post, [
                    "Autor" => $row[0],
                    "Tytul" => $row[1],
                    "Tresc" => $row[2],
                    "Wyswietlenia" => $row[3],
                    "Dodano" => $row[4],
                    "Komentarze" => array()
                ]);

                $comments = mb_split("||||", $row[5]);

                foreach($comments as $comment){
                    $data = mb_split(";;", $comment);
                    array_push($post[0]["Komentarze"], [
                       "Autor" => $data[0],
                        "Tresc" => $data[1],
                        "Dodano" => $data[2]
                    ]);
                }
            }

            return $post;
        }
 
        public function DownloadSinglePost($id) {
            $srv = $this->auth->Connection();
            $post = [];

            $get_post = "SELECT users.display_name AS autor, posts.title AS tytul, posts.content AS tresc, posts.views AS wyswietlenia, posts.added_at AS dodano, (SELECT GROUP_CONCAT(CONCAT(comments.author, ';;', comments.content, ';;', comments.added_at)) FROM comments WHERE comments.id_post = posts.id_post GROUP BY comments.id_comment) AS komentarze FROM posts, users WHERE users.id_user = posts.id_user AND id_post ={$id};";
            $res_post = mysqli_query($srv, $get_post);

            if($row = mysqli_fetch_row($res_post)){
                $post = [
                    "author" => $row[0],
                    "title" => $row[1],
                    "content" => $row[2],
                    "views" => $row[3],
                    "added_at" => $row[4],
                    "comments" => array()
                ];

                if($row[5] != ""){
                    $comments = mb_split("||||", $row[5]);

                    foreach($comments as $comment){
                        $data = mb_split(";;", $comment);
                        array_push($post[0]["Komentarze"], [
                        "Autor" => $data[0],
                            "Tresc" => $data[1],
                            "Dodano" => $data[2]
                        ]);
                    }
                }
            }
            
            $this->auth->Close($srv);
            return $post;
        }

        public function ConvertToSite($data, $site) : string{
            require "vendor/blog-engine/php/Dictionary.php";
            $dictionary = new \BlogEngine\Dictionary;
            
            foreach(array_keys($dictionary->BlogTags()) as $tag){
                $site = str_replace($tag, $data[$dictionary->BlogTags()[$tag]], $site);
            }

            return $site;
        }
    }

}